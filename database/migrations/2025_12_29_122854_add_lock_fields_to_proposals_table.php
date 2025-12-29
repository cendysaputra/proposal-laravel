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
            $table->boolean('is_locked')->default(false)->after('submitted_at');
            $table->string('lock_username')->nullable()->after('is_locked');
            $table->string('lock_password')->nullable()->after('lock_username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->dropColumn(['is_locked', 'lock_username', 'lock_password']);
        });
    }
};
