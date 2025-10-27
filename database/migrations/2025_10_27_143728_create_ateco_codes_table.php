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
        Schema::create('ateco_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('ateco_code', 10)->comment('e.g: 62.01.00');
            $table->string('description')->comment('e.g: Software development');
            $table->decimal('profitability_coeff', 5, 2)->comment('e.g: 78.00 (78%)');
            $table->boolean('is_primary')->default(false)->comment('Main activity');
            $table->timestamps();
            
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ateco_codes');
    }
};
