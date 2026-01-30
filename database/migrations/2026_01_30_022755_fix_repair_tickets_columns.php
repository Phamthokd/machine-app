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

        if (!Schema::hasColumn('repair_tickets', 'machine_id')) {
            $table->foreignId('machine_id')->nullable()->after('id')->constrained('machines');
        }

        if (!Schema::hasColumn('repair_tickets', 'department_id')) {
            $table->foreignId('department_id')->nullable()->after('machine_id')->constrained('departments');
        }

        if (!Schema::hasColumn('repair_tickets', 'ma_hang')) {
            $table->string('ma_hang')->nullable();
        }

        if (!Schema::hasColumn('repair_tickets', 'cong_doan')) {
            $table->string('cong_doan')->nullable();
        }

        if (!Schema::hasColumn('repair_tickets', 'nguyen_nhan')) {
            $table->text('nguyen_nhan')->nullable();
        }

        if (!Schema::hasColumn('repair_tickets', 'noi_dung_sua_chua')) {
            $table->text('noi_dung_sua_chua')->nullable();
        }

        if (!Schema::hasColumn('repair_tickets', 'started_at')) {
            $table->dateTime('started_at')->nullable();
        }

        if (!Schema::hasColumn('repair_tickets', 'ended_at')) {
            $table->dateTime('ended_at')->nullable();
        }

        if (!Schema::hasColumn('repair_tickets', 'endline_qc_user_id')) {
            $table->foreignId('endline_qc_user_id')->nullable()->constrained('users');
        }

        if (!Schema::hasColumn('repair_tickets', 'inline_qc_user_id')) {
            $table->foreignId('inline_qc_user_id')->nullable()->constrained('users');
        }

        if (!Schema::hasColumn('repair_tickets', 'qa_supervisor_user_id')) {
            $table->foreignId('qa_supervisor_user_id')->nullable()->constrained('users');
        }

        if (!Schema::hasColumn('repair_tickets', 'created_by')) {
            $table->foreignId('created_by')->nullable()->constrained('users');
        }

        if (!Schema::hasColumn('repair_tickets', 'status')) {
            $table->string('status')->default('submitted');
        }

        if (!Schema::hasColumn('repair_tickets', 'code')) {
            $table->string('code')->nullable();
        }
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repair_tickets', function (Blueprint $table) {
            //
        });
    }
};
