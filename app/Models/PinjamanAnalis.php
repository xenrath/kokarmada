<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PinjamanAnalis extends Model
{
    use HasFactory;

    protected $table = 'pinjaman_analiss';

    protected $fillable = [
        'pinjaman_id',
        'manajer_id',
        'manajer_nama',
        'nominal',
        'catatan',
        'status',
    ];

    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class);
    }
}
