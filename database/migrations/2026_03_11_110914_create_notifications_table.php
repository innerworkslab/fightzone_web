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
        Schema::create('notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('preview');
            $table->dateTime('date_time');
            $table->unsignedBigInteger('notificationable_id')->nullable();
            $table->char('notificationable_type')->nullable();
            $table->unsignedBigInteger('createdable_id');
            $table->char('createdable_type');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['createdable_id', 'createdable_type', 'notificationable_id', 'notificationable_type'], 'notifications_created_notif_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
