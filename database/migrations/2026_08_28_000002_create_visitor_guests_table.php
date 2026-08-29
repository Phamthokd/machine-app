<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitor_guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visitor_ticket_id')->constrained('visitor_tickets')->cascadeOnDelete();
            $table->string('full_name');                                 // Họ và tên
            $table->string('id_number', 50)->nullable();                 // Số CCCD/CMND
            $table->string('guest_card_number', 50)->nullable();         // Số thẻ khách (nhập tay)
            $table->boolean('baggage_checked')->nullable();              // Kiểm tra hành lý (true=có, false=không)
            $table->timestamp('checked_in_at')->nullable();              // Thời gian vào
            $table->timestamp('checked_out_at')->nullable();             // Thời gian ra
            $table->text('note')->nullable();                            // Ghi chú (bảo vệ)
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete(); // Bảo vệ xử lý
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitor_guests');
    }
};
