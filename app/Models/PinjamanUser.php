<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PinjamanUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'pinjaman_id',
        'telp',
        'nama',
        'panggilan',
        'gender',
        'file_kk',
        'file_ktp',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'kode_pos',
        'pekerjaan',
        'no_npwp',
        'nama_ibu',
        'tinggal_bersama',
        'nama_pasangan',
        'pekerjaan_pasangan',
    ];
}
