<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shus', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->decimal('total_shu', 15, 2)->default(0);
            $table->decimal('dibagikan_ke_anggota', 15, 2)->default(0);
            $table->date('tanggal_perhitungan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shus');
    }
};
