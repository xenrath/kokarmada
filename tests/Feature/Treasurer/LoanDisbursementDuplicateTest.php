<?php

namespace Tests\Feature\Treasurer;

use App\Models\Account;
use App\Models\CashFlow;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\LoanDisbursement;
use App\Models\LoanDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoanDisbursementDuplicateTest extends TestCase
{
    use DatabaseTransactions;

    public function test_treasurer_cannot_disburse_a_loan_more_than_once(): void
    {
        $member = User::create([
            'name' => 'Member Duplicate Disbursement Test',
            'nickname' => 'member_duplicate_disbursement_' . uniqid(),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'L',
            'password' => Hash::make('password'),
            'role' => 'member',
            'status' => 'active',
        ]);

        $analyst = User::create([
            'name' => 'Analyst Duplicate Disbursement Test',
            'nickname' => 'analyst_duplicate_disbursement_' . uniqid(),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'L',
            'password' => Hash::make('password'),
            'role' => 'analyst_manager',
            'status' => 'active',
        ]);

        $secretary = User::create([
            'name' => 'Secretary Duplicate Disbursement Test',
            'nickname' => 'secretary_duplicate_disbursement_' . uniqid(),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'P',
            'password' => Hash::make('password'),
            'role' => 'secretary',
            'status' => 'active',
        ]);

        $treasurer = User::create([
            'name' => 'Treasurer Duplicate Disbursement Test',
            'nickname' => 'treasurer_duplicate_disbursement_' . uniqid(),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'L',
            'password' => Hash::make('password'),
            'role' => 'treasurer',
            'status' => 'active',
        ]);

        $account = Account::create([
            'name' => 'Rekening Duplicate Disbursement Test',
            'type' => 'bank',
            'bank_name' => 'Bank Test',
            'account_number' => 'DUPLICATE-DISBURSEMENT-' . uniqid(),
            'account_name' => 'KOPKARMADA DUPLICATE TEST',
            'is_active' => true,
        ]);

        $sequenceNumber = ((int) Loan::max('sequence_number')) + 1;

        $loan = Loan::create([
            'code' => 'KOPKARMADA/' . $sequenceNumber . '/IX/2026',
            'sequence_number' => $sequenceNumber,
            'user_id' => $member->id,
            'submitted_at' => now(),
            'requested_amount' => 1800000,
            'purpose_category' => 'consumer',
            'business_type' => null,
            'business_type_other' => null,
            'purpose_description' => 'Pengujian duplicate disbursement.',
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
            'status' => 'disbursed',
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

        $disbursedAt = now();

        LoanDisbursement::create([
            'loan_id' => $loan->id,
            'treasurer_id' => $treasurer->id,
            'account_id' => $account->id,
            'amount' => 1800000,
            'disbursed_at' => $disbursedAt,
            'notes' => 'Pencairan pertama.',
        ]);

        CashFlow::create([
            'account_id' => $account->id,
            'loan_id' => $loan->id,
            'type' => 'out',
            'category' => 'loan_disbursement',
            'amount' => 1800000,
            'occurred_at' => $disbursedAt,
            'description' => 'Pencairan pinjaman pertama.',
        ]);

        for ($number = 1; $number <= 12; $number++) {
            Installment::create([
                'loan_id' => $loan->id,
                'installment_number' => $number,
                'due_date' => $disbursedAt->copy()->addMonthsNoOverflow($number),
                'principal_amount' => 150000,
                'interest_amount' => 12000,
                'penalty_amount' => 0,
                'total_amount' => 162000,
                'status' => 'pending',
            ]);
        }

        $this->assertSame(
            1,
            LoanDisbursement::where('loan_id', $loan->id)->count()
        );

        $this->assertSame(
            1,
            CashFlow::where('loan_id', $loan->id)
                ->where('category', 'loan_disbursement')
                ->count()
        );

        $this->assertSame(
            12,
            Installment::where('loan_id', $loan->id)->count()
        );

        $this->actingAs($treasurer);

        $response = $this->post(
            route('treasurer.disbursements.store', $loan),
            [
                'account_id' => $account->id,
                'disbursed_at' => now()->format('Y-m-d H:i:s'),
                'notes' => 'Percobaan pencairan kedua.',
            ]
        );

        $this->assertNotSame(
            500,
            $response->getStatusCode()
        );

        $this->assertFalse(
            $response->isSuccessful()
        );

        $loan->refresh();

        $this->assertSame(
            'disbursed',
            $loan->status
        );

        $this->assertSame(
            1,
            LoanDisbursement::where('loan_id', $loan->id)->count()
        );

        $this->assertSame(
            1,
            CashFlow::where('loan_id', $loan->id)
                ->where('category', 'loan_disbursement')
                ->count()
        );

        $this->assertSame(
            12,
            Installment::where('loan_id', $loan->id)->count()
        );

        $this->assertDatabaseHas('loan_disbursements', [
            'loan_id' => $loan->id,
            'treasurer_id' => $treasurer->id,
            'account_id' => $account->id,
            'amount' => 1800000,
        ]);
    }
}
