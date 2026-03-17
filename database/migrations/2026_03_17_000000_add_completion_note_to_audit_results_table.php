<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_results', function (Blueprint $table) {
            $table->text('completion_note')->nullable()->after('completion_image_path');
        });
    }

    public function down(): void
    {
        Schema::table('audit_results', function (Blueprint $table) {
            $table->dropColumn('completion_note');
        });
    }
};
