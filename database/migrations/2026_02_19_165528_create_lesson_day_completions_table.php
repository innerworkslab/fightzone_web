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
        Schema::create('lesson_day_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_level_purchase_id')->constrained('course_level_purchases')->cascadeOnDelete();
            $table->foreignId('lesson_day_id')->constrained('lesson_days')->cascadeOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['course_level_purchase_id', 'lesson_day_id'], 'ldc_purchase_day_unique');
            $table->index(['course_level_purchase_id', 'completed_at'], 'ldc_purchase_completed_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_day_completions');
    }
};
