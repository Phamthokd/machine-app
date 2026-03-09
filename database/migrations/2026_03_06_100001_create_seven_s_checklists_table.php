<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seven_s_checklists', function (Blueprint $table) {
            $table->id();
            $table->string('department');           // e.g. 'XNK'
            $table->string('section');              // e.g. 'S1&S2&S3'
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->text('content');                // Vietnamese checklist item description
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seven_s_checklists');
    }
};
