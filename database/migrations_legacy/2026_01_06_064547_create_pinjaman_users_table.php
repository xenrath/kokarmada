<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pinjaman_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pinjaman_id')->constrained('pinjamans')->onDelete('restrict');
            $table->string('telp');
            $table->string('nama');
            $table->string('panggilan');
            $table->enum('gender', ['L', 'P']);
            $table->string('file_ktp');
            $table->string('file_kk');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->text('alamat');
            $table->string('kode_pos');
            $table->string('pekerjaan');
            $table->string('no_npwp');
            $table->string('nama_ibu');
            $table->string('tinggal_bersama');
            $table->string('nama_pasangan')->nullable();
            $table->string('pekerjaan_pasangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pinjaman_users');
    }
};
