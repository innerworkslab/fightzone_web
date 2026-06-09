<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_physical_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('stance')->nullable();
            $table->enum('rope_jump_level', ['beginner','intermediate','expert'])->default('beginner');
            $table->enum('fitness_level', ['beginner','intermediate','expert'])->default('beginner');
            $table->enum('boxing_level', ['beginner','intermediate','expert'])->default('beginner');
            $table->float('weight')->nullable();
            $table->enum('gender', ['male','female'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_physical_profiles');
    }
};
