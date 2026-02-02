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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('course_category_id')->constrained('course_categories')->cascadeOnDelete();
            $table->enum('level', ['beginner', 'intermediate', 'expert'])->default('beginner');
            $table->double('price')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['course_category_id', 'is_active']);
            $table->index(['level', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
