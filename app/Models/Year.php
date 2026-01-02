<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Year extends Model
{
    protected $fillable = [
        'year',
        'order',
    ];

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class);
    }
}
