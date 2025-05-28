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
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->enum('session', ['breakfast', 'lunch', 'dinner']);
            $table->foreignId('menu_id')->references('id')->on('menus')->onDelete('cascade');
            $table->foreignId('supervisor_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('note')->nullable();
            $table->dateTime('date')->default(now());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_sessions');
    }
};
