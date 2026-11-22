<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seven_s_v2_records', function (Blueprint $table) {
            $table->id();
            $table->string('department');
            $table->foreignId('inspector_id')->constrained('users')->cascadeOnDelete();
            $table->integer('light_fault_count')->default(0);
            $table->integer('heavy_fault_count')->default(0);
            $table->integer('total_penalty_points')->default(0);
            $table->text('general_note')->nullable();
            $table->string('status')->default('completed'); // completed, in_progress
            $table->timestamps();
        });

        Schema::create('seven_s_v2_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('record_id')->constrained('seven_s_v2_records')->cascadeOnDelete();
            $table->text('content');
            $table->string('severity')->default('light'); // light (-1), heavy (-2)
            $table->integer('penalty_points')->default(-1);
            $table->string('location_detail')->nullable();
            $table->json('image_path')->nullable();
            
            // Quy trình khắc phục & nghiệm thu
            $table->string('status')->default('pending'); // pending, improved, verified, rejected
            $table->text('improvement_note')->nullable();
            $table->json('improvement_image_path')->nullable();
            $table->foreignId('improver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('improved_at')->nullable();
            
            $table->foreignId('verifier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seven_s_v2_issues');
        Schema::dropIfExists('seven_s_v2_records');
    }
};
