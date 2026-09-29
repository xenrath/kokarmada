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

class InstallmentInactiveAccountTest extends TestCase
{
    use DatabaseTransactions;

    public function test_installment_payment_cannot_use_inactive_account(): void
    {
        /*
         * ============================================================
         * 1. BUAT MEMBER PEMILIK PINJAMAN
         * ============================================================
         */
        $member = User::create([
            'name' => 'Member Inactive Account Test',
            'nickname' => 'member_inactive_' . uniqid(),
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
         * 2. BUAT USER TREASURER
         * ============================================================
         */
        $treasurer = User::create([
            'name' => 'Treasurer Inactive Account Test',
            'nickname' => 'treasurer_inactive_' . uniqid(),
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
         * 3. BUAT ACCOUNT NONAKTIF
         * ============================================================
         *
         * Account sengaja dibuat tidak aktif.
         */
        $account = Account::create([
            'name' => 'Rekening Nonaktif Test',
            'type' => 'bank',
            'bank_name' => 'Bank Test',
            'account_number' => 'INACTIVE-' . uniqid(),
            'account_name' => 'KOPKARMADA INACTIVE TEST',
            'is_active' => false,
        ]);

        $this->assertNotNull(
            $account->id,
            'Account gagal dibuat.'
        );

        $this->assertFalse(
            (bool) $account->is_active
        );

        /*
         * ============================================================
         * 4. BUAT LOAN
         * ============================================================
         *
         * Loan langsung dibuat disbursed karena test ini hanya
         * menguji validasi rekening saat pembayaran angsuran.
         */
        $sequenceNumber = ((int) Loan::max('sequence_number')) + 1;

        $loan = Loan::create([
            'code' => 'KOPKARMADA/TEST-INACTIVE/' .
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
            'purpose_description' => 'Pengujian pembayaran dengan rekening nonaktif.',

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
         * 5. BUAT SATU ANGSURAN PENDING
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
            $installment->id
        );

        $this->assertSame(
            'pending',
            $installment->status
        );

        /*
         * ============================================================
         * 6. VALIDASI KONDISI AWAL
         * ============================================================
         */
        $this->assertSame(
            1,
            $loan->installments()
                ->where('status', 'pending')
                ->count()
        );

        /*
         * Belum ada CashFlow pembayaran untuk angsuran ini.
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
         * 7. LOGIN SEBAGAI TREASURER
         * ============================================================
         */
        $this->actingAs($treasurer);

        $this->assertAuthenticatedAs(
            $treasurer
        );

        /*
         * ============================================================
         * 8. COBA BAYAR DENGAN ACCOUNT NONAKTIF
         * ============================================================
         *
         * Controller menggunakan firstOrFail() setelah filter
         * is_active = true.
         *
         * Karena account ini tidak aktif, request seharusnya
         * menghasilkan HTTP 404.
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

        $response->assertNotFound();

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
         * 10. LOAN HARUS TETAP DISBURSED
         * ============================================================
         */
        $loan->refresh();

        $this->assertSame(
            'disbursed',
            $loan->status
        );

        /*
         * ============================================================
         * 11. TIDAK BOLEH ADA CASH FLOW PEMBAYARAN
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
         * 12. TIDAK BOLEH ADA LOAN PROCESS PEMBAYARAN
         * ============================================================
         */
        $this->assertDatabaseMissing('loan_processes', [
            'loan_id' => $loan->id,
            'user_id' => $treasurer->id,
            'role' => 'treasurer',
            'action' => 'installment_paid',
        ]);
    }
}
