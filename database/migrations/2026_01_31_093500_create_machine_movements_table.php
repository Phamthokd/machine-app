<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('machine_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_id')->constrained('machines');
            $table->foreignId('from_department_id')->constrained('departments'); // Old department
            $table->foreignId('to_department_id')->constrained('departments');   // New department
            $table->foreignId('user_id')->nullable()->constrained('users');      // Who moved it
            $table->string('note')->nullable(); // Justification/Note
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('machine_movements');
    }
};
