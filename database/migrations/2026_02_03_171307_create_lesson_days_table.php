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
        Schema::create('lesson_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_level_id')->constrained('course_levels')->cascadeOnDelete();
            $table->integer('day_number');
            $table->enum('type', ['Lesson', 'Rest']);
            $table->string('name');
            $table->string('video_url');
            $table->time('duration');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_days');
    }
};
