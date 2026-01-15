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
        Schema::table('scans', function (Blueprint $table) {
            $table->decimal('plagiarism_score', 5, 2)->nullable()->after('human_score');
            $table->decimal('original_score', 5, 2)->nullable()->after('plagiarism_score');
            $table->json('plagiarism_sources')->nullable()->after('original_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scans', function (Blueprint $table) {
            $table->dropColumn(['plagiarism_score', 'original_score', 'plagiarism_sources']);
        });
    }
};
