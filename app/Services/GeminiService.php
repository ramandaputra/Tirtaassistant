<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service for Google Gemini API (Embeddings + Chat Completion)
 */
class GeminiService
{
    private string $apiKey;
    private string $embeddingModel;
    private string $chatModel;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', '');
        $this->embeddingModel = config('services.gemini.embedding_model', 'gemini-embedding-exp-03-07');
        $this->chatModel = config('services.gemini.chat_model', 'gemini-2.0-flash');
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta';
    }

    /**
     * Generate embedding vector for a given text
     *
     * @param  string $text
     * @return array<float>|null
     */
    public function generateEmbedding(string $text): ?array
    {
        try {
            $response = Http::timeout(30)->post(
                "{$this->baseUrl}/models/{$this->embeddingModel}:embedContent?key={$this->apiKey}",
                [
                    'model'   => "models/{$this->embeddingModel}",
                    'content' => [
                        'parts' => [['text' => $text]],
                    ],
                    'taskType' => 'RETRIEVAL_DOCUMENT',
                ]
            );

            if ($response->failed()) {
                Log::error('Gemini embedding error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();
            return $data['embedding']['values'] ?? null;
        } catch (\Exception $e) {
            Log::error('Gemini embedding exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Generate embedding for a query (different task type for retrieval)
     *
     * @param  string $query
     * @return array<float>|null
     */
    public function generateQueryEmbedding(string $query): ?array
    {
        try {
            $response = Http::timeout(30)->post(
                "{$this->baseUrl}/models/{$this->embeddingModel}:embedContent?key={$this->apiKey}",
                [
                    'model'   => "models/{$this->embeddingModel}",
                    'content' => [
                        'parts' => [['text' => $query]],
                    ],
                    'taskType' => 'RETRIEVAL_QUERY',
                ]
            );

            if ($response->failed()) {
                Log::error('Gemini query embedding error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();
            return $data['embedding']['values'] ?? null;
        } catch (\Exception $e) {
            Log::error('Gemini query embedding exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Generate a chat completion using Gemini with RAG context
     *
     * @param  string $userMessage
     * @param  string $context     Retrieved context from knowledge base
     * @param  array  $history     Previous messages [['role' => 'user'|'model', 'parts' => [['text' => '...']]]
     * @return string|null
     */
    public function chat(string $userMessage, string $context = '', array $history = []): ?string
    {
        try {
            $systemInstruction = "Anda adalah asisten AI yang cerdas dan membantu. " .
                "Jawablah pertanyaan pengguna HANYA berdasarkan konteks dokumen yang diberikan. " .
                "Jika informasi tidak ada dalam konteks, katakan dengan jujur bahwa Anda tidak menemukan " .
                "informasi tersebut dalam dokumen yang tersedia. " .
                "Gunakan Bahasa Indonesia yang baik dan jelas. " .
                "Berikan jawaban yang terstruktur dan mudah dipahami.";

            $userContent = $context
                ? "Konteks dari dokumen:\n\n{$context}\n\n---\n\nPertanyaan: {$userMessage}"
                : $userMessage;

            $contents = array_merge($history, [
                [
                    'role'  => 'user',
                    'parts' => [['text' => $userContent]],
                ],
            ]);

            $payload = [
                'contents'          => $contents,
                'systemInstruction' => [
                    'parts' => [['text' => $systemInstruction]],
                ],
                'generationConfig' => [
                    'temperature'     => 0.7,
                    'topK'            => 40,
                    'topP'            => 0.95,
                    'maxOutputTokens' => 2048,
                ],
            ];

            $response = Http::timeout(60)->post(
                "{$this->baseUrl}/models/{$this->chatModel}:generateContent?key={$this->apiKey}",
                $payload
            );

            if ($response->failed()) {
                Log::error('Gemini chat error', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return 'Maaf, terjadi kesalahan saat menghubungi AI. Silakan coba lagi.';
            }

            $data = $response->json();
            return $data['candidates'][0]['content']['parts'][0]['text']
                ?? 'Maaf, tidak ada respons dari AI.';
        } catch (\Exception $e) {
            Log::error('Gemini chat exception', ['error' => $e->getMessage()]);
            return 'Maaf, terjadi kesalahan: ' . $e->getMessage();
        }
    }
}
