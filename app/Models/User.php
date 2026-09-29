<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nickname',
        'phone',
        'gender',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function memberProfile()
    {
        return $this->hasOne(MemberProfile::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function loanAnalyses()
    {
        return $this->hasMany(LoanAnalysis::class, 'analyst_id');
    }

    public function loanTreasurerReviews()
    {
        return $this->hasMany(LoanTreasurerReview::class, 'treasurer_id');
    }

    public function loanProcesses()
    {
        return $this->hasMany(LoanProcess::class);
    }

    public function uploadedLoanDocuments()
    {
        return $this->hasMany(LoanDocument::class, 'uploaded_by');
    }

    public function verifiedLoanDocuments()
    {
        return $this->hasMany(LoanDocument::class, 'verified_by');
    }

    public function loanDisbursements()
    {
        return $this->hasMany(LoanDisbursement::class, 'treasurer_id');
    }

    public function savings()
    {
        return $this->hasMany(Saving::class);
    }

    public function savingsTransactions()
    {
        return $this->hasMany(SavingsTransaction::class);
    }

    public function calculatedShu()
    {
        return $this->hasMany(Shu::class, 'calculated_by');
    }

    public function shuDistributions()
    {
        return $this->hasMany(ShuDistribution::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function isRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isDeveloper(): bool
    {
        return $this->role === 'developer';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isMember(): bool
    {
        return $this->role === 'member';
    }

    public function isAnalystManager(): bool
    {
        return $this->role === 'analyst_manager';
    }

    public function isTreasurer(): bool
    {
        return $this->role === 'treasurer';
    }

    public function isChairman(): bool
    {
        return $this->role === 'chairman';
    }

    public function isSecretary(): bool
    {
        return $this->role === 'secretary';
    }

    public function isSupervisor(): bool
    {
        return $this->role === 'supervisor';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
