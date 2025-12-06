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
            $table->string('session_id')->nullable()->comment('Demo session ID for demo users');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null')->comment('Reference to customer, NULL for occasional');
            $table->foreignId('ateco_code_id')->constrained('ateco_codes')->onDelete('restrict')->comment('Link to ATECO code');

            // Customer data snapshot at invoice time
            $table->string('customer_business_name', 255);
            $table->string('customer_email', 255)->nullable();
            $table->string('customer_vat_number', 20)->nullable();
            $table->string('customer_tax_code', 20)->nullable();
            $table->text('customer_address')->nullable();
            $table->string('customer_city', 100)->nullable();
            $table->string('customer_province', 2)->nullable();
            $table->string('customer_postal_code', 10)->nullable();
            $table->string('customer_country', 2)->nullable()->default('IT');
            $table->string('customer_phone', 30)->nullable();
            $table->string('customer_pec', 255)->nullable();
            $table->string('customer_sdi_code', 7)->nullable();

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
            $table->index(['user_id', 'session_id']);
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
