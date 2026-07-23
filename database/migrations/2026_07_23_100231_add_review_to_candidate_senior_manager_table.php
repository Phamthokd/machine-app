<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidate_senior_manager', function (Blueprint $table) {
            $table->text('review_note')->nullable()->after('user_id');
            $table->timestamp('reviewed_at')->nullable()->after('review_note');
            $table->enum('review_result', ['pending', 'approved', 'rejected'])->default('pending')->after('reviewed_at');
        });
    }

    public function down(): void
    {
        Schema::table('candidate_senior_manager', function (Blueprint $table) {
            $table->dropColumn(['review_note', 'reviewed_at', 'review_result']);
        });
    }
};
