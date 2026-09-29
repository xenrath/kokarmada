<?php

namespace Tests\Feature\Treasurer;

use App\Models\Account;
use App\Models\Loan;
use App\Models\LoanDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountSelectionTest extends TestCase
{
    use DatabaseTransactions;

    private function createUser(
        string $name,
        string $nickname,
        string $gender,
        string $role
    ): User {
        return User::create([
            'name' => $name,
            'nickname' => $nickname . '_' . uniqid(),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => $gender,
            'password' => Hash::make('password'),
            'role' => $role,
            'status' => 'active',
        ]);
    }

    private function createApprovedLoan(
        User $member,
        User $analyst,
        User $secretary
    ): Loan {
        $sequenceNumber = ((int) Loan::max('sequence_number')) + 1;

        $loan = Loan::create([
            'code' => 'KOPKARMADA/' .
                $sequenceNumber .
                '/IX/2026',

            'sequence_number' => $sequenceNumber,
            'user_id' => $member->id,
            'submitted_at' => now(),

            'requested_amount' => 1800000,

            'purpose_category' => 'consumer',
            'business_type' => null,
            'business_type_other' => null,
            'purpose_description' => 'Pengujian pemilihan rekening Treasurer.',

            'term_months' => 12,
            'repayment_type' => 'monthly',
            'monthly_installment' => 162000,

            'work_unit' => 'Unit Test',
            'position' => 'Tester',
            'employment_duration_years' => 1,

            'other_monthly_income' => 0,
            'other_income_proof' => null,

            'net_monthly_income' => 4000000,

            'salary_slip' => 'private/test/salary-slip/test.pdf',

            'interest_rate' => 8,

            'approved_amount' => 1800000,

            'status' => 'approved',
        ]);

        LoanDocument::create([
            'loan_id' => $loan->id,
            'document_type' => 'approval_letter',
            'document_stage' => 'signed',
            'file_path' => 'loan-documents/test/approval-letter.pdf',
            'uploaded_by' => $analyst->id,
            'uploaded_at' => now(),
            'verified_by' => $secretary->id,
            'verified_at' => now(),
            'status' => 'verified',
        ]);

        LoanDocument::create([
            'loan_id' => $loan->id,
            'document_type' => 'credit_agreement',
            'document_stage' => 'signed',
            'file_path' => 'loan-documents/test/credit-agreement.pdf',
            'uploaded_by' => $analyst->id,
            'uploaded_at' => now(),
            'verified_by' => $secretary->id,
            'verified_at' => now(),
            'status' => 'verified',
        ]);

        return $loan;
    }

    public function test_disbursement_page_shows_initialized_active_accounts(): void
    {
        $member = $this->createUser(
            'Member Account Selection',
            'member_account_selection',
            'L',
            'member'
        );

        $analyst = $this->createUser(
            'Analyst Account Selection',
            'analyst_account_selection',
            'L',
            'analyst_manager'
        );

        $secretary = $this->createUser(
            'Secretary Account Selection',
            'secretary_account_selection',
            'P',
            'secretary'
        );

        $treasurer = $this->createUser(
            'Treasurer Account Selection',
            'treasurer_account_selection',
            'L',
            'treasurer'
        );

        $loan = $this->createApprovedLoan(
            $member,
            $analyst,
            $secretary
        );

        $sufficientAccount = Account::create([
            'type' => 'bank',
            'name' => 'Rekening Saldo Cukup',
            'bank_name' => 'Bank Test',
            'account_number' => 'SUFFICIENT-' . uniqid(),
            'account_name' => 'KOPKARMADA',
            'opening_balance' => 2000000,
            'is_active' => true,
        ]);

        $this->actingAs($treasurer);

        $response = $this->get(
            route('treasurer.disbursements.show', $loan)
        );

        $response->assertStatus(200);

        $response->assertSee(
            $sufficientAccount->name
        );

        $response->assertSee(
            'Saldo Rp 2.000.000'
        );
    }

    public function test_disbursement_page_shows_insufficient_account_but_disables_it(): void
    {
        $member = $this->createUser(
            'Member Insufficient Selection',
            'member_insufficient_selection',
            'L',
            'member'
        );

        $analyst = $this->createUser(
            'Analyst Insufficient Selection',
            'analyst_insufficient_selection',
            'L',
            'analyst_manager'
        );

        $secretary = $this->createUser(
            'Secretary Insufficient Selection',
            'secretary_insufficient_selection',
            'P',
            'secretary'
        );

        $treasurer = $this->createUser(
            'Treasurer Insufficient Selection',
            'treasurer_insufficient_selection',
            'L',
            'treasurer'
        );

        $loan = $this->createApprovedLoan(
            $member,
            $analyst,
            $secretary
        );

        $account = Account::create([
            'type' => 'bank',
            'name' => 'Rekening Saldo Kurang',
            'bank_name' => 'Bank Test',
            'account_number' => 'INSUFFICIENT-' . uniqid(),
            'account_name' => 'KOPKARMADA',
            'opening_balance' => 1000000,
            'is_active' => true,
        ]);

        $this->actingAs($treasurer);

        $response = $this->get(
            route('treasurer.disbursements.show', $loan)
        );

        $response->assertStatus(200);

        $response->assertSee(
            $account->name
        );

        $response->assertSee(
            'Saldo Tidak Mencukupi'
        );

        $response->assertSee(
            'disabled'
        );
    }

    public function test_disbursement_page_does_not_show_uninitialized_account(): void
    {
        $member = $this->createUser(
            'Member Uninitialized Selection',
            'member_uninitialized_selection',
            'L',
            'member'
        );

        $analyst = $this->createUser(
            'Analyst Uninitialized Selection',
            'analyst_uninitialized_selection',
            'L',
            'analyst_manager'
        );

        $secretary = $this->createUser(
            'Secretary Uninitialized Selection',
            'secretary_uninitialized_selection',
            'P',
            'secretary'
        );

        $treasurer = $this->createUser(
            'Treasurer Uninitialized Selection',
            'treasurer_uninitialized_selection',
            'L',
            'treasurer'
        );

        $loan = $this->createApprovedLoan(
            $member,
            $analyst,
            $secretary
        );

        $account = Account::withoutEvents(function () {
            return Account::create([
                'type' => 'bank',
                'name' => 'Rekening Belum Inisialisasi',
                'bank_name' => 'Bank Test',
                'account_number' => 'UNINITIALIZED-' . uniqid(),
                'account_name' => 'KOPKARMADA',
                'opening_balance' => 0,
                'opening_balance_initialized_at' => null,
                'is_active' => true,
            ]);
        });

        $this->assertNull(
            $account->opening_balance_initialized_at
        );

        $this->actingAs($treasurer);

        $response = $this->get(
            route('treasurer.disbursements.show', $loan)
        );

        $response->assertStatus(200);

        $response->assertDontSee(
            $account->name
        );
    }

    public function test_disbursement_page_does_not_show_inactive_account(): void
    {
        $member = $this->createUser(
            'Member Inactive Selection',
            'member_inactive_selection',
            'L',
            'member'
        );

        $analyst = $this->createUser(
            'Analyst Inactive Selection',
            'analyst_inactive_selection',
            'L',
            'analyst_manager'
        );

        $secretary = $this->createUser(
            'Secretary Inactive Selection',
            'secretary_inactive_selection',
            'P',
            'secretary'
        );

        $treasurer = $this->createUser(
            'Treasurer Inactive Selection',
            'treasurer_inactive_selection',
            'L',
            'treasurer'
        );

        $loan = $this->createApprovedLoan(
            $member,
            $analyst,
            $secretary
        );

        $account = Account::create([
            'type' => 'bank',
            'name' => 'Rekening Nonaktif',
            'bank_name' => 'Bank Test',
            'account_number' => 'INACTIVE-' . uniqid(),
            'account_name' => 'KOPKARMADA',
            'opening_balance' => 2000000,
            'is_active' => false,
        ]);

        $this->actingAs($treasurer);

        $response = $this->get(
            route('treasurer.disbursements.show', $loan)
        );

        $response->assertStatus(200);

        $response->assertDontSee(
            $account->name
        );
    }
}
