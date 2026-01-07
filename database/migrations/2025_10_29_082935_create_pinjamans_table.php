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
            $table->integer('urutan');
            $table->string('kode')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->date('tanggal_pengajuan');
            $table->date('tanggal_disetujui')->nullable();
            $table->bigInteger('nominal');
            $table->text('tujuan');
            $table->enum('usaha', ['perdagangan', 'pertanian', 'jasa', 'lainnya']);
            $table->string('usaha_lainnya');
            $table->integer('jangka_waktu'); // tahun
            $table->enum('tipe_angsuran', ['bulanan', 'sekaligus']);
            $table->string('tempat_kerja');
            $table->string('jabatan_terakhir');
            $table->integer('lama_kerja');
            $table->integer('pendapatan_kotor');
            $table->integer('pendapatan_bersih');
            $table->string('slip_gaji');
            $table->decimal('bunga_persen', 5, 2);
            $table->bigInteger('total_pinjaman');
            $table->enum('status', [
                'diajukan',
                'disetujui_manajer',
                'disetujui_ketua',
                'ditolak',
                'lunas',
                'berjalan'
            ])->default('diajukan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pinjamans');
    }
};
