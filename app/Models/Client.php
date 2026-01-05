<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Client extends Model
{
    protected $fillable = [
        'judul',
        'slug',
        'client_details',
    ];

    protected $casts = [
        'client_details' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($client) {
            if (empty($client->slug)) {
                $client->slug = static::generateUniqueSlug($client->judul);
            }
        });

        static::updating(function ($client) {
            if ($client->isDirty('judul')) {
                $client->slug = static::generateUniqueSlug($client->judul, $client->id);
            }
        });
    }

    protected static function generateUniqueSlug($judul, $id = null)
    {
        $slug = Str::slug($judul);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
