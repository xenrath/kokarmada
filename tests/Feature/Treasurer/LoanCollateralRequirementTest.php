<?php

namespace Tests\Feature\Treasurer;

use App\Models\Account;
use App\Models\Loan;
use App\Models\LoanCollateral;
use App\Models\LoanDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoanCollateralRequirementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_treasurer_cannot_disburse_loan_above_25_million_without_verified_collateral(): void
    {
        $member = $this->createUser('member');
        $treasurer = $this->createUser('treasurer');
        $secretary = $this->createUser('secretary');
        $analyst = $this->createUser('analyst_manager');

        $account = Account::create([
            'name' => 'Rekening Collateral Test',
            'type' => 'bank',
            'bank_name' => 'Bank Test',
            'account_number' => 'COLLATERAL-' . uniqid(),
            'account_name' => 'KOPKARMADA COLLATERAL TEST',
            'opening_balance' => 50000000,
            'is_active' => true,
        ]);

        $loan = $this->createLoan($member, 30000000);

        $this->createVerifiedDocument($loan, 'approval_letter', $analyst, $secretary);
        $this->createVerifiedDocument($loan, 'credit_agreement', $analyst, $secretary);

        LoanCollateral::create([
            'loan_id' => $loan->id,
            'type' => 'bpkb',
            'description' => 'Agunan pending.',
            'ownership_status' => 'self',
            'ownership_proof' => 'bpkb',
            'proof_file' => 'private/test/collateral.pdf',
            'status' => 'pending',
        ]);

        $this->actingAs($treasurer);

        $response = $this->post(
            route('treasurer.disbursements.store', $loan),
            [
                'account_id' => $account->id,
                'disbursed_at' => now()->format('Y-m-d H:i:s'),
                'notes' => 'Pengujian syarat agunan.',
            ]
        );

        $response->assertStatus(422);

        $loan->refresh();

        $this->assertSame('approved', $loan->status);
        $this->assertDatabaseCount('loan_disbursements', 0);
        $this->assertDatabaseMissing('cash_flows', [
            'loan_id' => $loan->id,
            'category' => 'loan_disbursement',
        ]);
    }

    public function test_treasurer_can_disburse_loan_above_25_million_with_verified_collateral(): void
    {
        $member = $this->createUser('member');
        $treasurer = $this->createUser('treasurer');
        $secretary = $this->createUser('secretary');
        $analyst = $this->createUser('analyst_manager');

        $account = Account::create([
            'name' => 'Rekening Collateral Verified Test',
            'type' => 'bank',
            'bank_name' => 'Bank Test',
            'account_number' => 'COLLATERAL-VERIFIED-' . uniqid(),
            'account_name' => 'KOPKARMADA COLLATERAL VERIFIED TEST',
            'opening_balance' => 50000000,
            'is_active' => true,
        ]);

        $loan = $this->createLoan($member, 30000000);

        $this->createVerifiedDocument($loan, 'approval_letter', $analyst, $secretary);
        $this->createVerifiedDocument($loan, 'credit_agreement', $analyst, $secretary);

        $collateral = LoanCollateral::create([
            'loan_id' => $loan->id,
            'type' => 'bpkb',
            'description' => 'Agunan terverifikasi.',
            'ownership_status' => 'self',
            'ownership_proof' => 'bpkb',
            'proof_file' => 'private/test/collateral.pdf',
            'status' => 'verified',
            'verified_by' => $secretary->id,
            'verified_at' => now(),
        ]);

        $this->assertSame('verified', $collateral->status);

        $this->actingAs($treasurer);

        $response = $this->post(
            route('treasurer.disbursements.store', $loan),
            [
                'account_id' => $account->id,
                'disbursed_at' => now()->format('Y-m-d H:i:s'),
                'notes' => 'Pengujian pencairan dengan agunan terverifikasi.',
            ]
        );

        $response->assertSessionHas('success');

        $loan->refresh();

        $this->assertSame('disbursed', $loan->status);
        $this->assertDatabaseHas('loan_disbursements', [
            'loan_id' => $loan->id,
            'amount' => 30000000,
        ]);
    }

    private function createUser(string $role): User
    {
        return User::create([
            'name' => ucfirst($role) . ' Collateral Disbursement Test',
            'nickname' => $role . '_collateral_disbursement_' . uniqid(),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'L',
            'password' => Hash::make('password'),
            'role' => $role,
            'status' => 'active',
        ]);
    }

    private function createLoan(User $member, float $amount): Loan
    {
        $sequenceNumber = ((int) Loan::max('sequence_number')) + 1;

        return Loan::create([
            'code' => 'KOPKARMADA/COLLATERAL-DISBURSE/' . now()->format('YmdHis') . '/' . $sequenceNumber,
            'sequence_number' => $sequenceNumber,
            'user_id' => $member->id,
            'submitted_at' => now(),
            'requested_amount' => $amount,
            'purpose_category' => 'consumer',
            'purpose_description' => 'Pengujian pencairan dengan agunan.',
            'term_months' => 12,
            'repayment_type' => 'monthly',
            'work_unit' => 'Unit Test',
            'position' => 'Tester',
            'employment_duration_years' => 1,
            'other_monthly_income' => 0,
            'net_monthly_income' => 5000000,
            'salary_slip' => 'private/test/salary-slip.pdf',
            'interest_rate' => 8,
            'approved_amount' => $amount,
            'status' => 'approved',
        ]);
    }

    private function createVerifiedDocument(
        Loan $loan,
        string $type,
        User $analyst,
        User $secretary
    ): void {
        LoanDocument::create([
            'loan_id' => $loan->id,
            'document_type' => $type,
            'document_stage' => 'signed',
            'file_path' => 'private/test/' . $type . '.pdf',
            'uploaded_by' => $analyst->id,
            'uploaded_at' => now(),
            'verified_by' => $secretary->id,
            'verified_at' => now(),
            'status' => 'verified',
        ]);
    }
}
