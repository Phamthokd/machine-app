<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidate_senior_manager', function (Blueprint $table) {
            $table->string('proposed_salary', 100)->nullable()->after('review_result');   // Mức lương đề xuất
            $table->date('start_date')->nullable()->after('proposed_salary');              // Ngày bắt đầu làm việc
            $table->string('probation_period', 100)->nullable()->after('start_date');     // Thời gian thử việc
            $table->string('assigned_department', 255)->nullable()->after('probation_period'); // Bộ phận/Vị trí
            $table->text('extra_note')->nullable()->after('assigned_department');         // Ghi chú bổ sung
        });
    }

    public function down(): void
    {
        Schema::table('candidate_senior_manager', function (Blueprint $table) {
            $table->dropColumn(['proposed_salary', 'start_date', 'probation_period', 'assigned_department', 'extra_note']);
        });
    }
};
