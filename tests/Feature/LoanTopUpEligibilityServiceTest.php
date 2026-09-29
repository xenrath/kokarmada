<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\LoanDisbursement;
use App\Models\Setting;
use App\Models\User;
use App\Services\LoanTopUpEligibilityService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoanTopUpEligibilityServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_eligible_when_half_of_principal_is_paid_and_no_arrears_exist(): void
    {
        $member = $this->createUser();
        $this->setSetting('top_up_minimum_amount', 500000);
        $this->setSetting('loan_maximum_amount', 5000000);
        $this->setSetting('loan_capacity_threshold_percent', 40);

        $loan = $this->createActiveMonthlyLoan($member, 3000000);

        $this->createInstallment($loan, 1, Carbon::yesterday(), 1500000, 'paid');
        $this->createInstallment($loan, 2, Carbon::tomorrow(), 1500000, 'pending');

        $result = app(LoanTopUpEligibilityService::class)
            ->evaluate($member);

        $this->assertTrue($result['eligible']);
        $this->assertSame('eligible', $result['reason_code'] ?? 'eligible');
        $this->assertSame(1500000.00, $result['principal_paid']);
        $this->assertSame(1500000.00, $result['outstanding_principal']);
        $this->assertSame(50.00, $result['principal_repayment_percent']);
        $this->assertSame(280000.00, $result['old_monthly_installment']);
        $this->assertSame(3500000.00, $result['maximum_additional_amount']);
        $this->assertFalse($result['has_overdue_installment']);
        $this->assertFalse($result['has_active_top_up']);

        $this->assertSame(5000000.00, $result['financial']['available_income']);
        $this->assertSame(40.00, $result['financial']['capacity_threshold_percent']);
        $this->assertSame(3200000.00, $result['financial']['capacity_limit_amount']);
    }

    public function test_not_eligible_when_principal_repayment_is_below_50_percent(): void
    {
        $member = $this->createUser();
        $this->configureTopUpSettings();

        $loan = $this->createActiveMonthlyLoan($member, 3000000);

        $this->createInstallment($loan, 1, Carbon::yesterday(), 1400000, 'paid');
        $this->createInstallment($loan, 2, Carbon::tomorrow(), 1600000, 'pending');

        $result = app(LoanTopUpEligibilityService::class)
            ->evaluate($member);

        $this->assertFalse($result['eligible']);
        $this->assertSame(
            'principal_repayment_below_50_percent',
            $result['reason_code']
        );
        $this->assertSame(46.67, $result['principal_repayment_percent']);
    }

    public function test_not_eligible_when_overdue_installment_exists(): void
    {
        $member = $this->createUser();
        $this->configureTopUpSettings();

        $loan = $this->createActiveMonthlyLoan($member, 3000000);

        $this->createInstallment($loan, 1, Carbon::yesterday(), 1500000, 'paid');
        $this->createInstallment(
            $loan,
            2,
            Carbon::yesterday(),
            1500000,
            'pending'
        );

        $result = app(LoanTopUpEligibilityService::class)
            ->evaluate($member);

        $this->assertFalse($result['eligible']);
        $this->assertSame(
            'has_overdue_installment',
            $result['reason_code']
        );
        $this->assertTrue($result['has_overdue_installment']);
    }

    public function test_not_eligible_when_active_top_up_application_exists(): void
    {
        $member = $this->createUser();
        $this->configureTopUpSettings();

        $loan = $this->createActiveMonthlyLoan($member, 3000000);

        $this->createInstallment($loan, 1, Carbon::yesterday(), 1500000, 'paid');
        $this->createInstallment($loan, 2, Carbon::tomorrow(), 1500000, 'pending');

        Loan::create([
            'code' => 'KOPKARMADA/TOPUP/ACTIVE/' . uniqid(),
            'sequence_number' => ((int) Loan::max('sequence_number')) + 1,
            'user_id' => $member->id,
            'submitted_at' => now(),
            'loan_type' => 'top_up',
            'top_up_of_loan_id' => $loan->id,
            'top_up_amount' => 1000000,
            'submitted_requested_amount' => 2500000,
            'top_up_minimum_amount_snapshot' => 500000,
            'loan_maximum_amount_snapshot' => 5000000,
            'requested_amount' => 2500000,
            'purpose_category' => 'consumer',
            'purpose_description' => 'Pengujian pengajuan Top Up aktif.',
            'term_months' => 12,
            'repayment_type' => 'monthly',
            'work_unit' => 'Unit Test',
            'position' => 'Tester',
            'employment_duration_years' => 1,
            'other_monthly_income' => 0,
            'net_monthly_income' => 6000000,
            'declared_external_monthly_obligations' => 0,
            'salary_slip' => 'private/test/salary-slip.pdf',
            'interest_rate' => 8,
            'status' => 'under_analysis',
        ]);

        $result = app(LoanTopUpEligibilityService::class)
            ->evaluate($member);

        $this->assertFalse($result['eligible']);
        $this->assertSame(
            'has_active_top_up',
            $result['reason_code']
        );
        $this->assertTrue($result['has_active_top_up']);
    }

    public function test_not_eligible_when_required_settings_are_missing(): void
    {
        $member = $this->createUser();

        $loan = $this->createActiveMonthlyLoan($member, 3000000);

        $this->createInstallment($loan, 1, Carbon::yesterday(), 1500000, 'paid');
        $this->createInstallment($loan, 2, Carbon::tomorrow(), 1500000, 'pending');

        $result = app(LoanTopUpEligibilityService::class)
            ->evaluate($member);

        $this->assertFalse($result['eligible']);
        $this->assertContains(
            $result['reason_code'],
            [
                'minimum_setting_unconfigured',
                'maximum_setting_unconfigured',
            ]
        );
    }

    public function test_top_up_rejects_lump_sum_old_loan(): void
    {
        $member = $this->createUser();
        $this->configureTopUpSettings();

        $loan = $this->createActiveMonthlyLoan(
            $member,
            3000000,
            'lump_sum'
        );

        $result = app(LoanTopUpEligibilityService::class)
            ->evaluate($member);

        $this->assertFalse($result['eligible']);
        $this->assertSame(
            'not_monthly',
            $result['reason_code']
        );
    }

    private function configureTopUpSettings(): void
    {
        $this->setSetting('top_up_minimum_amount', 500000);
        $this->setSetting('loan_maximum_amount', 5000000);
        $this->setSetting('loan_capacity_threshold_percent', 40);
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

    private function createUser(): User
    {
        return User::create([
            'name' => 'Top Up Eligibility Test',
            'nickname' => 'tu_' . substr(uniqid(), 0, 10),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'L',
            'password' => Hash::make('password'),
            'role' => 'member',
            'status' => 'active',
        ]);
    }

    private function createActiveMonthlyLoan(
        User $member,
        float $amount,
        string $repaymentType = 'monthly'
    ): Loan {
        $sequenceNumber = ((int) Loan::max('sequence_number')) + 1;

        $loan = Loan::create([
            'code' => 'KOPKARMADA/TOPUP-ELIGIBILITY/' . $sequenceNumber,
            'sequence_number' => $sequenceNumber,
            'user_id' => $member->id,
            'submitted_at' => now()->subMonths(3),
            'loan_type' => 'regular',
            'requested_amount' => $amount,
            'purpose_category' => 'consumer',
            'purpose_description' => 'Pengujian eligibility Top Up.',
            'term_months' => 12,
            'repayment_type' => $repaymentType,
            'monthly_installment' => $repaymentType === 'monthly'
                ? 280000
                : null,
            'work_unit' => 'Unit Test',
            'position' => 'Tester',
            'employment_duration_years' => 2,
            'other_monthly_income' => 500000,
            'net_monthly_income' => 4000000,
            'declared_external_monthly_obligations' => 100000,
            'salary_slip' => 'private/test/salary-slip.pdf',
            'interest_rate' => 8,
            'approved_amount' => $amount,
            'status' => 'disbursed',
        ]);

        $treasurer = $this->createTreasurer();
        $account = Account::create([
            'name' => 'Top Up Eligibility Account',
            'type' => 'bank',
            'bank_name' => 'Bank Test',
            'account_number' => 'TU-' . uniqid(),
            'account_name' => 'KOPKARMADA TEST',
            'opening_balance' => 10000000,
            'is_active' => true,
        ]);

        LoanDisbursement::create([
            'loan_id' => $loan->id,
            'treasurer_id' => $treasurer->id,
            'account_id' => $account->id,
            'amount' => $amount,
            'disbursed_at' => now()->subMonths(3),
            'notes' => 'Test eligibility Top Up.',
        ]);

        return $loan;
    }

    private function createInstallment(
        Loan $loan,
        int $number,
        Carbon $dueDate,
        float $principal,
        string $status
    ): Installment {
        return Installment::create([
            'loan_id' => $loan->id,
            'installment_number' => $number,
            'due_date' => $dueDate,
            'principal_amount' => $principal,
            'interest_amount' => 0,
            'penalty_amount' => 0,
            'total_amount' => $principal,
            'paid_at' => $status === 'paid' ? now() : null,
            'status' => $status,
        ]);
    }

    private function createTreasurer(): User
    {
        return User::create([
            'name' => 'Treasurer Top Up Eligibility',
            'nickname' => 'tr_' . substr(uniqid(), 0, 10),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'L',
            'password' => Hash::make('password'),
            'role' => 'treasurer',
            'status' => 'active',
        ]);
    }
}
