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
            $table->json('core_services_custom')->nullable()->after('core_services');
            $table->json('standard_features_custom')->nullable()->after('standard_features');
            $table->json('asset_custom')->nullable()->after('asset');
            $table->json('server_custom')->nullable()->after('server');
            $table->json('security_custom')->nullable()->after('security');
            $table->json('support_custom')->nullable()->after('support');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->dropColumn([
                'core_services_custom',
                'standard_features_custom',
                'asset_custom',
                'server_custom',
                'security_custom',
                'support_custom',
            ]);
        });
    }
};
