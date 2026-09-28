<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanCollateral extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'type',
        'description',
        'ownership_status',
        'ownership_proof',
        'proof_file',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
