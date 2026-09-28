<?php

namespace Tests\Feature\Treasurer;

use App\Models\Account;
use App\Models\CashFlow;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\LoanProcess;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InstallmentPaymentGuestTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_cannot_pay_installment(): void
    {
        /*
         * ============================================================
         * 1. BUAT MEMBER PEMILIK PINJAMAN
         * ============================================================
         */
        $member = User::create([
            'name' => 'Member Guest Test',
            'nickname' => 'member_guest_' . uniqid(),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'L',
            'password' => Hash::make('password'),
            'role' => 'member',
            'status' => 'active',
        ]);

        $this->assertNotNull(
            $member->id,
            'User member gagal dibuat.'
        );

        /*
         * ============================================================
         * 2. BUAT ACCOUNT AKTIF
         * ============================================================
         */
        $account = Account::create([
            'name' => 'Rekening Guest Test',
            'type' => 'bank',
            'bank_name' => 'Bank Test',
            'account_number' => 'GUEST-' . uniqid(),
            'account_name' => 'KOPKARMADA GUEST TEST',
            'is_active' => true,
        ]);

        $this->assertNotNull(
            $account->id,
            'Account gagal dibuat.'
        );

        $this->assertTrue(
            (bool) $account->is_active
        );

        /*
         * ============================================================
         * 3. BUAT LOAN DISBURSED
         * ============================================================
         */
        $sequenceNumber = ((int) Loan::max('sequence_number')) + 1;

        $loan = Loan::create([
            'code' => 'KOPKARMADA/TEST-GUEST/' .
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
            'purpose_description' => 'Pengujian guest tidak dapat membayar angsuran.',

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

        $this->assertNotNull(
            $loan->id,
            'Loan gagal dibuat.'
        );

        $this->assertSame(
            'disbursed',
            $loan->status
        );

        /*
         * ============================================================
         * 4. BUAT ANGSURAN PENDING
         * ============================================================
         */
        $installment = Installment::create([
            'loan_id' => $loan->id,
            'installment_number' => 1,

            'due_date' => now()
                ->startOfMonth()
                ->addMonth(),

            'principal_amount' => 150000,
            'interest_amount' => 12000,
            'penalty_amount' => 0,
            'total_amount' => 162000,

            'paid_at' => null,
            'status' => 'pending',
        ]);

        $this->assertNotNull(
            $installment->id,
            'Installment gagal dibuat.'
        );

        $this->assertSame(
            'pending',
            $installment->status
        );

        /*
         * ============================================================
         * 5. KONDISI AWAL
         * ============================================================
         */
        $this->assertGuest();

        $this->assertSame(
            0,
            CashFlow::query()
                ->where('loan_id', $loan->id)
                ->where('installment_id', $installment->id)
                ->where('category', 'installment_payment')
                ->count()
        );

        $this->assertDatabaseMissing('loan_processes', [
            'loan_id' => $loan->id,
            'action' => 'installment_paid',
        ]);

        /*
         * ============================================================
         * 6. GUEST MENCOBA MEMBAYAR
         * ============================================================
         *
         * Request tidak memiliki user yang login.
         *
         * Middleware Authenticate seharusnya menghentikan request
         * dan mengarahkan guest ke halaman login.
         */
        $response = $this->post(
            route(
                'treasurer.installments.pay',
                $installment
            ),
            [
                'account_id' => $account->id,
            ]
        );

        /*
         * Guest harus diarahkan ke route login.
         */
        $response->assertRedirect(
            route('login')
        );

        /*
         * ============================================================
         * 7. USER TETAP GUEST
         * ============================================================
         */
        $this->assertGuest();

        /*
         * ============================================================
         * 8. INSTALLMENT TIDAK BOLEH BERUBAH
         * ============================================================
         */
        $installment->refresh();

        $this->assertSame(
            'pending',
            $installment->status
        );

        $this->assertNull(
            $installment->paid_at
        );

        /*
         * ============================================================
         * 9. LOAN TIDAK BOLEH BERUBAH
         * ============================================================
         */
        $loan->refresh();

        $this->assertSame(
            'disbursed',
            $loan->status
        );

        /*
         * ============================================================
         * 10. CASH FLOW TIDAK BOLEH TERCATAT
         * ============================================================
         */
        $this->assertSame(
            0,
            CashFlow::query()
                ->where('loan_id', $loan->id)
                ->where('installment_id', $installment->id)
                ->where('category', 'installment_payment')
                ->count()
        );

        /*
         * ============================================================
         * 11. LOAN PROCESS TIDAK BOLEH TERCATAT
         * ============================================================
         */
        $this->assertDatabaseMissing('loan_processes', [
            'loan_id' => $loan->id,
            'action' => 'installment_paid',
        ]);

        $this->assertDatabaseMissing('loan_processes', [
            'loan_id' => $loan->id,
            'action' => 'loan_paid_off',
        ]);
    }
}
