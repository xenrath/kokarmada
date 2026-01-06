<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    use HasFactory;

    protected $fillable = [
        'bunga_pinjaman_pertahun',
        'bunga_pinjaman_perbulan',
        'jangka_waktu_pinjaman',
    ];
}
