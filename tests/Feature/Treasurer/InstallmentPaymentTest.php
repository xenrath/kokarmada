<?php

namespace Tests\Feature\Treasurer;

use App\Models\Account;
use App\Models\Activity;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InstallmentPaymentTest extends TestCase
{
    use DatabaseTransactions;

    public function test_paying_last_installment_marks_loan_as_paid_off(): void
    {
        /*
         * ============================================================
         * 1. BUAT MEMBER PEMILIK PINJAMAN
         * ============================================================
         */
        $member = User::create([
            'name' => 'Member Test',
            'nickname' => 'member_test_' . uniqid(),
            'phone' => '08' . mt_rand(1000000000, 9999999999),
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
         * 2. BUAT USER TREASURER
         * ============================================================
         */
        $treasurer = User::create([
            'name' => 'Treasurer Test',
            'nickname' => 'treasurer_test_' . uniqid(),
            'phone' => '08' . mt_rand(1000000000, 9999999999),
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
         * 3. BUAT ACCOUNT
         * ============================================================
         */
        $account = Account::create([
            'name' => 'Rekening Test',
            'type' => 'bank',
            'bank_name' => 'Bank Test',
            'account_number' => 'TEST-' . uniqid(),
            'account_name' => 'KOPKARMADA TEST',
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
         * Loan langsung dibuat dalam status disbursed karena
         * test ini fokus pada pembayaran angsuran sampai lunas.
         */
        $sequenceNumber = ((int) Loan::max('sequence_number')) + 1;

        $loan = Loan::create([
            'code' => 'KOPKARMADA/TEST/' . now()->format('YmdHis') . '/' . $sequenceNumber,
            'sequence_number' => $sequenceNumber,
            'user_id' => $member->id,
            'submitted_at' => now(),

            'requested_amount' => 1800000,

            'purpose_category' => 'consumer',
            'business_type' => null,
            'business_type_other' => null,
            'purpose_description' => 'Pengujian pembayaran angsuran.',

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
            $member->id,
            $loan->user_id
        );

        $this->assertSame(
            'disbursed',
            $loan->status
        );

        /*
         * ============================================================
         * 5. BUAT 12 ANGSURAN
         * ============================================================
         *
         * Angsuran 1-11 = paid
         * Angsuran 12   = pending
         *
         * Sehingga angsuran ke-12 menjadi pembayaran terakhir.
         */
        for ($number = 1; $number <= 12; $number++) {
            $isLastInstallment = $number === 12;

            Installment::create([
                'loan_id' => $loan->id,
                'installment_number' => $number,
                'due_date' => now()
                    ->startOfMonth()
                    ->addMonths($number),

                'principal_amount' => 150000,
                'interest_amount' => 12000,
                'penalty_amount' => 0,
                'total_amount' => 162000,

                'paid_at' => $isLastInstallment
                    ? null
                    : now(),

                'status' => $isLastInstallment
                    ? 'pending'
                    : 'paid',
            ]);
        }

        /*
         * ============================================================
         * 6. VALIDASI DATA ANGSURAN
         * ============================================================
         */
        $installments = $loan->installments()
            ->orderBy('installment_number')
            ->get();

        $this->assertCount(
            12,
            $installments
        );

        $this->assertSame(
            11,
            $loan->installments()
                ->where('status', 'paid')
                ->count()
        );

        $this->assertSame(
            1,
            $loan->installments()
                ->where('status', '!=', 'paid')
                ->count()
        );

        /*
         * ============================================================
         * 7. AMBIL ANGSURAN TERAKHIR
         * ============================================================
         */
        $lastInstallment = $loan->installments()
            ->where('installment_number', 12)
            ->firstOrFail();

        $this->assertSame(
            'pending',
            $lastInstallment->status
        );

        $this->assertSame(
            12,
            $lastInstallment->installment_number
        );

        $this->assertSame(
            $loan->id,
            $lastInstallment->loan_id
        );

        $this->assertSame(
            162000.00,
            (float) $lastInstallment->total_amount
        );

        /*
         * ============================================================
         * 8. LOGIN SEBAGAI TREASURER
         * ============================================================
         */
        $this->actingAs($treasurer);

        $this->assertAuthenticatedAs(
            $treasurer
        );

        /*
         * ============================================================
         * 9. BAYAR ANGSURAN TERAKHIR
         * ============================================================
         */
        $response = $this->post(
            route(
                'treasurer.installments.pay',
                $lastInstallment
            ),
            [
                'account_id' => $account->id,
            ]
        );

        /*
         * Setelah pembayaran berhasil, harus kembali ke
         * halaman detail angsuran.
         */
        $response->assertRedirect(
            route(
                'treasurer.installments.show',
                $loan
            )
        );

        /*
         * ============================================================
         * 10. REFRESH DATA
         * ============================================================
         */
        $loan->refresh();
        $lastInstallment->refresh();

        /*
         * ============================================================
         * 11. ANGSURAN TERAKHIR HARUS PAID
         * ============================================================
         */
        $this->assertSame(
            'paid',
            $lastInstallment->status
        );

        $this->assertNotNull(
            $lastInstallment->paid_at
        );

        /*
         * ============================================================
         * 12. SEMUA ANGSURAN HARUS PAID
         * ============================================================
         */
        $this->assertSame(
            0,
            $loan->installments()
                ->where('status', '!=', 'paid')
                ->count()
        );

        /*
         * ============================================================
         * 13. LOAN HARUS OTOMATIS MENJADI PAID_OFF
         * ============================================================
         */
        $this->assertSame(
            'paid_off',
            $loan->status
        );

        /*
         * ============================================================
         * 14. CASH FLOW PEMBAYARAN HARUS TERCATAT
         * ============================================================
         */
        $this->assertDatabaseHas('cash_flows', [
            'loan_id' => $loan->id,
            'installment_id' => $lastInstallment->id,
            'account_id' => $account->id,
            'type' => 'in',
            'category' => 'installment_payment',
            'amount' => $lastInstallment->total_amount,
        ]);

        /*
         * ============================================================
         * 15. LOAN PROCESS INSTALLMENT_PAID
         * ============================================================
         */
        $this->assertDatabaseHas('loan_processes', [
            'loan_id' => $loan->id,
            'user_id' => $treasurer->id,
            'role' => 'treasurer',
            'action' => 'installment_paid',
        ]);

        /*
         * ============================================================
         * 16. LOAN PROCESS LOAN_PAID_OFF
         * ============================================================
         */
        $this->assertDatabaseHas('loan_processes', [
            'loan_id' => $loan->id,
            'user_id' => $treasurer->id,
            'role' => 'treasurer',
            'action' => 'loan_paid_off',
        ]);

        /*
         * ============================================================
         * 17. ACTIVITY LOAN_PAID_OFF
         * ============================================================
         */
        $this->assertDatabaseHas('activities', [
            'user_id' => $treasurer->id,
            'action' => 'loan_paid_off',
        ]);
    }
}
