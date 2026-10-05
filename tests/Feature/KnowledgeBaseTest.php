<?php

namespace Tests\Feature;

use App\Livewire\KnowledgeManager;
use App\Models\KnowledgeDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class KnowledgeBaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_knowledge_base_page_is_accessible(): void
    {
        $response = $this->get('/knowledge-base');

        $response->assertStatus(200);
        $response->assertSee('Knowledge Base Management');
    }

    public function test_knowledge_manager_component_renders_correctly(): void
    {
        KnowledgeDocument::create([
            'filename' => 'sop-layanan.pdf',
            'original_name' => 'SOP Layanan Pelanggan.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 102400,
            'status' => 'ready',
            'chunk_count' => 5,
        ]);

        Livewire::test(KnowledgeManager::class)
            ->assertStatus(200)
            ->assertSee('SOP Layanan Pelanggan.pdf')
            ->assertSee('5 chunks')
            ->assertSee('Total Dokumen');
    }

    public function test_knowledge_manager_can_filter_by_search(): void
    {
        KnowledgeDocument::create([
            'filename' => 'tarif-air-2024.pdf',
            'original_name' => 'Tarif Air Minum 2024.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 51200,
            'status' => 'ready',
            'chunk_count' => 3,
        ]);

        KnowledgeDocument::create([
            'filename' => 'kontak-darurat.txt',
            'original_name' => 'Kontak Darurat.txt',
            'mime_type' => 'text/plain',
            'file_size' => 1200,
            'status' => 'ready',
            'chunk_count' => 1,
        ]);

        Livewire::test(KnowledgeManager::class)
            ->set('search', 'Tarif')
            ->assertSee('Tarif Air Minum 2024.pdf')
            ->assertDontSee('Kontak Darurat.txt');
    }

    public function test_knowledge_manager_can_delete_document(): void
    {
        Storage::fake('local');

        $doc = KnowledgeDocument::create([
            'filename' => 'doc-to-delete.pdf',
            'original_name' => 'Dokumen Dihapus.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 2048,
            'status' => 'ready',
        ]);

        Livewire::test(KnowledgeManager::class)
            ->call('deleteDocument', $doc->id)
            ->assertSee('berhasil dihapus');

        $this->assertDatabaseMissing('knowledge_documents', [
            'id' => $doc->id,
        ]);
    }
}
