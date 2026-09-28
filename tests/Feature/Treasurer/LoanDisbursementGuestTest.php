<?php

namespace Tests\Feature\Treasurer;

use App\Models\Account;
use App\Models\CashFlow;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\LoanDisbursement;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LoanDisbursementGuestTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_cannot_disburse_loan(): void
    {
        /*
         * ============================================================
         * 1. BUAT MEMBER
         * ============================================================
         */
        $member = User::create([
            'name' => 'Member Guest Disbursement Test',
            'nickname' => 'member_guest_disbursement_' . uniqid(),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'L',
            'password' => bcrypt('password'),
            'role' => 'member',
            'status' => 'active',
        ]);

        $this->assertNotNull($member->id);

        /*
         * ============================================================
         * 2. BUAT ACCOUNT AKTIF
         * ============================================================
         *
         * Account dibuat valid supaya request benar-benar sampai
         * ke endpoint pencairan apabila tidak ada authentication.
         */
        $account = Account::create([
            'name' => 'Rekening Guest Disbursement Test',
            'type' => 'bank',
            'bank_name' => 'Bank Test',
            'account_number' => 'GUEST-DISBURSEMENT-' . uniqid(),
            'account_name' => 'KOPKARMADA GUEST DISBURSEMENT TEST',
            'is_active' => true,
        ]);

        $this->assertNotNull($account->id);
        $this->assertTrue((bool) $account->is_active);

        /*
         * ============================================================
         * 3. BUAT LOAN APPROVED
         * ============================================================
         *
         * Loan dibuat approved agar kondisi bisnis pencairan valid.
         * Authentication tetap menjadi satu-satunya penghalang yang
         * sedang diuji.
         */
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
            'purpose_description' => 'Pengujian pencairan oleh guest.',

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

        $this->assertNotNull($loan->id);

        $this->assertSame(
            'approved',
            $loan->status
        );

        $this->assertSame(
            1800000.00,
            (float) $loan->approved_amount
        );

        /*
         * ============================================================
         * 4. KONDISI AWAL
         * ============================================================
         */
        $this->assertSame(
            0,
            LoanDisbursement::query()
                ->where('loan_id', $loan->id)
                ->count()
        );

        $this->assertSame(
            0,
            CashFlow::query()
                ->where('loan_id', $loan->id)
                ->where('category', 'loan_disbursement')
                ->count()
        );

        $this->assertSame(
            0,
            Installment::query()
                ->where('loan_id', $loan->id)
                ->count()
        );

        /*
         * ============================================================
         * 5. PASTIKAN TEST BENAR-BENAR SEBAGAI GUEST
         * ============================================================
         */
        $this->assertGuest();

        /*
         * ============================================================
         * 6. GUEST MENCOBA MELAKUKAN PENCAIRAN
         * ============================================================
         */
        $response = $this->post(
            route('treasurer.disbursements.store', $loan),
            [
                'account_id' => $account->id,
                'disbursed_at' => now()->format('Y-m-d H:i:s'),
                'notes' => 'Percobaan pencairan tanpa authentication.',
            ]
        );

        /*
         * ============================================================
         * 7. HARUS DIARAHKAN KE LOGIN
         * ============================================================
         */
        $response->assertRedirect(route('login'));

        /*
         * ============================================================
         * 8. USER TETAP GUEST
         * ============================================================
         */
        $this->assertGuest();

        /*
         * ============================================================
         * 9. LOAN TIDAK BERUBAH
         * ============================================================
         */
        $loan->refresh();

        $this->assertSame(
            'approved',
            $loan->status
        );

        /*
         * ============================================================
         * 10. TIDAK ADA LOAN DISBURSEMENT
         * ============================================================
         */
        $this->assertSame(
            0,
            LoanDisbursement::query()
                ->where('loan_id', $loan->id)
                ->count()
        );

        /*
         * ============================================================
         * 11. TIDAK ADA CASH FLOW OUT
         * ============================================================
         */
        $this->assertSame(
            0,
            CashFlow::query()
                ->where('loan_id', $loan->id)
                ->where('category', 'loan_disbursement')
                ->count()
        );

        /*
         * ============================================================
         * 12. TIDAK ADA INSTALLMENT
         * ============================================================
         */
        $this->assertSame(
            0,
            Installment::query()
                ->where('loan_id', $loan->id)
                ->count()
        );

        /*
         * ============================================================
         * 13. ACCOUNT TETAP TIDAK BERUBAH
         * ============================================================
         */
        $account->refresh();

        $this->assertTrue(
            (bool) $account->is_active
        );
    }
}
