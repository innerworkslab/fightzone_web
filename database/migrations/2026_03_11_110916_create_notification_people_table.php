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
        Schema::create('notification_people', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('is_read')->default(0);
            $table->boolean('is_read_count')->default(0);
            $table->dateTime('read_at')->nullable();
            $table->unsignedBigInteger('personable_id');
            $table->char('personable_type');#customer ,user
            $table->foreignId('notification_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->index(['personable_id', 'personable_type', 'is_read_count', 'is_read'], 'notif_people_pers_read_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_people');
    }
};
