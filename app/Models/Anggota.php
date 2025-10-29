<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nomor_anggota',
        'nik',
        'nama',
        'alamat',
        'no_hp',
        'tanggal_masuk',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function simpanan()
    {
        return $this->hasMany(Simpanan::class);
    }

    public function pinjaman()
    {
        return $this->hasMany(Pinjaman::class);
    }

    public function shuAnggota()
    {
        return $this->hasMany(ShuAnggota::class);
    }
}
