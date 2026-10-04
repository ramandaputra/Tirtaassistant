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
        $this->embeddingModel = config('services.gemini.embedding_model', 'gemini-embedding-001');
        $this->chatModel = config('services.gemini.chat_model', 'gemini-3.5-flash');
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta';
    }

    /**
     * Generate embedding vector for a given text
     *
     * @return array<float>|null
     */
    public function generateEmbedding(string $text): ?array
    {
        try {
            $response = Http::timeout(30)->post(
                "{$this->baseUrl}/models/{$this->embeddingModel}:embedContent?key={$this->apiKey}",
                [
                    'model' => "models/{$this->embeddingModel}",
                    'content' => [
                        'parts' => [['text' => $text]],
                    ],
                    'taskType' => 'RETRIEVAL_DOCUMENT',
                ]
            );

            if ($response->failed()) {
                Log::error('Gemini embedding error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
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
     * @return array<float>|null
     */
    public function generateQueryEmbedding(string $query): ?array
    {
        try {
            $response = Http::timeout(30)->post(
                "{$this->baseUrl}/models/{$this->embeddingModel}:embedContent?key={$this->apiKey}",
                [
                    'model' => "models/{$this->embeddingModel}",
                    'content' => [
                        'parts' => [['text' => $query]],
                    ],
                    'taskType' => 'RETRIEVAL_QUERY',
                ]
            );

            if ($response->failed()) {
                Log::error('Gemini query embedding error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
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
     * @param  string  $context  Retrieved context from knowledge base
     * @param  array  $history  Previous messages [['role' => 'user'|'model', 'parts' => [['text' => '...']]]
     */
    public function chat(string $userMessage, string $context = '', array $history = []): ?string
    {
        $systemInstruction = 'Anda adalah asisten AI yang cerdas dan membantu. '.
            'Jawablah pertanyaan pengguna HANYA berdasarkan konteks dokumen yang diberikan. '.
            'Jika informasi tidak ada dalam konteks, katakan dengan jujur bahwa Anda tidak menemukan '.
            'informasi tersebut dalam dokumen yang tersedia. '.
            'Gunakan Bahasa Indonesia yang baik dan jelas. '.
            'Berikan jawaban yang terstruktur dan mudah dipahami.';

        $userContent = $context
            ? "Konteks dari dokumen:\n\n{$context}\n\n---\n\nPertanyaan: {$userMessage}"
            : $userMessage;

        $contents = array_merge($history, [
            [
                'role' => 'user',
                'parts' => [['text' => $userContent]],
            ],
        ]);

        $payload = [
            'contents' => $contents,
            'systemInstruction' => [
                'parts' => [['text' => $systemInstruction]],
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 2048,
            ],
        ];

        // Candidate models in fallback order
        $candidateModels = array_unique(array_filter([
            $this->chatModel,
            'gemini-3.5-flash',
            'gemini-3.5-flash-lite',
            'gemini-flash-latest',
        ]));

        foreach ($candidateModels as $model) {
            try {
                $response = Http::timeout(45)->post(
                    "{$this->baseUrl}/models/{$model}:generateContent?key={$this->apiKey}",
                    $payload
                );

                if ($response->successful()) {
                    $data = $response->json();
                    $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    if ($text) {
                        return $text;
                    }
                }

                Log::warning("Gemini chat model [{$model}] failed (HTTP {$response->status()}), trying fallback model.", [
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 200),
                ]);
            } catch (\Exception $e) {
                Log::warning("Gemini chat exception on [{$model}]: ".$e->getMessage());
            }
        }

        return 'Maaf, terjadi kesalahan saat menghubungi AI. Silakan coba lagi.';
    }
}
