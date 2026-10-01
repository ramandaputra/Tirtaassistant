<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_chunks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('knowledge_document_id')->constrained()->onDelete('cascade');
            $table->integer('chunk_index');
            $table->text('content');
            $table->json('embedding')->nullable(); // Stored as JSON array
            $table->integer('token_count')->default(0);
            $table->timestamps();

            $table->index('knowledge_document_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_chunks');
    }
};
