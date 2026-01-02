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
        Schema::table('clients', function (Blueprint $table) {
            // Drop columns that will be moved to repeater
            $table->dropColumn([
                'name',
                'company_name',
                'meeting_date',
                'email',
                'phone',
                'address',
                'city',
                'province',
                'postal_code',
                'notes',
                'proposal',
                'mockup',
                'link_mockup',
                'status',
            ]);
        });

        Schema::table('clients', function (Blueprint $table) {
            // Add JSON column for repeater data
            $table->json('client_details')->nullable()->after('judul');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('client_details');
        });

        Schema::table('clients', function (Blueprint $table) {
            // Restore old columns
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->date('meeting_date')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('proposal')->default(false);
            $table->boolean('mockup')->default(false);
            $table->string('link_mockup')->nullable();
            $table->enum('status', ['Deal', 'Cancel', 'Progress', 'Review Mockup'])->default('Progress');
        });
    }
};
