<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'analyst_id',
        'recommended_amount',
        'notes',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'recommended_amount' => 'decimal:2',
            'reviewed_at' => 'datetime',
        ];
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function analyst()
    {
        return $this->belongsTo(User::class, 'analyst_id');
    }
}
