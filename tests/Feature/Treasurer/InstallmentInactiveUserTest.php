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

class InstallmentInactiveUserTest extends TestCase
{
    use DatabaseTransactions;

    public function test_inactive_treasurer_cannot_pay_installment(): void
    {
        /*
         * ============================================================
         * 1. BUAT MEMBER PEMILIK PINJAMAN
         * ============================================================
         */
        $member = User::create([
            'name' => 'Member Inactive User Test',
            'nickname' => 'member_inactive_user_' . uniqid(),
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

        $this->assertTrue(
            $member->isMember()
        );

        /*
         * ============================================================
         * 2. BUAT TREASURER NONAKTIF
         * ============================================================
         *
         * Role benar, tetapi status sengaja dibuat inactive.
         */
        $treasurer = User::create([
            'name' => 'Treasurer Inactive User Test',
            'nickname' => 'treasurer_inactive_user_' . uniqid(),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'L',
            'password' => Hash::make('password'),
            'role' => 'treasurer',
            'status' => 'inactive',
        ]);

        $this->assertNotNull(
            $treasurer->id,
            'User Treasurer gagal dibuat.'
        );

        $this->assertTrue(
            $treasurer->isTreasurer()
        );

        $this->assertFalse(
            $treasurer->isActive()
        );

        $this->assertSame(
            'inactive',
            $treasurer->status
        );

        /*
         * ============================================================
         * 3. BUAT ACCOUNT AKTIF
         * ============================================================
         *
         * Account aktif agar kegagalan pembayaran bukan berasal
         * dari validasi account.
         */
        $account = Account::create([
            'name' => 'Rekening Inactive User Test',
            'type' => 'bank',
            'bank_name' => 'Bank Test',
            'account_number' => 'INACTIVE-USER-' . uniqid(),
            'account_name' => 'KOPKARMADA INACTIVE USER TEST',
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
         */
        $sequenceNumber = ((int) Loan::max('sequence_number')) + 1;

        $loan = Loan::create([
            'code' => 'KOPKARMADA/TEST-INACTIVE-USER/' .
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
            'purpose_description' => 'Pengujian Treasurer inactive.',

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
         * 5. BUAT ANGSURAN PENDING
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
         * 6. KONDISI AWAL
         * ============================================================
         */
        $this->assertSame(
            'disbursed',
            $loan->status
        );

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
         * 7. LOGIN SEBAGAI TREASURER NONAKTIF
         * ============================================================
         */
        $this->actingAs($treasurer);

        $this->assertAuthenticatedAs(
            $treasurer
        );

        $this->assertTrue(
            auth()->user()->isTreasurer()
        );

        $this->assertFalse(
            auth()->user()->isActive()
        );

        /*
         * ============================================================
         * 8. COBA BAYAR
         * ============================================================
         *
         * User memiliki role Treasurer tetapi status inactive.
         *
         * ActiveUser middleware seharusnya menghentikan request.
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
         * User inactive tidak boleh berhasil melakukan pembayaran.
         */
        $this->assertFalse(
            $response->isSuccessful(),
            'Treasurer inactive tidak boleh menerima response sukses.'
        );

        /*
         * Middleware ActiveUser seharusnya mengeluarkan user
         * dari authentication session.
         */
        $this->assertGuest();

        /*
         * ============================================================
         * 9. INSTALLMENT HARUS TETAP PENDING
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
         * 11. CASH FLOW TIDAK BOLEH TERCATAT
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
         * 12. LOAN PROCESS TIDAK BOLEH TERCATAT
         * ============================================================
         */
        $this->assertDatabaseMissing('loan_processes', [
            'loan_id' => $loan->id,
            'role' => 'treasurer',
            'action' => 'installment_paid',
        ]);

        $this->assertDatabaseMissing('loan_processes', [
            'loan_id' => $loan->id,
            'role' => 'treasurer',
            'action' => 'loan_paid_off',
        ]);
    }
}
