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
        Schema::create('lesson_day_video_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_level_purchase_id')->constrained('course_level_purchases')->cascadeOnDelete();
            $table->foreignId('lesson_day_video_id')->constrained('lesson_day_videos')->cascadeOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['course_level_purchase_id', 'lesson_day_video_id'], 'ldvc_purchase_video_unique');
            $table->index(['course_level_purchase_id', 'completed_at'], 'ldvc_purchase_completed_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_day_video_completions');
    }
};
