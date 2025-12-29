<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'telp',
        'password',
        'nama',
        'panggilan',
        'gender',
        'role',
        'spesial',
        'status'
    ];

    public function anggota()
    {
        return $this->hasOne(Anggota::class);
    }

    public function isDev()
    {
        if ($this->role == 'dev') {
            return true;
        } else {
            return false;
        }
    }

    public function isAdmin()
    {
        if ($this->role == 'admin') {
            return true;
        } else {
            return false;
        }
    }
    
    public function isAnggota()
    {
        if ($this->role == 'anggota') {
            return true;
        } else {
            return false;
        }
    }

    public function isKetua()
    {
        if ($this->spesial == 'ketua') {
            return true;
        } else {
            return false;
        }
    }
    
    public function isSekretaris()
    {
        if ($this->spesial == 'sekretaris') {
            return true;
        } else {
            return false;
        }
    }
    
    public function isBendahara()
    {
        if ($this->spesial == 'bendahara') {
            return true;
        } else {
            return false;
        }
    }
    
    public function isManajer()
    {
        if ($this->spesial == 'manajer') {
            return true;
        } else {
            return false;
        }
    }

    public function isPetugas()
    {
        if ($this->spesial == 'petugas') {
            return true;
        } else {
            return false;
        }
    }
}
