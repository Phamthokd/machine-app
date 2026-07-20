<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('it_repairs', function (Blueprint $table) {
            $table->string('nguoi_ho_tro')->nullable()->after('resolver_id');
        });
    }

    public function down(): void
    {
        Schema::table('it_repairs', function (Blueprint $table) {
            $table->dropColumn('nguoi_ho_tro');
        });
    }
};
