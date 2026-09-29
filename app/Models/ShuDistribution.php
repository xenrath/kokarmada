<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShuDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'shu_id',
        'user_id',
        'member_share_amount',
        'capital_service_amount',
        'business_service_amount',
        'total_amount',
        'status',
        'distributed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'member_share_amount' => 'decimal:2',
            'capital_service_amount' => 'decimal:2',
            'business_service_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'distributed_at' => 'datetime',
        ];
    }

    public function shu()
    {
        return $this->belongsTo(Shu::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
