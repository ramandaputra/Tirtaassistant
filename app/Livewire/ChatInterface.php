<?php

namespace App\Livewire;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Services\GeminiService;
use App\Services\VectorSearch;
use Illuminate\Support\Str;
use Livewire\Component;

class ChatInterface extends Component
{
    public $sessionId;
    public $messages = [];
    public $userMessage = '';
    public $isTyping = false;

    public function mount()
    {
        $this->createNewSession();
    }

    public function createNewSession()
    {
        $this->sessionId = Str::uuid()->toString();
        
        $session = ChatSession::create([
            'session_id' => $this->sessionId,
            'title' => 'New Chat',
        ]);
        
        $this->messages = [];
        
        // Initial greeting
        $greeting = ChatMessage::create([
            'chat_session_id' => $session->id,
            'role' => 'assistant',
            'content' => 'Halo! Saya adalah AI Assistant. Ada yang bisa saya bantu terkait dokumen di Knowledge Base?',
        ]);
        
        $this->messages[] = $greeting->toArray();
    }

    public function sendMessage(GeminiService $gemini, VectorSearch $vectorSearch)
    {
        if (empty(trim($this->userMessage))) {
            return;
        }

        $session = ChatSession::where('session_id', $this->sessionId)->first();

        // 1. Save user message
        $userMsg = ChatMessage::create([
            'chat_session_id' => $session->id,
            'role' => 'user',
            'content' => $this->userMessage,
        ]);
        $this->messages[] = $userMsg->toArray();
        $messageText = $this->userMessage;
        $this->userMessage = '';
        $this->isTyping = true;

        // 2. Perform RAG vector search
        $searchResults = $vectorSearch->search($messageText);
        $context = $vectorSearch->buildContext($searchResults);
        $sources = $vectorSearch->getSources($searchResults);

        // 3. Prepare history
        $history = collect($this->messages)
            ->where('id', '!=', $userMsg->id)
            ->map(fn($msg) => [
                'role' => $msg['role'] === 'user' ? 'user' : 'model',
                'parts' => [['text' => $msg['content']]]
            ])
            ->toArray();

        // 4. Generate AI response
        $response = $gemini->chat($messageText, $context, $history);

        // 5. Save AI response
        $aiMsg = ChatMessage::create([
            'chat_session_id' => $session->id,
            'role' => 'assistant',
            'content' => $response,
            'sources' => $sources,
        ]);
        
        $this->messages[] = $aiMsg->toArray();
        $this->isTyping = false;
    }

    public function render()
    {
        return view('livewire.chat-interface');
    }
}
