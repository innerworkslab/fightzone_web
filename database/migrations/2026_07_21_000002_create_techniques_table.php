<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('techniques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('technique_category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->string('url');
            $table->string('provider')->nullable();
            $table->string('provider_video_id')->nullable();
            $table->time('duration')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['technique_category_id', 'is_active']);
            $table->index(['provider', 'provider_video_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('techniques');
    }
};
