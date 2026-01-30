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
        Schema::table('machines', function (\Illuminate\Database\Schema\Blueprint $table) {
            if (\Illuminate\Support\Facades\Schema::hasColumn('machines','ma_may')) {
                $table->dropColumn('ma_may');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machines', function (Blueprint $table) {
            //
        });
    }
};
