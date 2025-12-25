<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
      'title',
      'slug',
      'number_invoice',
      'company_name',
      'invoice_date',
      'invoice_due_date',
    ];

    protected $casts = [
      'published_at' => 'datetime',
      'invoice_date' => 'date',
      'invoice_due_date' => 'date',
    ];
}
