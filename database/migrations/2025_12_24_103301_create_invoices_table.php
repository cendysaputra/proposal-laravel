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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('number_invoice');
            $table->text('client_info')->nullable();
            $table->date('invoice_date');
            $table->date('invoice_due_date');
            $table->json('item_details')->nullable();
            $table->text('additional_info')->nullable();
            $table->text('custom_item_details')->nullable();
            $table->string('prepared_by')->nullable();
            $table->string('brand')->nullable();
            $table->boolean('paid')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
