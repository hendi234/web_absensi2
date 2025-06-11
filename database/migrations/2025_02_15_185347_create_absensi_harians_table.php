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
        Schema::create('daily_attendance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_attendance_in');
            $table->foreign('id_attendance_in')->references('id')->on('attendance_in')->onDelete("CASCADE")->onUpdate("CASCADE");
            $table->unsignedBigInteger('id_attendance_out');
            $table->foreign('id_attendance_out')->references('id')->on('attendance_out')->onDelete("CASCADE")->onUpdate("CASCADE");
            $table->time('work_time')->nullable();
            $table->boolean('status')->default(false);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_attendance');
    }
};
