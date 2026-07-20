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
        Schema::create('it_repairs', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('department')->nullable();
            $table->foreignId('reporter_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('issue_type'); // computer, network, printer, software, other
            $table->string('title');
            $table->text('description');
            $table->string('location')->nullable();
            $table->string('priority')->default('medium'); // low, medium, high, urgent
            $table->string('status')->default('pending'); // pending, in_progress, resolved, closed
            $table->foreignId('resolver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('resolution_note')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->json('images')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('it_repairs');
    }
};
