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
        Schema::create('audit_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_record_id')->constrained('audit_records')->onDelete('cascade');
            $table->foreignId('audit_criterion_id')->constrained('audit_criteria')->onDelete('cascade');
            $table->boolean('is_passed');
            $table->text('note')->nullable(); // Required if not passed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_results');
    }
};
