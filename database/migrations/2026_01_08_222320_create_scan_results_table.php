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
        Schema::create('scan_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scan_id')->constrained()->onDelete('cascade');
            $table->string('provider'); // 'gptzero', 'originality', 'copyleaks', etc.
            $table->decimal('ai_score', 5, 2)->nullable();
            $table->decimal('human_score', 5, 2)->nullable();
            $table->decimal('confidence', 5, 2)->nullable();
            $table->json('raw_response')->nullable(); // Full API response
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->index(['scan_id', 'provider']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scan_results');
    }
};
