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
        Schema::create('scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['text', 'url', 'image', 'video', 'humanize'])->default('text');
            $table->longText('content'); // Input text/URL
            $table->string('title')->nullable(); // Auto-generated or first 50 chars
            $table->decimal('ai_score', 5, 2)->nullable(); // 0.00 to 100.00
            $table->decimal('human_score', 5, 2)->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->longText('humanized_text')->nullable(); // Output for humanizer
            $table->integer('word_count')->default(0);
            $table->json('metadata')->nullable(); // Extra data (sources, etc)
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scans');
    }
};
