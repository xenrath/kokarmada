<?php

namespace Database\Seeders;

use App\Models\Pengaturan;
use Illuminate\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    public function run(): void
    {
        Pengaturan::create([
            'bunga_pinjaman' => 8.0,
            'jangka_waktu_pinjaman' => 2,
        ]);
    }
}
