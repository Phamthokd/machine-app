<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('guest_unit');                        // Đơn vị khách
            $table->string('purpose');                           // Mục đích vào công ty
            $table->date('visit_date');                          // Ngày hẹn / ngày tạo
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_tickets');
    }
};
