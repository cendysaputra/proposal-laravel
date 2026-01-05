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
        // Drop month column from clients table
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('month');
        });

        // Drop pivot table if it exists
        Schema::dropIfExists('client_year');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore month column
        Schema::table('clients', function (Blueprint $table) {
            $table->string('month')->nullable()->after('slug');
        });

        // Recreate pivot table
        Schema::create('client_year', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('year_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }
};
