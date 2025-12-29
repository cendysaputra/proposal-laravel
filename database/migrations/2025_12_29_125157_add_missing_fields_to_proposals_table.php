<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->string('client_name')->nullable()->after('proposal_number');
            $table->string('company_name')->nullable()->after('client_name');
            $table->json('short_brief')->nullable()->after('company_name');
            $table->string('additional_image_qr')->nullable()->after('short_brief');
            $table->boolean('aktifkan_garansi')->default(false)->after('additional_image_qr');
            $table->string('package_name_one')->nullable()->after('aktifkan_garansi');
            $table->string('option_price_one')->nullable()->after('package_name_one');
            $table->string('option_renewal_price_one')->nullable()->after('option_price_one');
            $table->string('option_price_coret_one')->nullable()->after('option_renewal_price_one');
            $table->string('berlaku_x_tahun')->nullable()->after('option_price_coret_one');
            $table->json('core_services')->nullable()->after('berlaku_x_tahun');
            $table->json('standard_features')->nullable()->after('core_services_custom');
            $table->json('asset')->nullable()->after('standard_features_custom');
            $table->json('server')->nullable()->after('asset_custom');
            $table->json('security')->nullable()->after('server_custom');
            $table->json('support')->nullable()->after('security_custom');
            $table->text('add_ons_features')->nullable()->after('support_custom');
            $table->json('modern_timeline')->nullable()->after('add_ons_features');
            $table->text('text_portfolio')->nullable()->after('modern_timeline');
            $table->json('gallery_portfolio')->nullable()->after('text_portfolio');
            $table->text('additional_notes')->nullable()->after('gallery_portfolio');
            $table->string('project_manager')->nullable()->after('additional_notes');
            $table->string('brand_project')->nullable()->after('project_manager');
            $table->string('masa_berlaku')->nullable()->after('brand_project');
            $table->string('allowance_meeting')->nullable()->after('masa_berlaku');
            $table->boolean('offline_meeting')->default(false)->after('allowance_meeting');
            $table->boolean('using_wordpress')->default(false)->after('offline_meeting');
            $table->boolean('use_tnb')->default(false)->after('using_wordpress');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->dropColumn([
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
            ]);
        });
    }
};
