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
    Schema::create('departments', function (Blueprint $table) {
        $table->id();
        $table->string('code')->unique(); // VD: TO_01, KHO
        $table->string('name');          // VD: Tổ 01, Kho
        $table->string('type');          // team, warehouse, qa, qc
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
