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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->uuid('session_id')->nullable()->index();
            $table->foreignId('ateco_code_id')->nullable()->constrained()->nullOnDelete();
            $table->string('invoice_number_format', 120)->default('{year}/{seq:3}');
            $table->string('professional_fund', 80)->nullable();
            $table->boolean('reduced_contributions')->default(false);
            $table->boolean('startup_rate')->default(false);
            // Percentuale predefinita per contributo integrativo (es. 4%)
            $table->decimal('contributo_integrativo_percent', 5, 2)->default(4.00)->comment('Default contributo integrativo percentuale');
            $table->timestamps();

            $table->unique(['user_id', 'session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
