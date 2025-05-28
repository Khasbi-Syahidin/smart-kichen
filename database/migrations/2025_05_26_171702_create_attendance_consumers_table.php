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
        Schema::create('attendance_consumers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consumer_id')->references('id')->on('consumers')->onDelete('cascade');
            $table->foreignId('attendance_session_id')->references('id')->on('attendance_sessions')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_consumers');
    }
};
