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
    Schema::create('machines', function (Blueprint $table) {
        $table->id();

        $table->string('ma_thiet_bi')->unique();   // dùng cho QR
        $table->string('ten_thiet_bi');

        $table->foreignId('current_department_id')
              ->constrained('departments')
              ->cascadeOnUpdate()
              ->restrictOnDelete();

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
