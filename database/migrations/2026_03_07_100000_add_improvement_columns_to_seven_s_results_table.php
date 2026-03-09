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
            $table->text('improvement_note')->nullable()->after('points');
            $table->json('improvement_image_path')->nullable()->after('improvement_note');
            $table->foreignId('improver_id')->nullable()->constrained('users')->nullOnDelete()->after('improvement_image_path');
            $table->timestamp('improved_at')->nullable()->after('improver_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seven_s_results', function (Blueprint $table) {
            $table->dropForeign(['improver_id']);
            $table->dropColumn(['improvement_note', 'improvement_image_path', 'improver_id', 'improved_at']);
        });
    }
};
