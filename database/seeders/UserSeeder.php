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
                'panggilan' => 'Admin',
                'gender' => 'L',
                'role' => 'admin',
                'spesial' => 'normal',
                'status' => 'aktif',
            ],
            [
                'telp' => '08111',
                'password' => bcrypt('bhamada'),
                'nama' => 'Firman Hidayat, S.Kep.Ns., M.Kep., Sp.Kep.J.',
                'panggilan' => 'Firman',
                'gender' => 'L',
                'role' => 'anggota',
                'spesial' => 'ketua',
                'status' => 'aktif',
            ],
            [
                'telp' => '08222',
                'password' => bcrypt('bhamada'),
                'nama' => 'Anisa Oktiawati, S.Kep., M.Kep.',
                'panggilan' => 'Anisa',
                'gender' => 'P',
                'role' => 'anggota',
                'spesial' => 'sekretaris',
                'status' => 'aktif',
            ],
            [
                'telp' => '08333',
                'password' => bcrypt('bhamada'),
                'nama' => 'Nur Khayati, SE., MM.',
                'panggilan' => 'Nur',
                'gender' => 'P',
                'role' => 'anggota',
                'spesial' => 'bendahara',
                'status' => 'aktif',
            ],
            [
                'telp' => '08444',
                'password' => bcrypt('bhamada'),
                'nama' => 'Siti Raudhah Nadia, S.Agr',
                'panggilan' => 'Nadia',
                'gender' => 'P',
                'role' => 'anggota',
                'spesial' => 'manajer',
                'status' => 'aktif',
            ],
            [
                'telp' => '08555',
                'password' => bcrypt('bhamada'),
                'nama' => 'Aghafiana Dewi Maharani, Amd.Kom.',
                'panggilan' => 'Rani',
                'gender' => 'P',
                'role' => 'anggota',
                'spesial' => 'petugas',
                'status' => 'aktif',
            ],
            [
                'telp' => '08666',
                'password' => bcrypt('bhamada'),
                'nama' => 'Agung Tyas Subekti, S.Kep., MA',
                'panggilan' => 'Agung',
                'gender' => 'L',
                'role' => 'anggota',
                'spesial' => 'petugas',
                'status' => 'aktif',
            ],
            [
                'telp' => '08777',
                'password' => bcrypt('bhamada'),
                'nama' => 'Ramadhan Putra Satria, S.Kep.Ns., M.Kep',
                'panggilan' => 'Ramadhan',
                'gender' => 'L',
                'role' => 'anggota',
                'spesial' => 'petugas',
                'status' => 'aktif',
            ],
            [
                'telp' => '08888',
                'password' => bcrypt('bhamada'),
                'nama' => 'Saiful Labib Marzuqi Hidayat, S.Tr.Kom.',
                'panggilan' => 'Labib',
                'gender' => 'L',
                'role' => 'anggota',
                'spesial' => 'normal',
                'status' => 'aktif',
            ],
        ];

        User::insert($users);
    }
}
