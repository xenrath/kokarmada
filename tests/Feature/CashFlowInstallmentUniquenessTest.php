<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\CashFlow;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CashFlowInstallmentUniquenessTest extends TestCase
{
    use DatabaseTransactions;

    public function test_installment_can_have_only_one_cashflow(): void
    {
        $member = $this->createUser(
            'member',
            'Member CashFlow Uniqueness Test'
        );

        $account = $this->createAccount();

        $loan = $this->createLoan($member);

        $installment = Installment::create([
            'loan_id' => $loan->id,
            'installment_number' => 1,
            'due_date' => now()->addMonth()->toDateString(),
            'principal_amount' => 150000,
            'interest_amount' => 12000,
            'penalty_amount' => 0,
            'total_amount' => 162000,
            'status' => 'pending',
        ]);

        CashFlow::create([
            'account_id' => $account->id,
            'loan_id' => $loan->id,
            'installment_id' => $installment->id,
            'type' => 'in',
            'category' => 'installment_payment',
            'amount' => 162000,
            'description' => 'Pembayaran angsuran pertama.',
            'occurred_at' => now(),
        ]);

        $this->expectException(QueryException::class);

        CashFlow::create([
            'account_id' => $account->id,
            'loan_id' => $loan->id,
            'installment_id' => $installment->id,
            'type' => 'in',
            'category' => 'installment_payment',
            'amount' => 162000,
            'description' => 'Duplikat pembayaran angsuran.',
            'occurred_at' => now(),
        ]);
    }

    private function createUser(
        string $role,
        string $name
    ): User {
        return User::create([
            'name' => $name,
            'nickname' => strtolower(
                str_replace(
                    ' ',
                    '_',
                    $name
                )
            ) . '_' . uniqid(),

            'phone' => '08' . random_int(
                1000000000,
                9999999999
            ),

            'gender' => 'L',

            'password' => Hash::make(
                'password'
            ),

            'role' => $role,
            'status' => 'active',
        ]);
    }

    private function createAccount(): Account
    {
        return Account::create([
            'name' => 'Rekening CashFlow Uniqueness Test',
            'type' => 'bank',
            'bank_name' => 'Bank Test',
            'account_number' => 'TEST-' . uniqid(),
            'account_name' => 'KOPKARMADA TEST',
            'is_active' => true,
        ]);
    }

    private function createLoan(
        User $member
    ): Loan {
        $sequenceNumber = ((int) Loan::max(
            'sequence_number'
        )) + 1;

        return Loan::create([
            'code' => 'KOPKARMADA/TEST-CF/' .
                now()->format('YmdHis') .
                '/' .
                $sequenceNumber,

            'sequence_number' => $sequenceNumber,
            'user_id' => $member->id,
            'submitted_at' => now(),

            'requested_amount' => 1800000,

            'purpose_category' => 'consumer',
            'business_type' => null,
            'business_type_other' => null,
            'purpose_description' => 'Pengujian uniqueness CashFlow.',

            'term_months' => 12,
            'repayment_type' => 'monthly',
            'monthly_installment' => 162000,

            'work_unit' => 'Unit Test',
            'position' => 'Tester',
            'employment_duration_years' => 1,

            'other_monthly_income' => 0,

            'salary_slip' => 'private/test/salary-slip/test.pdf',

            'interest_rate' => 8,
            'approved_amount' => 1800000,

            'status' => 'disbursed',
        ]);
    }
}
