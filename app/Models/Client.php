<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'judul',
        'client_details',
    ];

    protected $casts = [
        'client_details' => 'array',
    ];
}
