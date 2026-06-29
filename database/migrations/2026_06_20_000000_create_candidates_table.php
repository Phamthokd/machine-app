<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->enum('gender', ['male', 'female'])->default('male');
            $table->date('dob')->nullable();
            $table->string('id_number', 50)->nullable();
            $table->string('education')->nullable();
            $table->string('language_skills')->nullable();
            $table->string('position_applied');
            $table->string('phone', 20);
            $table->string('address')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('photo_path')->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced'])->default('single');
            $table->json('children_dob')->nullable();           // ["2018","2020"]
            $table->json('referral_source')->nullable();        // ["zalo","internal"]
            $table->string('referral_name')->nullable();
            $table->string('referral_department')->nullable();
            $table->string('referral_relation')->nullable();
            $table->string('emergency_name')->nullable();
            $table->string('emergency_address')->nullable();
            $table->string('emergency_relation')->nullable();
            $table->string('emergency_phone', 20)->nullable();
            $table->json('work_experiences')->nullable();       // [{start,end,company,position,salary,reason}]
            $table->string('expected_salary')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
