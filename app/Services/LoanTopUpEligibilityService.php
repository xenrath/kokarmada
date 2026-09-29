<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;

class LoanTopUpEligibilityService
{
    public const ACTIVE_TOP_UP_STATUSES = [
        'submitted',
        'under_analysis',
        'waiting_treasurer_review',
        'waiting_chairman_approval',
        'approved',
    ];

    public function evaluate(User $member): array
    {
        $loan = $this->latestActiveLoan($member);

        $minimumAdditionalAmount = $this->settingAmount(
            'top_up_minimum_amount'
        );

        $maximumLoanAmount = $this->settingAmount(
            'loan_maximum_amount'
        );

        $result = [
            'eligible' => false,
            'loan' => $loan,
            'minimum_additional_amount' => $minimumAdditionalAmount,
            'maximum_loan_amount' => $maximumLoanAmount,
            'maximum_additional_amount' => null,
            'principal_paid' => 0.0,
            'outstanding_principal' => 0.0,
            'principal_repayment_percent' => 0.0,
            'old_monthly_installment' => 0.0,
            'has_overdue_installment' => false,
            'has_active_top_up' => false,
            'financial' => $loan
                ? $this->financialCapacity($loan)
                : null,
            'reason_code' => null,
            'reason' => null,
        ];

        if (! $loan) {
            return $this->withReason(
                $result,
                'no_active_loan',
                'Tidak ada pinjaman aktif yang dapat dijadikan dasar Top Up.'
            );
        }

        if ($loan->repayment_type !== 'monthly') {
            return $this->withReason(
                $result,
                'not_monthly',
                'Top Up hanya dapat dilakukan dari pinjaman dengan angsuran bulanan.'
            );
        }

        if ($minimumAdditionalAmount === null) {
            return $this->withReason(
                $result,
                'minimum_setting_unconfigured',
                'Pengaturan minimum Top Up belum dikonfigurasi.'
            );
        }

        if ($maximumLoanAmount === null) {
            return $this->withReason(
                $result,
                'maximum_setting_unconfigured',
                'Pengaturan maksimum pinjaman belum dikonfigurasi.'
            );
        }

        if ($minimumAdditionalAmount <= 0) {
            return $this->withReason(
                $result,
                'minimum_setting_invalid',
                'Pengaturan minimum Top Up tidak valid.'
            );
        }

        if ($maximumLoanAmount <= 0) {
            return $this->withReason(
                $result,
                'maximum_setting_invalid',
                'Pengaturan maksimum pinjaman tidak valid.'
            );
        }

        $principalPaid = $loan->installments
            ->where('status', 'paid')
            ->sum(fn ($installment) => (float) $installment->principal_amount);

        $outstandingPrincipal = $loan->installments
            ->reject(
                fn ($installment) => in_array(
                    $installment->status,
                    ['paid', 'cancelled_by_topup'],
                    true
                )
            )
            ->sum(fn ($installment) => (float) $installment->principal_amount);

        $scheduledPrincipal = $loan->installments
            ->reject(
                fn ($installment) => $installment->status === 'cancelled_by_topup'
            )
            ->sum(fn ($installment) => (float) $installment->principal_amount);

        if ($scheduledPrincipal > 0) {
            $principalRepaymentPercent = round(
                ($principalPaid / $scheduledPrincipal) * 100,
                2
            );
        } else {
            $principalRepaymentPercent = 0.0;
        }

        $hasOverdueInstallment = $loan->installments
            ->contains(function ($installment) {
                return ! in_array(
                    $installment->status,
                    ['paid', 'cancelled_by_topup'],
                    true
                )
                    && $installment->due_date->isBefore(
                        Carbon::today()
                    );
            });

        $hasActiveTopUp = $loan->topUps()
            ->whereIn('status', self::ACTIVE_TOP_UP_STATUSES)
            ->exists();

        $result['principal_paid'] = round($principalPaid, 2);
        $result['outstanding_principal'] = round($outstandingPrincipal, 2);
        $result['principal_repayment_percent'] = $principalRepaymentPercent;
        $result['old_monthly_installment'] = (float) ($loan->monthly_installment ?? 0);
        $result['has_overdue_installment'] = $hasOverdueInstallment;
        $result['has_active_top_up'] = $hasActiveTopUp;
        $result['maximum_additional_amount'] = round(
            max(0, $maximumLoanAmount - $outstandingPrincipal),
            2
        );

        if ($principalRepaymentPercent < 50) {
            return $this->withReason(
                $result,
                'principal_repayment_below_50_percent',
                'Pokok pinjaman lama yang telah dibayar belum mencapai minimal 50%.'
            );
        }

        if ($hasOverdueInstallment) {
            return $this->withReason(
                $result,
                'has_overdue_installment',
                'Top Up belum dapat diajukan karena masih terdapat tunggakan angsuran.'
            );
        }

        if ($hasActiveTopUp) {
            return $this->withReason(
                $result,
                'has_active_top_up',
                'Masih terdapat pengajuan Top Up aktif untuk pinjaman ini.'
            );
        }

        if ($outstandingPrincipal <= 0) {
            return $this->withReason(
                $result,
                'no_outstanding_principal',
                'Pinjaman lama tidak memiliki sisa pokok untuk direfinancing.'
            );
        }

        if ($maximumLoanAmount <= $outstandingPrincipal) {
            return $this->withReason(
                $result,
                'maximum_below_outstanding',
                'Batas maksimum pinjaman tidak cukup untuk membentuk kontrak Top Up.'
            );
        }

        if ($minimumAdditionalAmount > $result['maximum_additional_amount']) {
            return $this->withReason(
                $result,
                'minimum_exceeds_maximum_additional',
                'Nominal minimum Top Up melebihi kapasitas tambahan yang tersedia dalam batas maksimum pinjaman.'
            );
        }

        $result['eligible'] = true;

        return $result;
    }

    public function financialCapacity(Loan $loan): array
    {
        $netSalary = $loan->verified_net_monthly_income !== null
            ? (float) $loan->verified_net_monthly_income
            : (float) ($loan->net_monthly_income ?? 0);

        $otherIncome = $loan->verified_other_monthly_income !== null
            ? (float) $loan->verified_other_monthly_income
            : (float) ($loan->other_monthly_income ?? 0);

        $externalObligations = $loan->verified_external_monthly_obligations !== null
            ? (float) $loan->verified_external_monthly_obligations
            : (float) ($loan->declared_external_monthly_obligations ?? 0);

        $existingCoopInstallment = (float) ($loan->monthly_installment ?? 0);

        $availableIncome =
            $netSalary
            + $otherIncome
            - $externalObligations
            - $existingCoopInstallment;

        $thresholdPercent = $this->settingAmount(
            'loan_capacity_threshold_percent'
        );

        $capacityLimitAmount = null;

        if ($thresholdPercent !== null) {
            $capacityLimitAmount = round(
                (
                    $netSalary
                    + $otherIncome
                ) * ($thresholdPercent / 100),
                2
            );
        }

        return [
            'net_monthly_income' => round($netSalary, 2),
            'other_monthly_income' => round($otherIncome, 2),
            'external_monthly_obligations' => round($externalObligations, 2),
            'existing_coop_installment' => round($existingCoopInstallment, 2),
            'available_income' => round($availableIncome, 2),
            'capacity_threshold_percent' => $thresholdPercent,
            'capacity_limit_amount' => $capacityLimitAmount,
        ];
    }

    private function latestActiveLoan(User $member): ?Loan
    {
        return Loan::query()
            ->where('user_id', $member->id)
            ->where('status', 'disbursed')
            ->whereHas('disbursement')
            ->with([
                'disbursement',
                'installments',
            ])
            ->get()
            ->sortByDesc(
                fn (Loan $loan) => optional(
                    $loan->disbursement?->disbursed_at
                )->timestamp ?? 0
            )
            ->first();
    }

    private function settingAmount(string $key): ?float
    {
        $value = Setting::query()
            ->where('key', $key)
            ->value('value');

        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        if (! is_numeric($value)) {
            return null;
        }

        return round((float) $value, 2);
    }

    private function withReason(
        array $result,
        string $reasonCode,
        string $reason
    ): array {
        $result['reason_code'] = $reasonCode;
        $result['reason'] = $reason;

        return $result;
    }
}
