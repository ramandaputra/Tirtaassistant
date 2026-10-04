<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser as PdfParser;

/**
 * Handles text extraction from files and chunking for RAG
 */
class DocumentProcessor
{
    // Target chunk size in characters (approx 400-600 tokens)
    private const CHUNK_SIZE = 1500;

    // Overlap between chunks to preserve context
    private const CHUNK_OVERLAP = 200;

    /**
     * Extract text content from a file
     *
     * @param  string  $path  Relative path in storage
     *
     * @throws \Exception
     */
    public function extractText(string $path): string
    {
        $fullPath = Storage::path($path);

        if (! file_exists($fullPath)) {
            throw new \Exception("File not found: {$fullPath}");
        }

        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        return match ($extension) {
            'pdf' => $this->extractFromPdf($fullPath),
            'txt' => $this->extractFromText($fullPath),
            'md' => $this->extractFromText($fullPath),
            'csv' => $this->extractFromText($fullPath),
            default => throw new \Exception("Unsupported file type: {$extension}"),
        };
    }

    /**
     * Extract text from PDF using smalot/pdfparser
     */
    private function extractFromPdf(string $path): string
    {
        try {
            $parser = new PdfParser;
            $pdf = $parser->parseFile($path);
            $text = $pdf->getText();

            // Clean up whitespace
            $text = preg_replace('/\s+/', ' ', $text);
            $text = trim($text);

            if (empty($text)) {
                throw new \Exception('PDF appears to be scanned or contains no extractable text.');
            }

            return $text;
        } catch (\Exception $e) {
            throw new \Exception('PDF parsing failed: '.$e->getMessage());
        }
    }

    /**
     * Extract text from plain text files
     */
    private function extractFromText(string $path): string
    {
        $content = file_get_contents($path);

        if ($content === false) {
            throw new \Exception("Failed to read file: {$path}");
        }

        // Detect and convert encoding if needed
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }

        return $content;
    }

    /**
     * Split text into overlapping chunks for embedding
     *
     * @return array<array{content: string, token_count: int}>
     */
    public function chunkText(string $text): array
    {
        $text = $this->cleanText($text);
        $chunks = [];

        if (strlen($text) <= self::CHUNK_SIZE) {
            return [[
                'content' => $text,
                'token_count' => $this->estimateTokenCount($text),
            ]];
        }

        // Split by paragraphs first for natural boundaries
        $paragraphs = preg_split('/\n\s*\n/', $text);
        $paragraphs = array_filter(array_map('trim', $paragraphs));
        $paragraphs = array_values($paragraphs);

        $currentChunk = '';
        $chunkIndex = 0;

        foreach ($paragraphs as $paragraph) {
            // If adding this paragraph would exceed chunk size
            if (strlen($currentChunk) + strlen($paragraph) + 2 > self::CHUNK_SIZE && ! empty($currentChunk)) {
                $chunks[] = [
                    'content' => trim($currentChunk),
                    'token_count' => $this->estimateTokenCount($currentChunk),
                ];

                // Start new chunk with overlap from previous chunk
                $overlap = $this->getOverlapText($currentChunk);
                $currentChunk = $overlap."\n\n".$paragraph;
                $chunkIndex++;
            } else {
                $currentChunk = empty($currentChunk)
                    ? $paragraph
                    : $currentChunk."\n\n".$paragraph;
            }

            // Handle very long single paragraphs
            while (strlen($currentChunk) > self::CHUNK_SIZE) {
                $splitAt = self::CHUNK_SIZE;
                // Try to split at sentence boundary
                $sentenceEnd = strrpos(substr($currentChunk, 0, $splitAt), '. ');
                if ($sentenceEnd !== false && $sentenceEnd > self::CHUNK_SIZE / 2) {
                    $splitAt = $sentenceEnd + 1;
                }

                $chunks[] = [
                    'content' => trim(substr($currentChunk, 0, $splitAt)),
                    'token_count' => $this->estimateTokenCount(substr($currentChunk, 0, $splitAt)),
                ];

                $overlap = $this->getOverlapText(substr($currentChunk, 0, $splitAt));
                $currentChunk = $overlap.' '.trim(substr($currentChunk, $splitAt));
            }
        }

        // Add remaining text
        if (! empty(trim($currentChunk))) {
            $chunks[] = [
                'content' => trim($currentChunk),
                'token_count' => $this->estimateTokenCount($currentChunk),
            ];
        }

        return $chunks;
    }

    /**
     * Get overlap text from the end of a chunk
     */
    private function getOverlapText(string $chunk): string
    {
        if (strlen($chunk) <= self::CHUNK_OVERLAP) {
            return $chunk;
        }

        $overlap = substr($chunk, -self::CHUNK_OVERLAP);
        // Find sentence start
        $sentenceStart = strpos($overlap, '. ');
        if ($sentenceStart !== false) {
            $overlap = substr($overlap, $sentenceStart + 2);
        }

        return trim($overlap);
    }

    /**
     * Estimate token count (rough approximation: 1 token ≈ 4 characters for Latin, 2 for CJK)
     */
    private function estimateTokenCount(string $text): int
    {
        return (int) ceil(mb_strlen($text) / 3.5);
    }

    /**
     * Clean extracted text
     */
    private function cleanText(string $text): string
    {
        // Remove null bytes
        $text = str_replace("\0", '', $text);
        // Normalize line endings
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        // Remove excessive blank lines (more than 2)
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        // Remove excessive spaces
        $text = preg_replace('/ {2,}/', ' ', $text);

        return trim($text);
    }
}
