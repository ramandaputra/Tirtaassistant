<?php

namespace App\Jobs;

use App\Models\DocumentChunk;
use App\Models\KnowledgeDocument;
use App\Services\DocumentProcessor;
use App\Services\GeminiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessDocumentJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 300; // 5 minutes

    public function __construct(
        private readonly int $documentId
    ) {}

    public function handle(DocumentProcessor $processor, GeminiService $gemini): void
    {
        $document = KnowledgeDocument::find($this->documentId);

        if (! $document) {
            Log::warning("ProcessDocumentJob: Document {$this->documentId} not found");

            return;
        }

        try {
            // Mark as processing
            $document->update(['status' => 'processing']);

            // Step 1: Extract text from file
            Log::info("Extracting text from: {$document->filename}");
            $text = $processor->extractText("knowledge_base/{$document->filename}");

            if (empty(trim($text))) {
                throw new \Exception('No text content extracted from document');
            }

            // Step 2: Chunk the text
            Log::info("Chunking text for document: {$document->id}");
            $chunks = $processor->chunkText($text);

            if (empty($chunks)) {
                throw new \Exception('No chunks created from document');
            }

            // Step 3: Delete existing chunks for re-processing
            $document->chunks()->delete();

            // Step 4: Generate embeddings and save chunks
            $successfulChunks = 0;
            foreach ($chunks as $index => $chunkData) {
                $total = count($chunks);
                Log::info("Generating embedding for chunk {$index}/{$total}");

                // Generate embedding via Gemini
                $embedding = $gemini->generateEmbedding($chunkData['content']);

                // Add small delay to avoid rate limiting
                if ($index > 0 && $index % 10 === 0) {
                    sleep(1);
                }

                DocumentChunk::create([
                    'knowledge_document_id' => $document->id,
                    'chunk_index' => $index,
                    'content' => $chunkData['content'],
                    'embedding' => $embedding,
                    'token_count' => $chunkData['token_count'],
                ]);

                if ($embedding) {
                    $successfulChunks++;
                }
            }

            if ($successfulChunks === 0) {
                throw new \Exception('Tidak ada embedding yang berhasil dibuat untuk dokumen ini.');
            }

            // Step 5: Mark document as ready
            $document->update([
                'status' => 'ready',
                'chunk_count' => count($chunks),
            ]);

            $totalChunks = count($chunks);
            Log::info("Document {$document->id} processed: {$successfulChunks}/{$totalChunks} chunks embedded");
        } catch (\Exception $e) {
            Log::error("ProcessDocumentJob failed for document {$this->documentId}: ".$e->getMessage());

            $document->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("ProcessDocumentJob permanently failed for document {$this->documentId}: ".$exception->getMessage());

        KnowledgeDocument::where('id', $this->documentId)->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
        ]);
    }
}
