<?php

namespace App\Livewire;

use App\Jobs\ProcessDocumentJob;
use App\Models\KnowledgeDocument;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class KnowledgeManager extends Component
{
    use WithFileUploads;

    public $document;

    public function updatedDocument()
    {
        $this->validate([
            'document' => 'required|file|mimes:pdf,txt,csv|max:10240', // max 10MB
        ]);
    }

    public function uploadDocument()
    {
        $this->validate([
            'document' => 'required|file|mimes:pdf,txt,csv|max:10240',
        ]);

        $originalName = $this->document->getClientOriginalName();
        $filename = $this->document->store('knowledge_base');
        
        // Save to DB
        $doc = KnowledgeDocument::create([
            'filename' => basename($filename),
            'original_name' => $originalName,
            'mime_type' => $this->document->getMimeType(),
            'file_size' => $this->document->getSize(),
            'status' => 'pending',
        ]);

        $this->document = null; // reset
        
        // Dispatch job to process the document
        ProcessDocumentJob::dispatch($doc->id);
        
        session()->flash('message', 'Dokumen berhasil diunggah dan sedang diproses.');
    }

    public function deleteDocument($id)
    {
        $doc = KnowledgeDocument::find($id);
        if ($doc) {
            Storage::delete('knowledge_base/' . $doc->filename);
            $doc->delete();
            session()->flash('message', 'Dokumen berhasil dihapus.');
        }
    }

    public function render()
    {
        return view('livewire.knowledge-manager', [
            'documents' => KnowledgeDocument::latest()->get()
        ]);
    }
}
