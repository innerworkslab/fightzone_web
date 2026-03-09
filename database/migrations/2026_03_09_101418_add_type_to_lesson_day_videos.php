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
        Schema::table('lesson_day_videos', function (Blueprint $table) {
            //
            $table->enum('type', ['Lesson', 'Rest'])->default('Lesson')->after('duration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lesson_day_videos', function (Blueprint $table) {
            //
        });
    }
};
