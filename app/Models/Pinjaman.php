<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pinjaman extends Model
{
    use HasFactory;

    protected $table = 'pinjamans';

    protected $fillable = [
        'urutan',
        'kode',
        'user_id',
        'tanggal_pengajuan',
        'tanggal_disetujui',
        'nominal',
        'tujuan',
        'usaha',
        'usaha_lainnya',
        'jangka_waktu',
        'tipe_angsuran',
        'tempat_kerja',
        'jabatan_terakhir',
        'lama_kerja',
        'pendapatan_kotor',
        'pendapatan_bersih',
        'slip_gaji',
        'bunga_persen',
        'total_pinjaman',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function angsuran()
    {
        return $this->hasMany(Angsuran::class);
    }
}
