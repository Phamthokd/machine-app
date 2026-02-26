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
            $table->string('improver_name')->nullable()->after('improvement_deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_results', function (Blueprint $table) {
            $table->dropColumn('improver_name');
        });
    }
};
