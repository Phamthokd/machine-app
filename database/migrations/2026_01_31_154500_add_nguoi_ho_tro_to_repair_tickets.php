<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repair_tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('repair_tickets', 'nguoi_ho_tro')) {
                $table->string('nguoi_ho_tro')->nullable()->after('noi_dung_sua_chua');
            }
        });
    }

    public function down(): void
    {
        Schema::table('repair_tickets', function (Blueprint $table) {
            if (Schema::hasColumn('repair_tickets', 'nguoi_ho_tro')) {
                $table->dropColumn('nguoi_ho_tro');
            }
        });
    }
};
