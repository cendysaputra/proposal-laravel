<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'proposal_number',
        'client_name',
        'company_name',
        'short_brief',
        'additional_image_qr',
        'aktifkan_garansi',
        'package_name_one',
        'option_price_one',
        'option_renewal_price_one',
        'option_price_coret_one',
        'berlaku_x_tahun',
        'core_services',
        'standard_features',
        'asset',
        'server',
        'security',
        'support',
        'add_ons_features',
        'modern_timeline',
        'text_portfolio',
        'gallery_portfolio',
        'additional_notes',
        'project_manager',
        'brand_project',
        'masa_berlaku',
        'allowance_meeting',
        'offline_meeting',
        'using_wordpress',
        'use_tnb',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',

        // Boolean fields (yes/no)
        'aktifkan_garansi' => 'boolean',
        'offline_meeting' => 'boolean',
        'using_wordpress' => 'boolean',

        // Array fields (multi checkbox + repeater + gallery)
        'short_brief' => 'array',
        'core_services' => 'array',
        'standard_features' => 'array',
        'asset' => 'array',
        'server' => 'array',
        'security' => 'array',
        'support' => 'array',
        'modern_timeline' => 'array',
        'gallery_portfolio' => 'array',
    ];
}
