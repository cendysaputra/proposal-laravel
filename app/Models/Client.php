<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name',
        'company_name',
        'email',
        'phone',
        'address',
        'city',
        'province',
        'postal_code',
        'notes',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];
}
