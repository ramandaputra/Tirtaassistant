<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KnowledgeDocument extends Model
{
    protected $fillable = [
        'filename',
        'original_name',
        'mime_type',
        'file_size',
        'status',
        'chunk_count',
        'error_message',
    ];

    public function chunks(): HasMany
    {
        return $this->hasMany(DocumentChunk::class);
    }

    public function isReady(): bool
    {
        return $this->status === 'ready';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'    => '⏳ Pending',
            'processing' => '⚙️ Processing',
            'ready'      => '✅ Ready',
            'failed'     => '❌ Failed',
            default      => $this->status,
        };
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes < 1024) return "{$bytes} B";
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }
}
