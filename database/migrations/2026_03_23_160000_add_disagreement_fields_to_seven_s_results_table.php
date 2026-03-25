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
        Schema::table('seven_s_results', function (Blueprint $table) {
            if (!Schema::hasColumn('seven_s_results', 'department_agreement')) {
                $table->boolean('department_agreement')->nullable()->after('points')->comment('true = đồng ý lỗi, false = phản đối lỗi');
            }
            if (!Schema::hasColumn('seven_s_results', 'department_reject_reason')) {
                $table->text('department_reject_reason')->nullable()->after('department_agreement')->comment('lý do phản đối từ bộ phận');
            }
            if (!Schema::hasColumn('seven_s_results', 'auditor_rejection_decision')) {
                $table->boolean('auditor_rejection_decision')->nullable()->after('department_reject_reason')->comment('true = chấp nhận huỷ lỗi, false = bác bỏ phản đối phải cải thiện');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seven_s_results', function (Blueprint $table) {
            $table->dropColumn([
                'department_agreement',
                'department_reject_reason',
                'auditor_rejection_decision',
            ]);
        });
    }
};
