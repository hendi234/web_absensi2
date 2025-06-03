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
        Schema::create('absensi_harians', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_absen_masuks');
            $table->foreign('id_absen_masuks')->references('id')->on('absen_masuks')->onDelete("CASCADE")->onUpdate("CASCADE");
            $table->unsignedBigInteger('id_absen_keluars');
            $table->foreign('id_absen_keluars')->references('id')->on('absen_keluars')->onDelete("CASCADE")->onUpdate("CASCADE");
            $table->time('durasi_kerja')->nullable();
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
        Schema::dropIfExists('absensi_harians');
    }
};
