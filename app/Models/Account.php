<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'bank_name',
        'account_number',
        'account_name',
        'opening_balance',
        'opening_balance_initialized_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'opening_balance' => 'decimal:2',
            'opening_balance_initialized_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Account $account) {
            if ($account->opening_balance_initialized_at === null) {
                $account->opening_balance_initialized_at = now();
            }
        });
    }

    public function loanDisbursements()
    {
        return $this->hasMany(LoanDisbursement::class);
    }

    public function cashFlows()
    {
        return $this->hasMany(CashFlow::class);
    }

    public function savingsTransactions()
    {
        return $this->hasMany(SavingsTransaction::class);
    }

    public function calculateBalance(): float
    {
        $inflow = (float) $this->cashFlows()
            ->where('type', 'in')
            ->sum('amount');

        $outflow = (float) $this->cashFlows()
            ->where('type', 'out')
            ->sum('amount');

        return (float) $this->opening_balance + $inflow - $outflow;
    }

    public function needsOpeningBalanceInitialization(): bool
    {
        return $this->opening_balance_initialized_at === null
            && $this->cashFlows()->exists();
    }
}
