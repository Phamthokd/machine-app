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
            if (!Schema::hasColumn('audit_results', 'image_path')) {
                $table->string('image_path')->nullable()->after('note');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_results', function (Blueprint $table) {
            if (Schema::hasColumn('audit_results', 'image_path')) {
                $table->dropColumn('image_path');
            }
        });
    }
};
