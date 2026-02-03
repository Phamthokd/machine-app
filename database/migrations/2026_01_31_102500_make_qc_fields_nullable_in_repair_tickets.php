<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repair_tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('endline_qc_user_id')->nullable()->change();
            $table->unsignedBigInteger('inline_qc_user_id')->nullable()->change();
            $table->unsignedBigInteger('qa_supervisor_user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Revert is risky if data has reduced constraints, but we can try to make them nullable false again if needed.
        // For now, simplify.
    }
};
