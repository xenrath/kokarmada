<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'member_number',
        'national_id',
        'national_id_expiry',
        'national_id_file',
        'family_card_file',
        'photo_file',
        'birth_place',
        'birth_date',
        'address',
        'postal_code',
        'occupation',
        'tax_id',
        'mother_name',
        'marital_status',
        'spouse_name',
        'spouse_occupation',
        'bank_name',
        'bank_account_number',
    ];

    protected function casts(): array
    {
        return [
            'national_id_expiry' => 'date',
            'birth_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}