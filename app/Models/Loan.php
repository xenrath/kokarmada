<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'sequence_number',
        'user_id',
        'submitted_at',
        'requested_amount',
        'purpose_category',
        'business_type',
        'business_type_other',
        'purpose_description',
        'term_months',
        'repayment_type',
        'monthly_installment',
        'work_unit',
        'position',
        'employment_duration_years',
        'other_monthly_income',
        'other_income_proof',
        'net_monthly_income',
        'salary_slip',
        'interest_rate',
        'approved_amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'requested_amount' => 'decimal:2',
            'monthly_installment' => 'decimal:2',
            'other_monthly_income' => 'decimal:2',
            'net_monthly_income' => 'decimal:2',
            'interest_rate' => 'decimal:2',
            'approved_amount' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function analysis()
    {
        return $this->hasOne(LoanAnalysis::class);
    }

    public function treasurerReview()
    {
        return $this->hasOne(LoanTreasurerReview::class);
    }

    public function processes()
    {
        return $this->hasMany(LoanProcess::class);
    }

    public function documents()
    {
        return $this->hasMany(LoanDocument::class);
    }

    public function collaterals()
    {
        return $this->hasMany(LoanCollateral::class);
    }

    public function disbursement()
    {
        return $this->hasOne(LoanDisbursement::class);
    }

    public function installments()
    {
        return $this->hasMany(Installment::class);
    }

    public function cashFlows()
    {
        return $this->hasMany(CashFlow::class);
    }
}
