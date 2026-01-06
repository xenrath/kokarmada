<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pinjaman extends Model
{
    use HasFactory;

    protected $fillable = [
        'urutan',
        'kode',
        'anggota_id',
        'tanggal_pengajuan',
        'tanggal_disetujui',
        'nominal',
        'usaha',
        'usaha_lainnya',
        'jangka_waktu',
        'jangka_waktu',
        'tipe_angsuran',
        'total_pinjaman',
        'status',
        'keterangan'
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }

    public function angsuran()
    {
        return $this->hasMany(Angsuran::class);
    }
}
