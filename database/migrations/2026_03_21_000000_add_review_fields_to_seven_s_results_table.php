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
        // Columns already exists in database but migration was not recorded.
        // Skipping to allow next migrations to run.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seven_s_results', function (Blueprint $table) {
            $table->dropForeign(['reviewer_id']);
            $table->dropColumn(['review_status', 'reviewer_id', 'review_note', 'reviewed_at']);
        });
    }
};
