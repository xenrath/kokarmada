<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pinjaman_agunans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pinjaman_id')->constrained('pinjamans')->onDelete('restrict');
            $table->enum('jenis_agunan', ['kendaraan', 'tanah_bangunan', 'pekarangan', 'sawah', 'lainnya']);
            $table->string('jenis_agunan_lainnya');
            $table->enum('bukti_agunan', ['shm', 'hgb', 'hgu', 'hak_pakai', 'bpkb']);
            $table->enum('bukti_kepemilikan', ['milik_nasabah', 'bukan_milik_nasabah']);
            $table->string('bukti_file');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pinjaman_agunans');
    }
};
