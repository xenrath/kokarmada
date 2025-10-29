<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Angsuran extends Model
{
    use HasFactory;

    protected $fillable = [
        'pinjaman_id',
        'tanggal_jatuh_tempo',
        'tanggal_bayar',
        'jumlah_angsuran',
        'denda',
        'status'
    ];

    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class);
    }
}
