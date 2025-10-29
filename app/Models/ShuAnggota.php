<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShuAnggota extends Model
{
    use HasFactory;

    protected $fillable = ['shu_id', 'anggota_id', 'jumlah_diterima'];

    public function shu()
    {
        return $this->belongsTo(Shu::class);
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }
}
