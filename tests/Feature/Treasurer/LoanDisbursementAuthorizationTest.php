<?php

namespace Tests\Feature\Treasurer;

use App\Models\Account;
use App\Models\CashFlow;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\LoanDisbursement;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoanDisbursementAuthorizationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_member_cannot_disburse_loan(): void
    {
        $member = User::create([
            'name' => 'Member Authorization Test',
            'nickname' => 'member_authorization_' . uniqid(),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'L',
            'password' => Hash::make('password'),
            'role' => 'member',
            'status' => 'active',
        ]);

        $account = Account::create([
            'name' => 'Rekening Authorization Test',
            'type' => 'bank',
            'bank_name' => 'Bank Test',
            'account_number' => 'AUTH-DISBURSEMENT-' . uniqid(),
            'account_name' => 'KOPKARMADA AUTHORIZATION TEST',
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
            'purpose_description' => 'Pengujian authorization pencairan.',
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

        $this->actingAs($member);

        $response = $this->post(
            route('treasurer.disbursements.store', $loan),
            [
                'account_id' => $account->id,
                'disbursed_at' => now()->format('Y-m-d H:i:s'),
                'notes' => 'Percobaan pencairan oleh member.',
            ]
        );

        $response->assertForbidden();

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
