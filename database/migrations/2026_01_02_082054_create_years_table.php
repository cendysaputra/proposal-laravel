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
        Schema::create('years', function (Blueprint $table) {
            $table->id();
            $table->string('year')->unique();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Pivot table for many-to-many relationship
        Schema::create('client_year', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('year_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Seed default years
        DB::table('years')->insert([
            ['year' => '2025', 'order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['year' => '2026', 'order' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_year');
        Schema::dropIfExists('years');
    }
};
