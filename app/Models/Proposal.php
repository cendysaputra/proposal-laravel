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
        'short_brief_custom',
        'additional_image_qr',
        'aktifkan_garansi',
        'package_name_one',
        'option_price_one',
        'option_renewal_price_one',
        'option_price_coret_one',
        'berlaku_x_tahun',
        'core_services',
        'core_services_custom',
        'standard_features',
        'standard_features_custom',
        'asset',
        'asset_custom',
        'server',
        'server_custom',
        'security',
        'security_custom',
        'support',
        'support_custom',
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
        'aktifkan_garansi' => 'boolean',
        'offline_meeting' => 'boolean',
        'using_wordpress' => 'boolean',
        'short_brief' => 'array',
        'short_brief_custom' => 'array',
        'core_services' => 'array',
        'core_services_custom' => 'array',
        'standard_features' => 'array',
        'standard_features_custom' => 'array',
        'asset' => 'array',
        'asset_custom' => 'array',
        'server' => 'array',
        'server_custom' => 'array',
        'security' => 'array',
        'security_custom' => 'array',
        'support' => 'array',
        'support_custom' => 'array',
        'modern_timeline' => 'array',
        'gallery_portfolio' => 'array',
    ];
}
