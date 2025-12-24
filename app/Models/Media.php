<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $fillable = [
        'name',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'type',
        'category',
        'description',
        'user_id',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getUrl(): string
    {
        return Storage::url($this->file_path);
    }

    public function getFileSizeFormatted(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    public function isDocument(): bool
    {
        return $this->type === 'document';
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($media) {
            Storage::delete($media->file_path);
        });
    }
}
