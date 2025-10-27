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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null')->comment('NULL for occasional customer');
            $table->foreignId('ateco_code_id')->constrained('ateco_codes')->onDelete('restrict')->comment('Link to ATECO code');
            $table->string('invoice_number', 50);
            $table->date('issue_date');
            $table->date('payment_date')->nullable()->comment('When actually paid');
            $table->text('description');
            $table->decimal('amount', 10, 2);
            $table->decimal('withholding_tax', 10, 2)->default(0.00)->comment('20% withholding if applicable');
            $table->decimal('net_amount', 10, 2)->comment('amount - withholding_tax');
            $table->boolean('is_paid')->default(false);
            $table->string('payment_method', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('customer_id');
            $table->index('ateco_code_id');
            $table->index('issue_date');
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
