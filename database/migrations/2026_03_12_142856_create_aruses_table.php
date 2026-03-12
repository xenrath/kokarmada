<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aruses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rekening_id')->constrained('rekenings')->onDelete('restrict');
            $table->string('pengadaan');
            $table->int('jumlah');
            $table->text('keterangan');
            $table->enum('jenis', ['pemasukan', 'pengeluaran']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aruses');
    }
};
