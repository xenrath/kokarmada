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
        'net_monthly_income',
        'other_monthly_income',
        'external_monthly_obligations',
        'existing_coop_installment',
        'available_income',
        'capacity_threshold_percent',
        'capacity_limit_amount',
        'old_outstanding_principal',
        'old_principal_paid',
        'old_principal_repayment_percent',
        'old_monthly_installment',
        'simulated_new_installment',
        'remaining_income_after_new_installment',
        'notes',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'recommended_amount' => 'decimal:2',
            'net_monthly_income' => 'decimal:2',
            'other_monthly_income' => 'decimal:2',
            'external_monthly_obligations' => 'decimal:2',
            'existing_coop_installment' => 'decimal:2',
            'available_income' => 'decimal:2',
            'capacity_threshold_percent' => 'decimal:2',
            'capacity_limit_amount' => 'decimal:2',
            'old_outstanding_principal' => 'decimal:2',
            'old_principal_paid' => 'decimal:2',
            'old_principal_repayment_percent' => 'decimal:2',
            'old_monthly_installment' => 'decimal:2',
            'simulated_new_installment' => 'decimal:2',
            'remaining_income_after_new_installment' => 'decimal:2',
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
