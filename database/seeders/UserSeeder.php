<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'telp' => '08000',
                'password' => bcrypt('bhamada'),
                'nama' => 'Admin',
                'gender' => 'L',
                'role' => 'admin',
                'spesial' => 'normal',
                'status' => 'aktif',
            ],
            [
                'telp' => '08111',
                'password' => bcrypt('bhamada'),
                'nama' => 'Ketua',
                'gender' => 'L',
                'role' => 'anggota',
                'spesial' => 'ketua',
                'status' => 'aktif',
            ],
            [
                'telp' => '08222',
                'password' => bcrypt('bhamada'),
                'nama' => 'Sekretaris',
                'gender' => 'L',
                'role' => 'anggota',
                'spesial' => 'sekretaris',
                'status' => 'aktif',
            ],
            [
                'telp' => '08333',
                'password' => bcrypt('bhamada'),
                'nama' => 'Bendahara',
                'gender' => 'L',
                'role' => 'anggota',
                'spesial' => 'bendahara',
                'status' => 'aktif',
            ],
            [
                'telp' => '08444',
                'password' => bcrypt('bhamada'),
                'nama' => 'Manajer Analis',
                'gender' => 'L',
                'role' => 'anggota',
                'spesial' => 'manajer',
                'status' => 'aktif',
            ],
            [
                'telp' => '08555',
                'password' => bcrypt('bhamada'),
                'nama' => 'Petugas',
                'gender' => 'L',
                'role' => 'anggota',
                'spesial' => 'petugas',
                'status' => 'aktif',
            ],
            [
                'telp' => '08666',
                'password' => bcrypt('bhamada'),
                'nama' => 'Anggota',
                'gender' => 'L',
                'role' => 'anggota',
                'spesial' => 'normal',
                'status' => 'aktif',
            ],
        ];

        User::insert($users);
    }
}
