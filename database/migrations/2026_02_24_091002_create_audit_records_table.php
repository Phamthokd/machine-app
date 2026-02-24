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
        Schema::create('audit_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_template_id')->constrained('audit_templates')->onDelete('cascade');
            $table->foreignId('auditor_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('completed'); // draft, completed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_records');
    }
};
