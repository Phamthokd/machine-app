<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seven_s_records', function (Blueprint $table) {
            $table->string('status')->default('completed')->after('max_score')->index();
        });
    }

    public function down(): void
    {
        Schema::table('seven_s_records', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn('status');
        });
    }
};
