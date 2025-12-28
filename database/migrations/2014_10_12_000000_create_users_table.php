<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email', 100);
            $table->string('password');
            $table->string('nama');
            $table->enum('gender', ['L', 'P']);
            $table->enum('role', ['dev', 'admin', 'anggota'])->default('anggota');
            $table->enum('spesial', ['ketua', 'sekretaris', 'bendahara', 'manajer', 'petugas', 'normal'])->default('normal');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
