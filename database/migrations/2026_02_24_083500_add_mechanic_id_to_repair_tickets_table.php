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
            if (!Schema::hasColumn('repair_tickets', 'mechanic_id')) {
                $table->foreignId('mechanic_id')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repair_tickets', function (Blueprint $table) {
            if (Schema::hasColumn('repair_tickets', 'mechanic_id')) {
                $table->dropForeign(['mechanic_id']);
                $table->dropColumn('mechanic_id');
            }
        });
    }
};
