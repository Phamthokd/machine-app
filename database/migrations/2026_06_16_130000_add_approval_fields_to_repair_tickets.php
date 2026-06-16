<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repair_tickets', function (Blueprint $table) {
            // Trạng thái phê duyệt: null = không cần duyệt, pending_approval = chờ duyệt, approved = đã duyệt, rejected = từ chối
            $table->string('approval_status')->nullable()->after('status');
            $table->text('approval_note')->nullable()->after('approval_status');
            $table->unsignedBigInteger('approved_by')->nullable()->after('approval_note');
            $table->timestamp('approved_at')->nullable()->after('approved_by');

            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('repair_tickets', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['approval_status', 'approval_note', 'approved_by', 'approved_at']);
        });
    }
};
