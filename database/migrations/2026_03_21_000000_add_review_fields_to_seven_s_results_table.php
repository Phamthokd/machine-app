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
        Schema::table('seven_s_results', function (Blueprint $table) {
            if (!Schema::hasColumn('seven_s_results', 'review_status')) {
                $table->string('review_status')->nullable()->after('improved_at');
            }
            if (!Schema::hasColumn('seven_s_results', 'reviewer_id')) {
                $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete()->after('review_status');
            }
            if (!Schema::hasColumn('seven_s_results', 'review_note')) {
                $table->text('review_note')->nullable()->after('reviewer_id');
            }
            if (!Schema::hasColumn('seven_s_results', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('review_note');
            }
        });
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
