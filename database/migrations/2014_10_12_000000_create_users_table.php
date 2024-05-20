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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama_lengkap');
            $table->string('no_telp');
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('password');

            $table->string('nama_perusahaan');
            $table->string('alamat_perusahaan');
            $table->string('deskripsi_perusahaan');

            $table->string('firebase_id')->nullable();
            

            // $table->enum('status_verifikasi', ['unverified', 'verified'])->default('unverified')->nullable();
            $table->enum('jabatan', ['admin', 'pembuat'])->default('pembuat');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
