<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Loan;
use App\Models\LoanCollateral;
use App\Models\LoanProcess;
use App\Models\LoanDisbursement;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class LoanTopUpService
{
    public function submit(User $member, array $data): Loan
    {
        if (! $member->isMember() || ! $member->isActive()) {
            throw new RuntimeException(
                'Akun tidak memiliki hak untuk mengajukan Top Up.'
            );
        }

        return DB::transaction(function () use ($member, $data) {
            $member = User::query()
                ->whereKey($member->id)
                ->lockForUpdate()
                ->firstOrFail();

            $eligibilityService = app(
                LoanTopUpEligibilityService::class
            );

            $eligibility = $eligibilityService->evaluate($member);

            if (! $eligibility['eligible']) {
                throw new RuntimeException(
                    $eligibility['reason']
                    ?? 'Pinjaman belum memenuhi syarat Top Up.'
                );
            }

            /** @var Loan $oldLoan */
            $oldLoan = Loan::query()
                ->whereKey($eligibility['loan']->id)
                ->lockForUpdate()
                ->firstOrFail();

            $eligibility = $eligibilityService->evaluate(
                $member->fresh()
            );

            if (
                ! $eligibility['eligible']
                || ! $eligibility['loan']
                || $eligibility['loan']->id !== $oldLoan->id
            ) {
                throw new RuntimeException(
                    'Kelayakan Top Up berubah. Silakan periksa kembali pinjaman aktif Anda.'
                );
            }

            $topUpAmount = $this->money(
                $data['top_up_amount'] ?? null
            );

            $minimumAdditionalAmount =
                (float) $eligibility['minimum_additional_amount'];

            $maximumLoanAmount =
                (float) $eligibility['maximum_loan_amount'];

            $outstandingPrincipal =
                (float) $eligibility['outstanding_principal'];

            if ($topUpAmount <= 0) {
                throw new RuntimeException(
                    'Nominal tambahan Top Up harus lebih dari Rp0.'
                );
            }

            if ($topUpAmount < $minimumAdditionalAmount) {
                throw new RuntimeException(
                    'Nominal tambahan Top Up belum mencapai minimum yang ditetapkan.'
                );
            }

            $requestedAmount =
                $outstandingPrincipal + $topUpAmount;

            if ($requestedAmount > $maximumLoanAmount) {
                throw new RuntimeException(
                    'Total kontrak baru melebihi batas maksimum pinjaman.'
                );
            }

            $interestRate = $this->settingNumber(
                'loan_interest_rate'
            );

            if ($interestRate === null || $interestRate < 0) {
                throw new RuntimeException(
                    'Pengaturan suku bunga pinjaman belum dikonfigurasi dengan benar.'
                );
            }

            $termMonths = (int) (
                $data['term_months']
                ?? $oldLoan->term_months
            );

            if (
                ! in_array(
                    $termMonths,
                    [12, 24, 36, 48, 60],
                    true
                )
            ) {
                throw new RuntimeException(
                    'Jangka waktu Top Up harus 12, 24, 36, 48, atau 60 bulan.'
                );
            }

            $purposeCategory = $data['purpose_category']
                ?? $oldLoan->purpose_category;

            $businessType = $data['business_type']
                ?? $oldLoan->business_type;

            $businessTypeOther = trim(
                (string) (
                    $data['business_type_other']
                    ?? $oldLoan->business_type_other
                    ?? ''
                )
            );

            $purposeDescription = trim(
                (string) (
                    $data['purpose_description']
                    ?? $oldLoan->purpose_description
                )
            );

            $this->validatePurpose(
                $purposeCategory,
                $businessType,
                $businessTypeOther,
                $purposeDescription
            );

            $workUnit = trim(
                (string) (
                    $data['work_unit']
                    ?? $oldLoan->work_unit
                )
            );

            $position = trim(
                (string) (
                    $data['position']
                    ?? $oldLoan->position
                )
            );

            $employmentDurationYears = (int) (
                $data['employment_duration_years']
                ?? $oldLoan->employment_duration_years
            );

            if ($workUnit === '' || $position === '') {
                throw new RuntimeException(
                    'Unit kerja dan jabatan wajib diisi.'
                );
            }

            if ($employmentDurationYears < 0) {
                throw new RuntimeException(
                    'Lama bekerja tidak valid.'
                );
            }

            $netMonthlyIncome = $this->money(
                $data['net_monthly_income']
                ?? $oldLoan->net_monthly_income
            );

            $otherMonthlyIncome = $this->money(
                $data['other_monthly_income']
                ?? $oldLoan->other_monthly_income
            );

            $externalMonthlyObligations = $this->money(
                $data['declared_external_monthly_obligations']
                ?? $oldLoan->declared_external_monthly_obligations
                ?? 0
            );

            if (
                $netMonthlyIncome < 0
                || $otherMonthlyIncome < 0
                || $externalMonthlyObligations < 0
            ) {
                throw new RuntimeException(
                    'Data penghasilan dan kewajiban tidak boleh negatif.'
                );
            }

            $salarySlip = trim(
                (string) (
                    $data['salary_slip']
                    ?? ''
                )
            );

            if ($salarySlip === '') {
                throw new RuntimeException(
                    'Slip gaji terbaru wajib tersedia untuk pengajuan Top Up.'
                );
            }

            $sequenceNumber = ((int) Loan::query()
                ->lockForUpdate()
                ->max('sequence_number')) + 1;

            if ($sequenceNumber < 100) {
                $sequenceNumber = 100;
            }

            $code = $this->generateCode(
                $sequenceNumber
            );

            $loan = Loan::create([
                'code' => $code,
                'sequence_number' => $sequenceNumber,
                'user_id' => $member->id,
                'submitted_at' => now(),

                'loan_type' => 'top_up',
                'top_up_of_loan_id' => $oldLoan->id,
                'top_up_amount' => $topUpAmount,
                'submitted_requested_amount' => $requestedAmount,
                'top_up_minimum_amount_snapshot' =>
                    $minimumAdditionalAmount,
                'loan_maximum_amount_snapshot' =>
                    $maximumLoanAmount,

                'requested_amount' => $requestedAmount,

                'purpose_category' => $purposeCategory,
                'business_type' =>
                    $purposeCategory === 'business'
                        ? $businessType
                        : null,
                'business_type_other' =>
                    $purposeCategory === 'business'
                    && $businessType === 'other'
                        ? $businessTypeOther
                        : null,
                'purpose_description' => $purposeDescription,

                'term_months' => $termMonths,
                'repayment_type' => 'monthly',
                'monthly_installment' => null,

                'work_unit' => $workUnit,
                'position' => $position,
                'employment_duration_years' =>
                    $employmentDurationYears,

                'other_monthly_income' =>
                    $otherMonthlyIncome,

                'net_monthly_income' =>
                    $netMonthlyIncome,

                'declared_external_monthly_obligations' =>
                    $externalMonthlyObligations,

                'declared_external_obligations_note' =>
                    $data['declared_external_obligations_note'] ?? null,

                'declared_external_obligations_proof' =>
                    $data['declared_external_obligations_proof'] ?? null,

                'other_income_proof' =>
                    $data['other_income_proof'] ?? null,

                'salary_slip' => $salarySlip,

                'interest_rate' => $interestRate,
                'approved_amount' => null,

                'verified_net_monthly_income' => null,
                'verified_other_monthly_income' => null,
                'verified_external_monthly_obligations' => null,

                'status' => 'submitted',
            ]);

            $this->createCollateralIfRequired(
                $loan,
                $requestedAmount,
                $data['collateral'] ?? null
            );

            LoanProcess::create([
                'loan_id' => $loan->id,
                'user_id' => $member->id,
                'role' => 'member',
                'action' => 'top_up_submitted',
                'notes' =>
                    'Anggota mengajukan Top Up sebesar Rp'
                    . number_format(
                        $topUpAmount,
                        0,
                        ',',
                        '.'
                    )
                    . ' dengan total kontrak baru Rp'
                    . number_format(
                        $requestedAmount,
                        0,
                        ',',
                        '.'
                    )
                    . '.',
            ]);

            Activity::create([
                'user_id' => $member->id,
                'action' => 'loan_top_up_submitted',
                'subject_type' => Loan::class,
                'subject_id' => $loan->id,
                'description' =>
                    'Pengajuan Top Up '
                    . $loan->code
                    . ' dengan total kontrak Rp'
                    . number_format(
                        $requestedAmount,
                        0,
                        ',',
                        '.'
                    )
                    . '.',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            $analystManager = User::query()
                ->where('role', 'analyst_manager')
                ->where('status', 'active')
                ->first();

            if ($analystManager) {
                Notification::create([
                    'user_id' => $analystManager->id,
                    'type' => 'loan_submitted',
                    'title' => 'Pengajuan Top Up Baru',
                    'message' =>
                        'Pengajuan Top Up '
                        . $loan->code
                        . ' menunggu analisis.',
                    'reference_type' => Loan::class,
                    'reference_id' => $loan->id,
                ]);
            }

            return $loan;
        });
    }

    private function createCollateralIfRequired(
        Loan $loan,
        float $requestedAmount,
        ?array $collateral
    ): void {
        if ($requestedAmount <= 25000000) {
            return;
        }

        if (! $collateral) {
            throw new RuntimeException(
                'Pengajuan Top Up di atas Rp25.000.000 wajib memiliki data agunan.'
            );
        }

        foreach ([
            'type' => 'Jenis agunan',
            'ownership_status' => 'Status kepemilikan',
            'ownership_proof' => 'Bukti kepemilikan',
            'proof_file' => 'File bukti agunan',
        ] as $key => $label) {
            if (
                ! array_key_exists($key, $collateral)
                || blank($collateral[$key])
            ) {
                throw new RuntimeException(
                    $label
                    . ' wajib diisi untuk pengajuan di atas Rp25.000.000.'
                );
            }
        }

        LoanCollateral::create([
            'loan_id' => $loan->id,
            'type' => $collateral['type'],
            'description' => $collateral['description'] ?? null,
            'ownership_status' => $collateral['ownership_status'],
            'ownership_proof' => $collateral['ownership_proof'],
            'proof_file' => $collateral['proof_file'],
            'status' => 'pending',
        ]);
    }

    private function validatePurpose(
        string $purposeCategory,
        ?string $businessType,
        string $businessTypeOther,
        string $purposeDescription
    ): void {
        if (! in_array(
            $purposeCategory,
            ['business', 'consumer'],
            true
        )) {
            throw new RuntimeException(
                'Kategori tujuan pinjaman tidak valid.'
            );
        }

        if ($purposeCategory === 'business') {
            if (! in_array(
                $businessType,
                ['trade', 'agriculture', 'service', 'other'],
                true
            )) {
                throw new RuntimeException(
                    'Jenis usaha tidak valid.'
                );
            }

            if (
                $businessType === 'other'
                && $businessTypeOther === ''
            ) {
                throw new RuntimeException(
                    'Jenis usaha lainnya wajib diisi.'
                );
            }
        }

        if (
            $purposeCategory === 'consumer'
            && $businessType !== null
            && $businessType !== ''
        ) {
            throw new RuntimeException(
                'Jenis usaha tidak diperlukan untuk pinjaman konsumtif.'
            );
        }

        if ($purposeDescription === '') {
            throw new RuntimeException(
                'Tujuan pengajuan kredit wajib diisi.'
            );
        }
    }

    private function money(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0;
        }

        if (is_numeric($value)) {
            return max(0, (float) $value);
        }

        $value = str_replace(
            '.',
            '',
            (string) $value
        );

        $value = str_replace(
            ',',
            '.',
            $value
        );

        return max(0, (float) $value);
    }

    private function settingNumber(string $key): ?float
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

        return (float) $value;
    }

    private function generateCode(int $sequenceNumber): string
    {
        $month = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ][now()->month];

        return 'KOPKARMADA/'
            . $sequenceNumber
            . '/'
            . $month
            . '/'
            . now()->year;
    }
}
