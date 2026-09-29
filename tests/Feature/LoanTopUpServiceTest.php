<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\LoanCollateral;
use App\Models\LoanDisbursement;
use App\Models\LoanProcess;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\User;
use App\Services\LoanTopUpService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoanTopUpServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_member_can_submit_top_up_and_creates_new_contract(): void
    {
        $member = $this->createUser('member');
        $this->createUser('analyst_manager');

        $this->configureSettings();

        $oldLoan = $this->createEligibleLoan($member);

        $newLoan = app(LoanTopUpService::class)->submit(
            $member,
            [
                'top_up_amount' => 1000000,
                'term_months' => 12,
                'purpose_category' => 'consumer',
                'purpose_description' => 'Tambahan kebutuhan keluarga.',
                'work_unit' => 'Unit Baru',
                'position' => 'Tester',
                'employment_duration_years' => 3,
                'net_monthly_income' => 4500000,
                'other_monthly_income' => 500000,
                'declared_external_monthly_obligations' => 100000,
                'declared_external_obligations_note' => 'Tidak ada perubahan material.',
                'salary_slip' => 'private/test/top-up-salary-slip.pdf',
            ]
        );

        $this->assertNotSame(
            $oldLoan->id,
            $newLoan->id
        );

        $this->assertSame(
            'top_up',
            $newLoan->loan_type
        );

        $this->assertSame(
            $oldLoan->id,
            $newLoan->top_up_of_loan_id
        );

        $this->assertSame(
            1000000.00,
            (float) $newLoan->top_up_amount
        );

        $this->assertSame(
            2500000.00,
            (float) $newLoan->requested_amount
        );

        $this->assertSame(
            2500000.00,
            (float) $newLoan->submitted_requested_amount
        );

        $this->assertSame(
            500000.00,
            (float) $newLoan->top_up_minimum_amount_snapshot
        );

        $this->assertSame(
            5000000.00,
            (float) $newLoan->loan_maximum_amount_snapshot
        );

        $this->assertNull($newLoan->approved_amount);
        $this->assertSame('monthly', $newLoan->repayment_type);
        $this->assertSame('submitted', $newLoan->status);

        $this->assertDatabaseHas('loan_processes', [
            'loan_id' => $newLoan->id,
            'action' => 'top_up_submitted',
        ]);

        $this->assertDatabaseHas('activities', [
            'subject_type' => Loan::class,
            'subject_id' => $newLoan->id,
            'action' => 'loan_top_up_submitted',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => User::query()
                ->where('role', 'analyst_manager')
                ->value('id'),
            'reference_id' => $newLoan->id,
            'type' => 'loan_submitted',
        ]);
    }

    public function test_top_up_submission_requires_minimum_additional_amount(): void
    {
        $member = $this->createUser('member');

        $this->configureSettings();

        $this->createEligibleLoan($member);

        $this->expectExceptionMessage(
            'Nominal tambahan Top Up belum mencapai minimum yang ditetapkan.'
        );

        app(LoanTopUpService::class)->submit(
            $member,
            $this->baseSubmission([
                'top_up_amount' => 499999,
            ])
        );
    }

    public function test_top_up_submission_cannot_exceed_maximum_total_loan(): void
    {
        $member = $this->createUser('member');

        $this->configureSettings();

        $this->createEligibleLoan($member);

        $this->expectExceptionMessage(
            'Total kontrak baru melebihi batas maksimum pinjaman.'
        );

        app(LoanTopUpService::class)->submit(
            $member,
            $this->baseSubmission([
                'top_up_amount' => 3500001,
            ])
        );
    }

    public function test_top_up_submission_is_blocked_when_old_loan_is_not_eligible(): void
    {
        $member = $this->createUser('member');

        $this->configureSettings();

        $loan = $this->createEligibleLoan($member);

        $installment = Installment::query()
            ->where('loan_id', $loan->id)
            ->where('installment_number', 1)
            ->firstOrFail();

        $installment->update([
            'status' => 'pending',
            'paid_at' => null,
        ]);

        $this->expectExceptionMessage(
            'Pokok pinjaman lama yang telah dibayar belum mencapai minimal 50%.'
        );

        app(LoanTopUpService::class)->submit(
            $member,
            $this->baseSubmission()
        );
    }

    public function test_top_up_submission_creates_pending_collateral_above_25_million(): void
    {
        $member = $this->createUser('member');

        $this->setSetting('top_up_minimum_amount', 500000);
        $this->setSetting('loan_maximum_amount', 40000000);
        $this->setSetting('loan_capacity_threshold_percent', 40);
        $this->setSetting('loan_interest_rate', 8);

        $oldLoan = $this->createEligibleLoan($member, 40000000);

        $newLoan = app(LoanTopUpService::class)->submit(
            $member,
            $this->baseSubmission([
                'top_up_amount' => 6000000,
                'term_months' => 12,
            ])
        );

        $this->assertSame(
            26000000.00,
            (float) $newLoan->requested_amount
        );

        $this->assertDatabaseHas('loan_collaterals', [
            'loan_id' => $newLoan->id,
            'status' => 'pending',
            'type' => 'bpkb',
            'ownership_status' => 'self',
            'ownership_proof' => 'bpkb',
            'proof_file' => 'private/test/top-up-collateral.pdf',
        ]);
    }

    public function test_top_up_submission_requires_collateral_above_25_million(): void
    {
        $member = $this->createUser('member');

        $this->setSetting('top_up_minimum_amount', 500000);
        $this->setSetting('loan_maximum_amount', 40000000);
        $this->setSetting('loan_capacity_threshold_percent', 40);
        $this->setSetting('loan_interest_rate', 8);

        $this->createEligibleLoan($member, 40000000);

        $this->expectExceptionMessage(
            'Pengajuan Top Up di atas Rp25.000.000 wajib memiliki data agunan.'
        );

        app(LoanTopUpService::class)->submit(
            $member,
            $this->baseSubmission([
                'top_up_amount' => 6000000,
                'collateral' => null,
            ])
        );

        $this->assertDatabaseCount('loan_collaterals', 0);
    }

    public function test_top_up_submission_rejects_lump_sum_term_or_non_monthly(): void
    {
        $member = $this->createUser('member');
        $this->configureSettings();
        $this->createEligibleLoan($member);

        $this->expectExceptionMessage(
            'Jangka waktu Top Up harus 12, 24, 36, 48, atau 60 bulan.'
        );

        app(LoanTopUpService::class)->submit(
            $member,
            $this->baseSubmission([
                'term_months' => 6,
            ])
        );
    }

    public function test_top_up_submission_rolls_back_when_validation_fails_after_loan_creation(): void
    {
        $member = $this->createUser('member');
        $this->configureSettings();

        $this->setSetting('top_up_minimum_amount', 500000);
        $this->setSetting('loan_maximum_amount', 40000000);
        $this->setSetting('loan_capacity_threshold_percent', 40);
        $this->setSetting('loan_interest_rate', 8);

        $oldLoan = $this->createEligibleLoan($member, 40000000);

        try {
            app(LoanTopUpService::class)->submit(
                $member,
                $this->baseSubmission([
                    'top_up_amount' => 6000000,
                    'collateral' => [
                        'type' => 'bpkb',
                        'ownership_status' => 'self',
                        'ownership_proof' => 'bpkb',
                        'proof_file' => null,
                    ],
                ])
            );
            $this->fail('Expected submission to fail.');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString(
                'File bukti agunan wajib diisi',
                $e->getMessage()
            );
        }

        $this->assertSame(
            1,
            Loan::query()
                ->where('user_id', $member->id)
                ->count()
        );

        $this->assertSame(
            0,
            Loan::query()
                ->where('user_id', $member->id)
                ->where('id', '!=', $oldLoan->id)
                ->count()
        );

        $this->assertSame(
            0,
            LoanProcess::query()
                ->where('loan_id', $oldLoan->id)
                ->where('action', 'top_up_submitted')
                ->count()
        );
    }

    public function test_active_top_up_prevents_second_submission(): void
    {
        $member = $this->createUser('member');
        $this->configureSettings();

        $oldLoan = $this->createEligibleLoan($member);

        Loan::create([
            'code' => 'KOPKARMADA/ACTIVE-TOP-UP/' . uniqid(),
            'sequence_number' => ((int) Loan::max('sequence_number')) + 1,
            'user_id' => $member->id,
            'submitted_at' => now(),
            'loan_type' => 'top_up',
            'top_up_of_loan_id' => $oldLoan->id,
            'top_up_amount' => 1000000,
            'submitted_requested_amount' => 2500000,
            'top_up_minimum_amount_snapshot' => 500000,
            'loan_maximum_amount_snapshot' => 5000000,
            'requested_amount' => 2500000,
            'purpose_category' => 'consumer',
            'purpose_description' => 'Top Up aktif.',
            'term_months' => 12,
            'repayment_type' => 'monthly',
            'work_unit' => 'Unit Test',
            'position' => 'Tester',
            'employment_duration_years' => 1,
            'net_monthly_income' => 4000000,
            'other_monthly_income' => 0,
            'declared_external_monthly_obligations' => 0,
            'salary_slip' => 'private/test/salary.pdf',
            'interest_rate' => 8,
            'status' => 'submitted',
        ]);

        $this->expectExceptionMessage(
            'Masih terdapat pengajuan Top Up aktif untuk pinjaman ini.'
        );

        app(LoanTopUpService::class)->submit(
            $member,
            $this->baseSubmission()
        );
    }

    private function configureSettings(): void
    {
        $this->setSetting('top_up_minimum_amount', 500000);
        $this->setSetting('loan_maximum_amount', 5000000);
        $this->setSetting('loan_capacity_threshold_percent', 40);
        $this->setSetting('loan_interest_rate', 8);
    }

    private function setSetting(string $key, float $value): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => (string) $value,
                'description' => 'Test setting ' . $key,
            ]
        );
    }

    private function baseSubmission(array $overrides = []): array
    {
        return array_merge([
            'top_up_amount' => 1000000,
            'term_months' => 12,
            'purpose_category' => 'consumer',
            'purpose_description' => 'Tambahan kebutuhan keluarga.',
            'work_unit' => 'Unit Baru',
            'position' => 'Tester',
            'employment_duration_years' => 3,
            'net_monthly_income' => 4500000,
            'other_monthly_income' => 500000,
            'declared_external_monthly_obligations' => 100000,
            'salary_slip' => 'private/test/top-up-salary-slip.pdf',
            'collateral' => [
                'type' => 'bpkb',
                'description' => 'BPKB kendaraan.',
                'ownership_status' => 'self',
                'ownership_proof' => 'bpkb',
                'proof_file' => 'private/test/top-up-collateral.pdf',
            ],
        ], $overrides);
    }

    private function createUser(string $role): User
    {
        return User::create([
            'name' => ucfirst($role) . ' Top Up Service Test',
            'nickname' => 'ts_' . substr(uniqid(), 0, 10),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'L',
            'password' => Hash::make('password'),
            'role' => $role,
            'status' => 'active',
        ]);
    }

    private function createEligibleLoan(
        User $member,
        float $amount = 3000000
    ): Loan {
        $loan = Loan::create([
            'code' => 'KOPKARMADA/TOPUP-SUBMIT/' . uniqid(),
            'sequence_number' => ((int) Loan::max('sequence_number')) + 1,
            'user_id' => $member->id,
            'submitted_at' => now()->subMonths(3),
            'loan_type' => 'regular',
            'requested_amount' => $amount,
            'purpose_category' => 'consumer',
            'purpose_description' => 'Pinjaman lama untuk pengujian Top Up.',
            'term_months' => 12,
            'repayment_type' => 'monthly',
            'monthly_installment' => 280000,
            'work_unit' => 'Unit Test',
            'position' => 'Tester',
            'employment_duration_years' => 2,
            'net_monthly_income' => 4000000,
            'other_monthly_income' => 500000,
            'declared_external_monthly_obligations' => 100000,
            'salary_slip' => 'private/test/salary-slip.pdf',
            'interest_rate' => 8,
            'approved_amount' => $amount,
            'status' => 'disbursed',
        ]);

        $treasurer = $this->createUser('treasurer');

        $account = Account::create([
            'name' => 'Top Up Submit Account',
            'type' => 'bank',
            'bank_name' => 'Bank Test',
            'account_number' => 'TUS-' . uniqid(),
            'account_name' => 'KOPKARMADA TEST',
            'opening_balance' => $amount + 1000000,
            'is_active' => true,
        ]);

        LoanDisbursement::create([
            'loan_id' => $loan->id,
            'treasurer_id' => $treasurer->id,
            'account_id' => $account->id,
            'amount' => $amount,
            'disbursed_at' => now()->subMonths(3),
        ]);

        Installment::create([
            'loan_id' => $loan->id,
            'installment_number' => 1,
            'due_date' => now()->subMonth()->startOfDay(),
            'principal_amount' => $amount / 2,
            'interest_amount' => 0,
            'penalty_amount' => 0,
            'total_amount' => $amount / 2,
            'paid_at' => now()->subWeeks(2),
            'status' => 'paid',
        ]);

        Installment::create([
            'loan_id' => $loan->id,
            'installment_number' => 2,
            'due_date' => now()->addMonth()->startOfDay(),
            'principal_amount' => $amount / 2,
            'interest_amount' => 0,
            'penalty_amount' => 0,
            'total_amount' => $amount / 2,
            'paid_at' => null,
            'status' => 'pending',
        ]);

        return $loan;
    }
}
