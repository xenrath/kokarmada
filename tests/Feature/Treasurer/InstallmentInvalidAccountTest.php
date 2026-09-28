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

class InstallmentInvalidAccountTest extends TestCase
{
    use DatabaseTransactions;

    public function test_installment_payment_requires_existing_account(): void
    {
        /*
         * ============================================================
         * 1. BUAT MEMBER PEMILIK PINJAMAN
         * ============================================================
         */
        $member = User::create([
            'name' => 'Member Invalid Account Test',
            'nickname' => 'member_invalid_account_' . uniqid(),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'L',
            'password' => Hash::make('password'),
            'role' => 'member',
            'status' => 'active',
        ]);

        $this->assertNotNull(
            $member->id
        );

        /*
         * ============================================================
         * 2. BUAT TREASURER
         * ============================================================
         */
        $treasurer = User::create([
            'name' => 'Treasurer Invalid Account Test',
            'nickname' => 'treasurer_invalid_account_' . uniqid(),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'L',
            'password' => Hash::make('password'),
            'role' => 'treasurer',
            'status' => 'active',
        ]);

        $this->assertNotNull(
            $treasurer->id
        );

        $this->assertTrue(
            $treasurer->isTreasurer()
        );

        /*
         * ============================================================
         * 3. BUAT ACCOUNT AKTIF
         * ============================================================
         *
         * Account ini hanya sebagai pembanding.
         * Request nantinya sengaja menggunakan ID account
         * yang tidak ada.
         */
        $account = Account::create([
            'name' => 'Rekening Invalid Account Test',
            'type' => 'bank',
            'bank_name' => 'Bank Test',
            'account_number' => 'VALID-' . uniqid(),
            'account_name' => 'KOPKARMADA INVALID ACCOUNT TEST',
            'is_active' => true,
        ]);

        $this->assertNotNull(
            $account->id
        );

        $this->assertTrue(
            (bool) $account->is_active
        );

        /*
         * ============================================================
         * 4. TENTUKAN ACCOUNT ID YANG TIDAK ADA
         * ============================================================
         */
        $invalidAccountId = ((int) Account::max('id')) + 9999;

        $this->assertDatabaseMissing(
            'accounts',
            [
                'id' => $invalidAccountId,
            ]
        );

        /*
         * ============================================================
         * 5. BUAT LOAN DISBURSED
         * ============================================================
         */
        $sequenceNumber = ((int) Loan::max('sequence_number')) + 1;

        $loan = Loan::create([
            'code' => 'KOPKARMADA/TEST-INVALID-ACCOUNT/' .
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
            'purpose_description' => 'Pengujian account_id yang tidak valid.',

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
            $loan->id
        );

        $this->assertSame(
            'disbursed',
            $loan->status
        );

        /*
         * ============================================================
         * 6. BUAT ANGSURAN PENDING
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
         * 7. KONDISI AWAL
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

        $this->assertDatabaseMissing('loan_processes', [
            'loan_id' => $loan->id,
            'role' => 'treasurer',
            'action' => 'installment_paid',
        ]);

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
         * 9. COBA BAYAR DENGAN ACCOUNT ID YANG TIDAK ADA
         * ============================================================
         *
         * Rule exists:accounts,id harus menolak request.
         *
         * Karena request menggunakan middleware validasi Laravel,
         * response normal untuk validation failure adalah redirect
         * kembali dengan session error.
         */
        $response = $this->post(
            route(
                'treasurer.installments.pay',
                $installment
            ),
            [
                'account_id' => $invalidAccountId,
            ]
        );

        /*
         * Validation gagal dan request kembali ke halaman sebelumnya.
         */
        $response->assertSessionHasErrors([
            'account_id',
        ]);

        /*
         * ============================================================
         * 10. INSTALLMENT TETAP PENDING
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
         * 11. LOAN TETAP DISBURSED
         * ============================================================
         */
        $loan->refresh();

        $this->assertSame(
            'disbursed',
            $loan->status
        );

        /*
         * ============================================================
         * 12. TIDAK ADA CASH FLOW PEMBAYARAN
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
         * 13. TIDAK ADA LOAN PROCESS
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
