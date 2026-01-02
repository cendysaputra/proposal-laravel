<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Client extends Model
{
    protected $fillable = [
        'judul',
        'month',
        'client_details',
    ];

    protected $casts = [
        'client_details' => 'array',
    ];

    public function years(): BelongsToMany
    {
        return $this->belongsToMany(Year::class);
    }
}
