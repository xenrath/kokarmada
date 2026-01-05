<?php

namespace Database\Seeders;

use App\Models\Pengaturan;
use Illuminate\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    public function run(): void
    {
        Pengaturan::create([
            'bunga_pinjaman_pertahun' => 8.0,
            'bunga_pinjaman_perbulan' => 0.67,
            'jangka_waktu_pinjaman' => 2,
        ]);
    }
}
