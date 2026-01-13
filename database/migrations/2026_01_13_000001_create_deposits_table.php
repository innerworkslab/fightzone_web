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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_method_id')->constrained('payment_methods')->cascadeOnDelete();
            $table->string('transaction_id')->unique();
            $table->double('amount');
            $table->string('screenshot_path')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'rejected'])->default('pending');
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->text('admin_note')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deposits');
    }
};
