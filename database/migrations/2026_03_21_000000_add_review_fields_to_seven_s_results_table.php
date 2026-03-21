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
            $table->string('review_status')->nullable()->after('improved_at');
            $table->unsignedBigInteger('reviewer_id')->nullable()->after('review_status');
            $table->text('review_note')->nullable()->after('reviewer_id');
            $table->timestamp('reviewed_at')->nullable()->after('review_note');

            $table->foreign('reviewer_id')->references('id')->on('users')->onDelete('set null');
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
