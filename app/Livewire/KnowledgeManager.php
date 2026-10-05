<?php

namespace App\Livewire;

use App\Jobs\ProcessDocumentJob;
use App\Models\DocumentChunk;
use App\Models\KnowledgeDocument;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class KnowledgeManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    /**
     * Staged uploaded files for drag and drop (supports multiple files).
     * Named `stagedFiles` to avoid collision with the `$documents` paginator variable in the view.
     *
     * @var array<TemporaryUploadedFile>
     */
    public array $stagedFiles = [];

    /**
     * Search query for filtering documents
     */
    public string $search = '';

    /**
     * Filter by document status (all, ready, processing, pending, failed)
     */
    public string $statusFilter = 'all';

    /**
     * Active document ID for the chunk inspection modal
     */
    public ?int $selectedDocId = null;

    /**
     * Modal visibility state
     */
    public bool $showChunkModal = false;

    /**
     * Validation rules
     */
    protected array $rules = [
        'stagedFiles.*' => 'required|file|mimes:pdf,txt,csv,md|max:10240', // max 10MB per file
    ];

    /**
     * Custom validation messages in Indonesian
     */
    protected array $messages = [
        'stagedFiles.*.required' => 'File dokumen wajib dipilih.',
        'stagedFiles.*.file' => 'Berkas harus berupa file yang valid.',
        'stagedFiles.*.mimes' => 'Format file yang didukung: PDF, TXT, CSV, dan Markdown (.md).',
        'stagedFiles.*.max' => 'Ukuran setiap file maksimal 10MB.',
    ];

    public function updatedStagedFiles(): void
    {
        $this->validate();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function removeStagedFile(int $index): void
    {
        if (isset($this->stagedFiles[$index])) {
            unset($this->stagedFiles[$index]);
            $this->stagedFiles = array_values($this->stagedFiles);
        }
    }

    public function clearStagedFiles(): void
    {
        $this->stagedFiles = [];
        $this->resetValidation();
    }

    public function uploadDocuments(): void
    {
        $this->validate();

        if (empty($this->stagedFiles)) {
            session()->flash('error', 'Silakan pilih atau tarik berkas dokumen ke area unggah terlebih dahulu.');

            return;
        }

        $successCount = 0;
        $failedCount = 0;
        $errorDetails = [];

        foreach ($this->stagedFiles as $uploadedFile) {
            try {
                $originalName = $uploadedFile->getClientOriginalName();
                $path = $uploadedFile->store('knowledge_base');

                $doc = KnowledgeDocument::create([
                    'filename' => basename($path),
                    'original_name' => $originalName,
                    'mime_type' => $uploadedFile->getMimeType(),
                    'file_size' => $uploadedFile->getSize(),
                    'status' => 'pending',
                ]);

                try {
                    // Extract text, chunk, and generate vector embeddings
                    ProcessDocumentJob::dispatchSync($doc->id);
                    $successCount++;
                } catch (\Throwable $e) {
                    $failedCount++;
                    $errorDetails[] = "{$originalName}: ".$e->getMessage();
                    Log::error("Failed to process document {$doc->id}: ".$e->getMessage());
                }
            } catch (\Throwable $e) {
                $failedCount++;
                $errorDetails[] = $e->getMessage();
                Log::error('Failed to store file: '.$e->getMessage());
            }
        }

        $this->stagedFiles = [];
        $this->resetPage();

        if ($failedCount === 0) {
            session()->flash('message', "Berhasil! {$successCount} dokumen telah diunggah dan siap digunakan dalam Knowledge Base.");
        } elseif ($successCount > 0) {
            session()->flash('warning', "{$successCount} dokumen berhasil diproses, namun {$failedCount} dokumen mengalami kendala. Periksa status pada tabel.");
        } else {
            session()->flash('error', 'Gagal memproses dokumen: '.implode('; ', array_slice($errorDetails, 0, 2)));
        }
    }

    public function deleteDocument(int $id): void
    {
        $doc = KnowledgeDocument::find($id);
        if ($doc) {
            Storage::delete('knowledge_base/'.$doc->filename);
            $doc->chunks()->delete();
            $doc->delete();

            if ($this->selectedDocId === $id) {
                $this->closeInspectModal();
            }

            session()->flash('message', "Dokumen '{$doc->original_name}' berhasil dihapus dari Knowledge Base.");
        }
    }

    public function reprocessDocument(int $id): void
    {
        $document = KnowledgeDocument::findOrFail($id);
        $document->update(['status' => 'pending', 'error_message' => null]);

        try {
            ProcessDocumentJob::dispatchSync($document->id);
            session()->flash('message', "Dokumen '{$document->original_name}' berhasil diproses ulang dan embedding diperbarui.");
        } catch (\Throwable $e) {
            session()->flash('error', 'Gagal memproses ulang dokumen: '.$e->getMessage());
        }
    }

    public function inspectDocument(int $id): void
    {
        $this->selectedDocId = $id;
        $this->showChunkModal = true;
    }

    public function closeInspectModal(): void
    {
        $this->showChunkModal = false;
        $this->selectedDocId = null;
    }

    public function render()
    {
        $query = KnowledgeDocument::query();

        if (! empty(trim($this->search))) {
            $query->where('original_name', 'like', '%'.trim($this->search).'%');
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $knowledgeDocs = $query->latest()->paginate(10);

        // System overview statistics
        $stats = [
            'total' => KnowledgeDocument::count(),
            'ready' => KnowledgeDocument::where('status', 'ready')->count(),
            'processing' => KnowledgeDocument::whereIn('status', ['pending', 'processing'])->count(),
            'failed' => KnowledgeDocument::where('status', 'failed')->count(),
            'total_chunks' => DocumentChunk::count(),
        ];

        // Retrieve active document for chunk inspection modal
        $selectedDocument = null;
        if ($this->selectedDocId && $this->showChunkModal) {
            $selectedDocument = KnowledgeDocument::with([
                'chunks' => fn ($q) => $q->orderBy('chunk_index'),
            ])->find($this->selectedDocId);
        }

        return view('livewire.knowledge-manager', [
            'knowledgeDocs' => $knowledgeDocs,
            'stats' => $stats,
            'selectedDocument' => $selectedDocument,
            'hasProcessing' => $stats['processing'] > 0,
        ]);
    }
}
