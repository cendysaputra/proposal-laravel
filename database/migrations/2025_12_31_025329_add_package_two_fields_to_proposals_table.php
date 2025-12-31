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
            $table->string('package_name_two')->nullable()->after('option_price_coret_one');
            $table->string('option_price_two')->nullable()->after('package_name_two');
            $table->string('option_renewal_price_two')->nullable()->after('option_price_two');
            $table->string('option_price_coret_two')->nullable()->after('option_renewal_price_two');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->dropColumn(['package_name_two', 'option_price_two', 'option_renewal_price_two', 'option_price_coret_two']);
        });
    }
};
