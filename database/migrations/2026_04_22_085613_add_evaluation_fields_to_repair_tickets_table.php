<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repair_tickets', function (Blueprint $table) {
            $table->string('eval_response_time')->nullable()->after('qa_supervisor_name'); // fast | ok | slow
            $table->string('eval_repair_speed')->nullable()->after('eval_response_time');  // fast | ok | slow_affect
            $table->string('eval_error_rate')->nullable()->after('eval_repair_speed');     // none | few | frequent
            $table->timestamp('evaluated_at')->nullable()->after('eval_error_rate');
        });
    }

    public function down(): void
    {
        Schema::table('repair_tickets', function (Blueprint $table) {
            $table->dropColumn(['eval_response_time', 'eval_repair_speed', 'eval_error_rate', 'evaluated_at']);
        });
    }
};
