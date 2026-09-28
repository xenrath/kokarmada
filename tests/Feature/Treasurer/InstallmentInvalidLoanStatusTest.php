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

class InstallmentInvalidLoanStatusTest extends TestCase
{
    use DatabaseTransactions;

    public function test_installment_payment_is_rejected_when_loan_is_not_disbursed(): void
    {
        /*
         * ============================================================
         * 1. BUAT MEMBER PEMILIK PINJAMAN
         * ============================================================
         */
        $member = User::create([
            'name' => 'Member Invalid Status Test',
            'nickname' => 'member_invalid_status_' . uniqid(),
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

        $this->assertSame(
            'member',
            $member->role
        );

        $this->assertSame(
            'active',
            $member->status
        );

        /*
         * ============================================================
         * 2. BUAT TREASURER
         * ============================================================
         */
        $treasurer = User::create([
            'name' => 'Treasurer Invalid Status Test',
            'nickname' => 'treasurer_invalid_status_' . uniqid(),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'L',
            'password' => Hash::make('password'),
            'role' => 'treasurer',
            'status' => 'active',
        ]);

        $this->assertNotNull(
            $treasurer->id,
            'User Treasurer gagal dibuat.'
        );

        $this->assertTrue(
            $treasurer->isTreasurer()
        );

        $this->assertSame(
            'active',
            $treasurer->status
        );

        /*
         * ============================================================
         * 3. BUAT ACCOUNT AKTIF
         * ============================================================
         *
         * Account sengaja dibuat aktif supaya kegagalan pembayaran
         * benar-benar berasal dari status Loan, bukan dari account.
         */
        $account = Account::create([
            'name' => 'Rekening Invalid Status Test',
            'type' => 'bank',
            'bank_name' => 'Bank Test',
            'account_number' => 'INVALID-STATUS-' . uniqid(),
            'account_name' => 'KOPKARMADA INVALID STATUS TEST',
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
         * 4. BUAT LOAN
         * ============================================================
         *
         * Loan sengaja dibuat dalam status approved.
         *
         * approved bukan status yang diperbolehkan untuk pembayaran
         * angsuran. Pembayaran hanya boleh dilakukan setelah Loan
         * berstatus disbursed.
         */
        $sequenceNumber = ((int) Loan::max('sequence_number')) + 1;

        $loan = Loan::create([
            'code' => 'KOPKARMADA/TEST-INVALID-STATUS/' .
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
            'purpose_description' => 'Pengujian pembayaran pada loan yang belum disbursed.',

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

            'status' => 'approved',
        ]);

        $this->assertNotNull(
            $loan->id,
            'Loan gagal dibuat.'
        );

        $this->assertSame(
            $member->id,
            $loan->user_id
        );

        $this->assertSame(
            'approved',
            $loan->status
        );

        /*
         * ============================================================
         * 5. BUAT ANGSURAN PENDING
         * ============================================================
         *
         * Angsuran memang ada, tetapi Loan belum disbursed.
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

        $this->assertSame(
            $loan->id,
            $installment->loan_id
        );

        /*
         * ============================================================
         * 6. KONDISI AWAL
         * ============================================================
         */
        $this->assertSame(
            'approved',
            $loan->status
        );

        $this->assertSame(
            1,
            $loan->installments()
                ->where('status', 'pending')
                ->count()
        );

        /*
         * Belum ada CashFlow pembayaran.
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
         * Belum ada LoanProcess installment_paid.
         */
        $this->assertDatabaseMissing('loan_processes', [
            'loan_id' => $loan->id,
            'user_id' => $treasurer->id,
            'role' => 'treasurer',
            'action' => 'installment_paid',
        ]);

        /*
         * ============================================================
         * 7. LOGIN SEBAGAI TREASURER
         * ============================================================
         */
        $this->actingAs($treasurer);

        $this->assertAuthenticatedAs(
            $treasurer
        );

        /*
         * ============================================================
         * 8. COBA BAYAR ANGSURAN
         * ============================================================
         *
         * Account aktif, tetapi Loan berstatus approved.
         *
         * Controller seharusnya menghentikan proses dengan HTTP 422.
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

        $response->assertStatus(422);

        /*
         * ============================================================
         * 9. ANGSURAN HARUS TETAP PENDING
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
         * 10. LOAN HARUS TETAP APPROVED
         * ============================================================
         */
        $loan->refresh();

        $this->assertSame(
            'approved',
            $loan->status
        );

        /*
         * ============================================================
         * 11. CASH FLOW PEMBAYARAN TIDAK BOLEH TERCATAT
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
         * 12. LOAN PROCESS PEMBAYARAN TIDAK BOLEH TERCATAT
         * ============================================================
         */
        $this->assertDatabaseMissing('loan_processes', [
            'loan_id' => $loan->id,
            'user_id' => $treasurer->id,
            'role' => 'treasurer',
            'action' => 'installment_paid',
        ]);

        /*
         * ============================================================
         * 13. LOAN PROCESS PAID OFF JUGA TIDAK BOLEH ADA
         * ============================================================
         */
        $this->assertDatabaseMissing('loan_processes', [
            'loan_id' => $loan->id,
            'user_id' => $treasurer->id,
            'role' => 'treasurer',
            'action' => 'loan_paid_off',
        ]);
    }
}
