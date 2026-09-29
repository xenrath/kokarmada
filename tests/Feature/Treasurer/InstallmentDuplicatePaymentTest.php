<?php

namespace Tests\Feature\Treasurer;

use App\Models\Account;
use App\Models\CashFlow;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InstallmentDuplicatePaymentTest extends TestCase
{
    use DatabaseTransactions;

    public function test_paid_installment_cannot_be_paid_again(): void
    {
        /*
         * ============================================================
         * 1. BUAT MEMBER PEMILIK PINJAMAN
         * ============================================================
         */
        $member = User::create([
            'name' => 'Member Duplicate Test',
            'nickname' => 'member_duplicate_' . uniqid(),
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

        /*
         * ============================================================
         * 2. BUAT USER TREASURER
         * ============================================================
         */
        $treasurer = User::create([
            'name' => 'Treasurer Duplicate Test',
            'nickname' => 'treasurer_duplicate_' . uniqid(),
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

        /*
         * ============================================================
         * 3. BUAT ACCOUNT
         * ============================================================
         */
        $account = Account::create([
            'name' => 'Rekening Duplicate Test',
            'type' => 'bank',
            'bank_name' => 'Bank Test',
            'account_number' => 'DUPLICATE-' . uniqid(),
            'account_name' => 'KOPKARMADA DUPLICATE TEST',
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
         * test ini hanya menguji perlindungan terhadap pembayaran
         * angsuran yang sudah dibayar.
         */
        $sequenceNumber = ((int) Loan::max('sequence_number')) + 1;

        $loan = Loan::create([
            'code' => 'KOPKARMADA/TEST-DUPLICATE/' .
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
            'purpose_description' => 'Pengujian pembayaran angsuran ganda.',

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
         * 5. BUAT ANGSURAN YANG SUDAH DIBAYAR
         * ============================================================
         *
         * Angsuran ini sengaja dibuat sudah paid.
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

            'paid_at' => now(),

            'status' => 'paid',
        ]);

        $this->assertNotNull(
            $installment->id
        );

        $this->assertSame(
            'paid',
            $installment->status
        );

        $this->assertNotNull(
            $installment->paid_at
        );

        /*
         * ============================================================
         * 6. BUAT CASH FLOW PEMBAYARAN AWAL
         * ============================================================
         *
         * Ini mensimulasikan pembayaran pertama yang memang sudah
         * berhasil dilakukan sebelumnya.
         */
        CashFlow::create([
            'account_id' => $account->id,
            'loan_id' => $loan->id,
            'installment_id' => $installment->id,

            'type' => 'in',
            'category' => 'installment_payment',

            'amount' => $installment->total_amount,

            'description' => sprintf(
                'Pembayaran angsuran ke-%d - %s',
                $installment->installment_number,
                $loan->code
            ),

            'occurred_at' => $installment->paid_at,
        ]);

        /*
         * Pastikan tepat satu CashFlow pembayaran sudah ada
         * sebelum percobaan pembayaran kedua.
         */
        $initialCashFlowCount = CashFlow::query()
            ->where('loan_id', $loan->id)
            ->where('installment_id', $installment->id)
            ->where('category', 'installment_payment')
            ->count();

        $this->assertSame(
            1,
            $initialCashFlowCount
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
         * 8. COBA BAYAR LAGI ANGSURAN YANG SUDAH PAID
         * ============================================================
         *
         * Controller seharusnya menghentikan proses dengan HTTP 422:
         *
         * "Angsuran ini sudah dibayar."
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
         * Harus ditolak dengan status HTTP 422.
         */
        $response->assertStatus(422);

        /*
         * ============================================================
         * 9. STATUS ANGSURAN HARUS TETAP PAID
         * ============================================================
         */
        $installment->refresh();

        $this->assertSame(
            'paid',
            $installment->status
        );

        $this->assertNotNull(
            $installment->paid_at
        );

        /*
         * ============================================================
         * 10. STATUS LOAN TIDAK BOLEH BERUBAH
         * ============================================================
         */
        $loan->refresh();

        $this->assertSame(
            'disbursed',
            $loan->status
        );

        /*
         * ============================================================
         * 11. CASH FLOW TIDAK BOLEH BERTAMBAH
         * ============================================================
         *
         * Harus tetap hanya ada satu CashFlow untuk angsuran ini.
         */
        $finalCashFlowCount = CashFlow::query()
            ->where('loan_id', $loan->id)
            ->where('installment_id', $installment->id)
            ->where('category', 'installment_payment')
            ->count();

        $this->assertSame(
            $initialCashFlowCount,
            $finalCashFlowCount
        );

        $this->assertDatabaseCount(
            'cash_flows',
            CashFlow::count()
        );
    }
}
