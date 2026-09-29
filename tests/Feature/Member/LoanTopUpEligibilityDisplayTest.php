<?php

namespace Tests\Feature\Member;

use App\Models\Account;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\LoanDisbursement;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoanTopUpEligibilityDisplayTest extends TestCase
{
    use DatabaseTransactions;

    public function test_member_sees_top_up_eligibility_on_latest_active_loan_detail(): void
    {
        $member = $this->createUser();

        $this->setSetting('top_up_minimum_amount', 500000);
        $this->setSetting('loan_maximum_amount', 5000000);
        $this->setSetting('loan_capacity_threshold_percent', 40);

        $loan = $this->createLoan($member);

        Installment::create([
            'loan_id' => $loan->id,
            'installment_number' => 1,
            'due_date' => now()->subMonth()->startOfDay(),
            'principal_amount' => 1500000,
            'interest_amount' => 120000,
            'penalty_amount' => 0,
            'total_amount' => 1620000,
            'paid_at' => now(),
            'status' => 'paid',
        ]);

        Installment::create([
            'loan_id' => $loan->id,
            'installment_number' => 2,
            'due_date' => now()->addMonth()->startOfDay(),
            'principal_amount' => 1500000,
            'interest_amount' => 120000,
            'penalty_amount' => 0,
            'total_amount' => 1620000,
            'paid_at' => null,
            'status' => 'pending',
        ]);

        $this->actingAs($member);

        $response = $this->get(
            route('member.loans.show', $loan)
        );

        $response->assertOk();
        $response->assertSee('Status Kelayakan Top Up');
        $response->assertSee('Memenuhi Syarat');
        $response->assertSee('Pokok Sudah Dibayar');
        $response->assertSee('Sisa Pokok');
        $response->assertSee('Persentase Pokok Terbayar');
        $response->assertSee('Rp1.500.000');
        $response->assertSee('50,00%');
        $response->assertSee('Rp3.500.000');
        $response->assertSee('Informasi Kapasitas Keuangan');
        $response->assertSee('Rp4.120.000');
    }

    public function test_member_sees_reason_when_latest_active_loan_is_not_eligible(): void
    {
        $member = $this->createUser();

        $this->configureSettings();

        $loan = $this->createLoan($member);

        Installment::create([
            'loan_id' => $loan->id,
            'installment_number' => 1,
            'due_date' => now()->addMonth()->startOfDay(),
            'principal_amount' => 1000000,
            'interest_amount' => 80000,
            'penalty_amount' => 0,
            'total_amount' => 1080000,
            'status' => 'pending',
        ]);

        Installment::create([
            'loan_id' => $loan->id,
            'installment_number' => 2,
            'due_date' => now()->addMonths(2)->startOfDay(),
            'principal_amount' => 2000000,
            'interest_amount' => 160000,
            'penalty_amount' => 0,
            'total_amount' => 2160000,
            'status' => 'pending',
        ]);

        $this->actingAs($member);

        $response = $this->get(
            route('member.loans.show', $loan)
        );

        $response->assertOk();
        $response->assertSee('Status Kelayakan Top Up');
        $response->assertSee('Belum Memenuhi Syarat');
        $response->assertSee(
            'Pokok pinjaman lama yang telah dibayar belum mencapai minimal 50%.'
        );
    }

    private function configureSettings(): void
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
            'name' => 'Member Top Up Display Test',
            'nickname' => 'mud_' . substr(uniqid(), 0, 10),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'L',
            'password' => Hash::make('password'),
            'role' => 'member',
            'status' => 'active',
        ]);
    }

    private function createLoan(User $member): Loan
    {
        $sequenceNumber = ((int) Loan::max('sequence_number')) + 1;

        $loan = Loan::create([
            'code' => 'KOPKARMADA/TOPUP-DISPLAY/' . $sequenceNumber,
            'sequence_number' => $sequenceNumber,
            'user_id' => $member->id,
            'submitted_at' => now()->subMonths(3),
            'loan_type' => 'regular',
            'requested_amount' => 3000000,
            'purpose_category' => 'consumer',
            'purpose_description' => 'Pengujian tampilan eligibility Top Up.',
            'term_months' => 12,
            'repayment_type' => 'monthly',
            'monthly_installment' => 280000,
            'work_unit' => 'Unit Test',
            'position' => 'Tester',
            'employment_duration_years' => 2,
            'other_monthly_income' => 500000,
            'net_monthly_income' => 4000000,
            'declared_external_monthly_obligations' => 100000,
            'salary_slip' => 'private/test/salary-slip.pdf',
            'interest_rate' => 8,
            'approved_amount' => 3000000,
            'status' => 'disbursed',
        ]);

        $treasurer = User::create([
            'name' => 'Treasurer Top Up Display',
            'nickname' => 'tud_' . substr(uniqid(), 0, 10),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'L',
            'password' => Hash::make('password'),
            'role' => 'treasurer',
            'status' => 'active',
        ]);

        $account = Account::create([
            'name' => 'Top Up Display Account',
            'type' => 'bank',
            'bank_name' => 'Bank Test',
            'account_number' => 'TUD-' . uniqid(),
            'account_name' => 'KOPKARMADA TEST',
            'opening_balance' => 10000000,
            'is_active' => true,
        ]);

        LoanDisbursement::create([
            'loan_id' => $loan->id,
            'treasurer_id' => $treasurer->id,
            'account_id' => $account->id,
            'amount' => 3000000,
            'disbursed_at' => now()->subMonths(3),
            'notes' => 'Test display eligibility Top Up.',
        ]);

        return $loan;
    }
}
