<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
      'title',
      'slug',
      'number_invoice',
      'client_info',
      'invoice_date',
      'invoice_due_date',
      'item_details',
      'additional_info',
      'prepared_by',
      'brand',
      'paid',
      'custom_item_details',
      'published_at',
    ];

    protected $casts = [
      'published_at' => 'datetime',
      'invoice_date' => 'date',
      'invoice_due_date' => 'date',
      'item_details' => 'array',
      'custom_item_details' => 'array',
      'paid' => 'boolean',
    ];
}
