<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->bigInteger('change_points');
            $table->string('type');
            // Polymorphic reference to the model that caused this transaction
            $table->nullableMorphs('referenceable');
            $table->bigInteger('before_points');
            $table->bigInteger('after_points');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('point_transactions');
    }
};
