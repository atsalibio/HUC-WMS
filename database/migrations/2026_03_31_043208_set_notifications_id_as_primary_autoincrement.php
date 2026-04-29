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
        Schema::dropIfExists('Notifications');
        Schema::create('Notifications', function (Blueprint $table) {
            $table->id('NotificationID');
            $table->unsignedBigInteger('UserID')->nullable();
            $table->string('TargetRole')->nullable();
            $table->string('Title');
            $table->text('Message');
            $table->string('Link')->nullable();
            $table->string('Priority')->default('Normal');
            $table->boolean('IsRead')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Notifications');
    }
};
