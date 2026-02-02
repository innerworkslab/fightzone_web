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
        Schema::create('course_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->integer('day_number');
            $table->enum('type', ['Lesson', 'Rest'])->default('Lesson');
            $table->string('video_link')->nullable();
            $table->time('duration')->nullable(); // HH:MM:SS format
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Unique constraint: each course can have only one day_number
            $table->unique(['course_id', 'day_number']);
            $table->index(['course_id', 'type']);
            $table->index(['course_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_days');
    }
};
