<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'nama' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('bhamada'),
                'gender' => 'L',
                'role' => 'admin',
                'spesial' => 'normal',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Ketua',
                'email' => 'ketua@gmail.com',
                'password' => bcrypt('bhamada'),
                'gender' => 'L',
                'role' => 'anggota',
                'spesial' => 'ketua',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Sekretaris',
                'email' => 'sekretaris@gmail.com',
                'password' => bcrypt('bhamada'),
                'gender' => 'L',
                'role' => 'anggota',
                'spesial' => 'sekretaris',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Bendahara',
                'email' => 'bendahara@gmail.com',
                'password' => bcrypt('bhamada'),
                'gender' => 'L',
                'role' => 'anggota',
                'spesial' => 'bendahara',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Manajer Analis',
                'email' => 'manajer@gmail.com',
                'password' => bcrypt('bhamada'),
                'gender' => 'L',
                'role' => 'anggota',
                'spesial' => 'manajer',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Petugas',
                'email' => 'petugas@gmail.com',
                'password' => bcrypt('bhamada'),
                'gender' => 'L',
                'role' => 'anggota',
                'spesial' => 'petugas',
                'status' => 'aktif',
            ],
            [
                'nama' => 'Anggota',
                'email' => 'anggota@gmail.com',
                'password' => bcrypt('bhamada'),
                'gender' => 'L',
                'role' => 'anggota',
                'spesial' => 'normal',
                'status' => 'aktif',
            ],
        ];

        User::insert($users);
    }
}
