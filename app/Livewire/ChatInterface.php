<?php

namespace App\Livewire;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Services\GeminiService;
use App\Services\VectorSearch;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;
use Throwable;

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
            'content' => 'Halo! Saya adalah TirtAssistant, AI Pembantu dari PERUMDA TIRTA KEPRI. Ada yang bisa saya bantu terkait?',
        ]);

        $this->messages[] = $greeting->toArray();
    }

    public function sendMessage(GeminiService $gemini, VectorSearch $vectorSearch): void
    {
        $this->validate(['userMessage' => ['required', 'string', 'max:2000']]);

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

        try {
            $this->isTyping = true;
            $searchResults = $vectorSearch->search($messageText);
            $sources = $vectorSearch->getSources($searchResults);

            if (empty($searchResults)) {
                $response = 'Maaf, saya tidak menemukan jawaban untuk pertanyaan tersebut di knowledge base yang tersedia.';
            } else {
                $context = $vectorSearch->buildContext($searchResults);
                $history = collect($this->messages)
                    ->where('id', '!=', $userMsg->id)
                    ->map(fn ($message) => [
                        'role' => $message['role'] === 'user' ? 'user' : 'model',
                        'parts' => [['text' => $message['content']]],
                    ])
                    ->toArray();
                $response = $gemini->chat($messageText, $context, $history);
            }
        } catch (Throwable $exception) {
            Log::error('Chat message could not be processed.', ['exception' => $exception->getMessage()]);
            $response = 'Maaf, terjadi kendala saat memproses pertanyaan Anda. Silakan coba lagi.';
            $sources = [];
        } finally {
            $this->isTyping = false;
        }

        $aiMsg = ChatMessage::create([
            'chat_session_id' => $session->id,
            'role' => 'assistant',
            'content' => $response,
            'sources' => $sources,
        ]);

        $this->messages[] = $aiMsg->toArray();
    }

    public function sendQuickMessage(string $message, GeminiService $gemini, VectorSearch $vectorSearch): void
    {
        $this->userMessage = $message;
        $this->sendMessage($gemini, $vectorSearch);
    }

    public function render()
    {
        return view('livewire.chat-interface');
    }
}
