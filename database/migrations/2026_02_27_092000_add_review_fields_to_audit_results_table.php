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
        Schema::table('audit_results', function (Blueprint $table) {
            $table->string('reviewer_name')->nullable()->after('improver_name');
            $table->text('review_note')->nullable()->after('reviewer_name');
            $table->string('review_image_path')->nullable()->after('review_note');
            $table->timestamp('reviewed_at')->nullable()->after('review_image_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_results', function (Blueprint $table) {
            $table->dropColumn(['reviewer_name', 'review_note', 'review_image_path', 'reviewed_at']);
        });
    }
};
