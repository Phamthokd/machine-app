<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seven_s_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('record_id')->constrained('seven_s_records')->cascadeOnDelete();
            $table->foreignId('checklist_id')->constrained('seven_s_checklists')->cascadeOnDelete();
            $table->enum('grade', ['B', 'C', 'D', 'E']);
            $table->text('note')->nullable();
            $table->json('image_path')->nullable();
            $table->integer('points');  // computed: B=2, C=1, D=0, E=-2
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seven_s_results');
    }
};
