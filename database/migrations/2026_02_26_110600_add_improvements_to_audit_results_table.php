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
            $table->text('root_cause')->nullable()->after('image_path');
            $table->text('corrective_action')->nullable()->after('root_cause');
            $table->date('improvement_deadline')->nullable()->after('corrective_action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_results', function (Blueprint $table) {
            $table->dropColumn(['root_cause', 'corrective_action', 'improvement_deadline']);
        });
    }
};
