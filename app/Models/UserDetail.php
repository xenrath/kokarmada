<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'no_ktp',
        'masa_berlaku_ktp',
        'foto_diri',
        'file_ktp',
        'file_kk',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'kode_pos',
        'pekerjaan',
        'no_npwp',
        'nama_ibu',
        'tinggal_bersama',
        'nama_pasangan',
        'pekerjaan_pasangan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
