<?php

namespace Tests\Feature\Treasurer;

use App\Models\Account;
use App\Models\CashFlow;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InstallmentPaymentRollbackTest extends TestCase
{
    use DatabaseTransactions;

    public function test_installment_payment_rolls_back_when_cashflow_creation_fails(): void
    {
        $member = User::create([
            'name' => 'Member Rollback Test',
            'nickname' => 'member_rollback_' . uniqid(),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'L',
            'password' => Hash::make('password'),
            'role' => 'member',
            'status' => 'active',
        ]);

        $treasurer = User::create([
            'name' => 'Treasurer Rollback Test',
            'nickname' => 'treasurer_rollback_' . uniqid(),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'L',
            'password' => Hash::make('password'),
            'role' => 'treasurer',
            'status' => 'active',
        ]);

        $account = Account::create([
            'name' => 'Rekening Rollback Test',
            'type' => 'bank',
            'bank_name' => 'Bank Test',
            'account_number' => 'ROLLBACK-' . uniqid(),
            'account_name' => 'KOPKARMADA ROLLBACK TEST',
            'is_active' => true,
        ]);

        $sequenceNumber = ((int) Loan::max('sequence_number')) + 1;

        $loan = Loan::create([
            'code' => 'KOPKARMADA/TEST-ROLLBACK/' .
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
            'purpose_description' => 'Pengujian rollback pembayaran angsuran.',

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

        $installment = Installment::create([
            'loan_id' => $loan->id,
            'installment_number' => 1,
            'due_date' => now()->addMonth()->toDateString(),
            'principal_amount' => 150000,
            'interest_amount' => 12000,
            'penalty_amount' => 0,
            'total_amount' => 162000,
            'paid_at' => null,
            'status' => 'pending',
        ]);

        /*
         * CashFlow ini sengaja dibuat terlebih dahulu agar
         * CashFlow::create() di controller gagal karena
         * UNIQUE cash_flows.installment_id.
         */
        $existingCashFlow = CashFlow::create([
            'account_id' => $account->id,
            'loan_id' => $loan->id,
            'installment_id' => $installment->id,
            'type' => 'in',
            'category' => 'installment_payment',
            'amount' => 162000,
            'description' => 'CashFlow existing untuk memicu rollback.',
            'occurred_at' => now(),
        ]);

        $this->assertDatabaseHas('cash_flows', [
            'id' => $existingCashFlow->id,
            'installment_id' => $installment->id,
        ]);

        $this->assertSame(
            'pending',
            $installment->status
        );

        $this->withoutExceptionHandling();

        $this->actingAs($treasurer);

        $this->expectException(
            QueryException::class
        );

        try {
            $this->post(
                route(
                    'treasurer.installments.pay',
                    $installment
                ),
                [
                    'account_id' => $account->id,
                ]
            );
        } finally {
            /*
             * Verifikasi dilakukan sebelum exception meninggalkan test.
             */
            $installment->refresh();
            $loan->refresh();

            $this->assertSame(
                'pending',
                $installment->status
            );

            $this->assertNull(
                $installment->paid_at
            );

            $this->assertSame(
                'disbursed',
                $loan->status
            );

            $this->assertSame(
                1,
                CashFlow::where(
                    'installment_id',
                    $installment->id
                )->count()
            );

            $this->assertDatabaseMissing('loan_processes', [
                'loan_id' => $loan->id,
                'action' => 'installment_paid',
            ]);

            $this->assertDatabaseMissing('activities', [
                'user_id' => $treasurer->id,
                'action' => 'installment_paid',
            ]);
        }
    }
}
