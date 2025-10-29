<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\User;
use Illuminate\Database\Seeder;

class KoperasiSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'Admin Koperasi',
            'email' => 'admin@koperasi.test',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        Anggota::create([
            'user_id' => $user->id,
            'nomor_anggota' => 'AG001',
            'nama' => 'Admin Koperasi',
            'tanggal_masuk' => now(),
            'status' => 'aktif'
        ]);
    }
}
