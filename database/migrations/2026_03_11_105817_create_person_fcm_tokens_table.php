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
        Schema::create('person_fcm_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('fcm_token');
            $table->unsignedInteger('personable_id');
            $table->char('personable_type'); #customer ,user
            $table->timestamps();
            $table->index(['personable_id', 'personable_type'], 'notif_people_pers_idx');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_fcm_tokens');
    }
};
