<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
      'title',
      'slug',
      'number_invoice',
      'company_name'
    ];

    protected $casts = [
      'published_at' => 'datetime',
    ];
}
