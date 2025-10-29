<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pinjamans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anggota_id')->constrained('anggotas')->onDelete('cascade');
            $table->string('kode_pinjaman', 20)->unique();
            $table->date('tanggal_pengajuan')->nullable();
            $table->date('tanggal_disetujui')->nullable();
            $table->decimal('jumlah_pinjaman', 15, 2);
            $table->integer('lama_angsuran'); // bulan
            $table->decimal('bunga_persen', 5, 2)->default(0);
            $table->decimal('total_pinjaman', 15, 2)->nullable();
            $table->enum('status', ['diajukan', 'disetujui', 'ditolak', 'lunas', 'berjalan'])->default('diajukan');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pinjamen');
    }
};
