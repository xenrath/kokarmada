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

class LoanDisbursementInactiveAccountTest extends TestCase
{
    use DatabaseTransactions;

    public function test_treasurer_cannot_disburse_using_inactive_account(): void
    {
        $member = User::create([
            'name' => 'Member Inactive Account Test',
            'nickname' => 'member_inactive_account_' . uniqid(),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'L',
            'password' => Hash::make('password'),
            'role' => 'member',
            'status' => 'active',
        ]);

        $analyst = User::create([
            'name' => 'Analyst Inactive Account Test',
            'nickname' => 'analyst_inactive_account_' . uniqid(),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'L',
            'password' => Hash::make('password'),
            'role' => 'analyst_manager',
            'status' => 'active',
        ]);

        $secretary = User::create([
            'name' => 'Secretary Inactive Account Test',
            'nickname' => 'secretary_inactive_account_' . uniqid(),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'P',
            'password' => Hash::make('password'),
            'role' => 'secretary',
            'status' => 'active',
        ]);

        $treasurer = User::create([
            'name' => 'Treasurer Inactive Account Test',
            'nickname' => 'treasurer_inactive_account_' . uniqid(),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'L',
            'password' => Hash::make('password'),
            'role' => 'treasurer',
            'status' => 'active',
        ]);

        $account = Account::create([
            'name' => 'Rekening Tidak Aktif Test',
            'type' => 'bank',
            'bank_name' => 'Bank Test',
            'account_number' => 'INACTIVE-DISBURSEMENT-' . uniqid(),
            'account_name' => 'KOPKARMADA INACTIVE ACCOUNT TEST',
            'is_active' => false,
        ]);

        $this->assertFalse((bool) $account->is_active);

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
            'purpose_description' => 'Pengujian pencairan menggunakan rekening tidak aktif.',
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

        $this->assertSame(
            0,
            LoanDisbursement::where('loan_id', $loan->id)->count()
        );

        $this->assertSame(
            0,
            CashFlow::where('loan_id', $loan->id)
                ->where('category', 'loan_disbursement')
                ->count()
        );

        $this->assertSame(
            0,
            Installment::where('loan_id', $loan->id)->count()
        );

        $this->actingAs($treasurer);

        $response = $this->post(
            route('treasurer.disbursements.store', $loan),
            [
                'account_id' => $account->id,
                'disbursed_at' => now()->format('Y-m-d H:i:s'),
                'notes' => 'Percobaan pencairan menggunakan rekening tidak aktif.',
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
            'approved',
            $loan->status
        );

        $this->assertSame(
            0,
            LoanDisbursement::where('loan_id', $loan->id)->count()
        );

        $this->assertSame(
            0,
            CashFlow::where('loan_id', $loan->id)
                ->where('category', 'loan_disbursement')
                ->count()
        );

        $this->assertSame(
            0,
            Installment::where('loan_id', $loan->id)->count()
        );
    }
}
