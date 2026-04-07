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
        Schema::create('environment_reports', function (Blueprint $table) {
            $table->id();
            $table->string('department_name');
            $table->unsignedSmallInteger('report_year');
            $table->unsignedTinyInteger('report_month');
            $table->foreignId('creator_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('draft');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['department_name', 'report_year', 'report_month'], 'environment_reports_unique_period');
        });

        Schema::create('environment_report_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('environment_report_id')->constrained('environment_reports')->cascadeOnDelete();
            $table->date('report_date');
            $table->unsignedTinyInteger('day_number');

            $table->decimal('humidity_0730', 5, 1)->nullable();
            $table->decimal('humidity_1030', 5, 1)->nullable();
            $table->decimal('humidity_1400', 5, 1)->nullable();
            $table->decimal('humidity_1630', 5, 1)->nullable();

            $table->decimal('temperature_0730', 5, 1)->nullable();
            $table->decimal('temperature_1030', 5, 1)->nullable();
            $table->decimal('temperature_1400', 5, 1)->nullable();
            $table->decimal('temperature_1630', 5, 1)->nullable();

            $table->string('weather')->nullable();
            $table->string('action_0730')->nullable();
            $table->string('action_1030')->nullable();
            $table->string('action_1400')->nullable();
            $table->string('action_1630')->nullable();
            $table->string('checked_by')->nullable();
            $table->timestamps();

            $table->unique(['environment_report_id', 'day_number'], 'environment_report_entries_unique_day');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('environment_report_entries');
        Schema::dropIfExists('environment_reports');
    }
};
