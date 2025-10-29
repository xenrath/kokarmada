<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pinjaman extends Model
{
    use HasFactory;

    protected $fillable = [
        'anggota_id',
        'kode_pinjaman',
        'tanggal_pengajuan',
        'tanggal_disetujui',
        'jumlah_pinjaman',
        'lama_angsuran',
        'bunga_persen',
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
