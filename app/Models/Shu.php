<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shu extends Model
{
    use HasFactory;

    protected $table = 'shu';

    protected $fillable = [
        'year',
        'total_shu',
        'member_share_percentage',
        'management_share_percentage',
        'education_share_percentage',
        'social_share_percentage',
        'reserve_share_percentage',
        'status',
        'calculated_at',
        'calculated_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'total_shu' => 'decimal:2',
            'member_share_percentage' => 'decimal:2',
            'management_share_percentage' => 'decimal:2',
            'education_share_percentage' => 'decimal:2',
            'social_share_percentage' => 'decimal:2',
            'reserve_share_percentage' => 'decimal:2',
            'calculated_at' => 'datetime',
        ];
    }

    public function calculator()
    {
        return $this->belongsTo(User::class, 'calculated_by');
    }

    public function distributions()
    {
        return $this->hasMany(ShuDistribution::class);
    }
}
