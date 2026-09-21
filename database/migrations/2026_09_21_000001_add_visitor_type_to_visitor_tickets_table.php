<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visitor_tickets', function (Blueprint $table) {
            $table->string('visitor_type', 50)->nullable()->after('guest_unit');
        });
    }

    public function down(): void
    {
        Schema::table('visitor_tickets', function (Blueprint $table) {
            $table->dropColumn('visitor_type');
        });
    }
};
