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
        Schema::create('booths', function (Blueprint $table) {
            $table->increments('id_booth');
            $table->string('upload_gambar_booth')->nullable();
            $table->string('tipe_booth');
            $table->integer('harga_booth');
            $table->integer('jumlah_booth');
            $table->text('deskripsi_booth');
            $table->unsignedInteger('id_event');
            $table->foreign('id_event')->references('id_event')->on('events')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booth');
    }
};