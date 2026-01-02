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
            // Drop old status column
            $table->dropColumn('status');
        });

        Schema::table('clients', function (Blueprint $table) {
            // Add new columns
            $table->date('meeting_date')->nullable()->after('company_name');
            $table->boolean('proposal')->default(false)->after('notes');
            $table->boolean('mockup')->default(false)->after('proposal');
            $table->string('link_mockup')->nullable()->after('mockup');

            // Add new status with updated options
            $table->enum('status', ['Deal', 'Cancel', 'Progress', 'Review Mockup'])
                ->default('Progress')
                ->after('link_mockup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['meeting_date', 'proposal', 'mockup', 'link_mockup', 'status']);
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive'])->default('active');
        });
    }
};
