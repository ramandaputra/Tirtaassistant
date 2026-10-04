<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\ChatSession;
use App\Services\GeminiService;
use App\Services\VectorSearch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class WidgetController extends Controller
{
    /**
     * Start a new chat session for the widget.
     */
    public function startSession(): JsonResponse
    {
        $sessionId = Str::uuid()->toString();

        $session = ChatSession::create([
            'session_id' => $sessionId,
            'title' => 'Widget Chat',
        ]);

        // Create initial greeting
        $greeting = ChatMessage::create([
            'chat_session_id' => $session->id,
            'role' => 'assistant',
            'content' => 'Halo! 👋 Saya TirtAssistant, asisten virtual dari Perumda Air Minum Tirta Kepri. Ada yang bisa saya bantu?',
        ]);

        return response()->json([
            'session_id' => $sessionId,
            'greeting' => [
                'role' => 'assistant',
                'content' => $greeting->content,
                'time' => $greeting->created_at->format('H:i'),
            ],
        ]);
    }

    /**
     * Send a message and receive an AI response.
     */
    public function sendMessage(Request $request, GeminiService $gemini, VectorSearch $vectorSearch): JsonResponse
    {
        $validated = $request->validate([
            'session_id' => ['required', 'string', 'uuid'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $session = ChatSession::where('session_id', $validated['session_id'])->first();

        if (! $session) {
            return response()->json(['error' => 'Session not found.'], 404);
        }

        // Save user message
        $userMsg = ChatMessage::create([
            'chat_session_id' => $session->id,
            'role' => 'user',
            'content' => $validated['message'],
        ]);

        try {
            $searchResults = $vectorSearch->search($validated['message']);
            $sources = $vectorSearch->getSources($searchResults);

            if (empty($searchResults)) {
                $response = 'Maaf, saya tidak menemukan jawaban untuk pertanyaan tersebut di knowledge base yang tersedia.';
            } else {
                $context = $vectorSearch->buildContext($searchResults);

                // Build conversation history from previous messages
                $history = $session->messages()
                    ->where('id', '<', $userMsg->id)
                    ->orderBy('id')
                    ->get()
                    ->map(fn (ChatMessage $msg) => [
                        'role' => $msg->role === 'user' ? 'user' : 'model',
                        'parts' => [['text' => $msg->content]],
                    ])
                    ->toArray();

                $response = $gemini->chat($validated['message'], $context, $history);
            }
        } catch (Throwable $exception) {
            Log::error('Widget chat error.', ['exception' => $exception->getMessage()]);
            $response = 'Maaf, terjadi kendala saat memproses pertanyaan Anda. Silakan coba lagi.';
            $sources = [];
        }

        // Save AI response
        $aiMsg = ChatMessage::create([
            'chat_session_id' => $session->id,
            'role' => 'assistant',
            'content' => $response,
            'sources' => $sources,
        ]);

        return response()->json([
            'reply' => [
                'role' => 'assistant',
                'content' => $aiMsg->content,
                'sources' => $aiMsg->sources ?? [],
                'time' => $aiMsg->created_at->format('H:i'),
            ],
        ]);
    }
}
