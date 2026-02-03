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
        Schema::table('repair_tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('repair_tickets', 'type')) {
                // 'mechanic' or 'contractor'
                $table->string('type')->default('mechanic')->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repair_tickets', function (Blueprint $table) {
            if (Schema::hasColumn('repair_tickets', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
