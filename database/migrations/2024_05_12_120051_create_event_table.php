<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id_event');
            $table->string('nama_event');
            $table->string('penyelenggara_event');
            $table->string('upload_ktp')->nullable();
            $table->string('kategori_event');
            $table->dateTime('pelaksanaan_event');
            $table->date('tanggal_pendaftaran');
            $table->date('tanggal_penutupan');
            $table->text('deskripsi');
            $table->string('alamat');
            $table->string('longitude');
            $table->string('latitude');
            $table->string('upload_denah')->nullable();
            $table->string('upload_pamflet')->nullable();
            $table->string('no_rekening');
            $table->string('nama_rekening');
            $table->string('nama_bank');
            $table->string('email');
            $table->string('instagram')->nullable();
            $table->string('whatsapp')->nullable();
            $table->enum('status', ['unverified', 'verified'])->default('unverified')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event');
    }
};