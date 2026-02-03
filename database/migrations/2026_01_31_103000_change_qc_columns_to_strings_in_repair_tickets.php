<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repair_tickets', function (Blueprint $table) {
            // Drop Foreign Keys first
            // Note: Use array syntax to drop multiple.
            // Constraint names based on standard Laravel naming: table_column_foreign
            $table->dropForeign(['endline_qc_user_id']);
            $table->dropForeign(['inline_qc_user_id']);
            $table->dropForeign(['qa_supervisor_user_id']);
            
            // Now drop columns
            $table->dropColumn(['endline_qc_user_id', 'inline_qc_user_id', 'qa_supervisor_user_id']);
            
            // Add new string columns
            $table->string('endline_qc_name')->nullable();
            $table->string('inline_qc_name')->nullable();
            $table->string('qa_supervisor_name')->nullable();
        });
    }

    public function down(): void
    {
    }
};
