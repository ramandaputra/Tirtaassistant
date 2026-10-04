<?php

namespace App\Services;

use App\Models\DocumentChunk;
use Illuminate\Support\Str;

/**
 * Performs cosine similarity search against stored embeddings
 */
class VectorSearch
{
    private GeminiService $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    /**
     * Search for the most relevant chunks given a query
     *
     * @param  int  $topK  Number of results to return
     * @param  float  $minScore  Minimum similarity score (0-1)
     * @return array<array{chunk: DocumentChunk, score: float}>
     */
    public function search(string $query, int $topK = 5, float $minScore = 0.3): array
    {
        // Generate embedding for the query
        $queryEmbedding = $this->gemini->generateQueryEmbedding($query);

        if (! $queryEmbedding) {
            return [];
        }

        // Load all chunks that have embeddings (from ready documents)
        $chunks = DocumentChunk::whereNotNull('embedding')
            ->whereHas('document', fn ($q) => $q->where('status', 'ready'))
            ->get();

        if ($chunks->isEmpty()) {
            return [];
        }

        // Compute cosine similarity for each chunk
        $results = [];
        foreach ($chunks as $chunk) {
            $chunkEmbedding = $chunk->embedding;
            if (empty($chunkEmbedding)) {
                continue;
            }

            $score = $this->cosineSimilarity($queryEmbedding, $chunkEmbedding);

            if ($score >= $minScore) {
                $results[] = [
                    'chunk' => $chunk,
                    'score' => $score,
                ];
            }
        }

        // Sort by similarity descending
        usort($results, fn ($a, $b) => $b['score'] <=> $a['score']);

        // Return top K results
        return array_slice($results, 0, $topK);
    }

    /**
     * Build context string from search results
     *
     * @param  array  $results  Output from search()
     */
    public function buildContext(array $results): string
    {
        if (empty($results)) {
            return '';
        }

        $contextParts = [];
        foreach ($results as $index => $result) {
            $chunk = $result['chunk'];
            $docName = $chunk->document->original_name ?? 'Dokumen';
            $score = round($result['score'] * 100, 1);

            $contextParts[] = "[Sumber {$index}: {$docName} (relevansi: {$score}%)]\n{$chunk->content}";
        }

        return implode("\n\n---\n\n", $contextParts);
    }

    /**
     * Get source info for display in chat
     */
    public function getSources(array $results): array
    {
        return array_map(fn ($r) => [
            'document' => $r['chunk']->document->original_name ?? 'Unknown',
            'chunk_index' => $r['chunk']->chunk_index,
            'score' => round($r['score'] * 100, 1),
            'preview' => Str::limit($r['chunk']->content, 150),
        ], $results);
    }

    /**
     * Compute cosine similarity between two vectors
     *
     * @param  array<float>  $vecA
     * @param  array<float>  $vecB
     * @return float Value between -1 and 1 (1 = identical direction)
     */
    private function cosineSimilarity(array $vecA, array $vecB): float
    {
        if (count($vecA) !== count($vecB)) {
            return 0.0;
        }

        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($vecA as $i => $valA) {
            $valB = $vecB[$i];
            $dotProduct += $valA * $valB;
            $normA += $valA * $valA;
            $normB += $valB * $valB;
        }

        $denominator = sqrt($normA) * sqrt($normB);

        if ($denominator == 0.0) {
            return 0.0;
        }

        return $dotProduct / $denominator;
    }
}
