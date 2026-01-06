<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PinjamanAgunan extends Model
{
    use HasFactory;

    protected $fillable = [
        'pinjaman_id',
        'jenis_agunan',
        'jenis_agunan_lainnya',
        'bukti_agunan',
        'bukti_kepemilikan',
        'bukti_file',
    ];
}
