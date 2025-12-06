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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable()->comment('Demo session ID for demo users');
            $table->date('expense_date');
            $table->foreignId('expense_category_id')->nullable()->constrained()->onDelete('set null')->comment('Link to expense category');
            $table->text('description');
            $table->string('supplier')->nullable();
            $table->decimal('amount', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index(['user_id', 'session_id']);
            $table->index('expense_date');
            $table->index('expense_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
