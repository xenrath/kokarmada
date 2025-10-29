<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shu extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun',
        'total_shu',
        'dibagikan_ke_anggota',
        'tanggal_perhitungan'
    ];

    public function shuAnggota()
    {
        return $this->hasMany(ShuAnggota::class);
    }
}
