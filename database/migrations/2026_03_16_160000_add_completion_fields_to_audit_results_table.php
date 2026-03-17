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
            $table->boolean('is_completed')->default(false)->after('improver_name');
            $table->timestamp('completed_at')->nullable()->after('is_completed');
            $table->text('completion_image_path')->nullable()->after('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_results', function (Blueprint $table) {
            $table->dropColumn(['is_completed', 'completed_at', 'completion_image_path']);
        });
    }
};
