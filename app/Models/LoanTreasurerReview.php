<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanTreasurerReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'treasurer_id',
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

    public function treasurer()
    {
        return $this->belongsTo(User::class, 'treasurer_id');
    }
}
