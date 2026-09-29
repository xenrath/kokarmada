<?php

namespace Tests\Feature\Treasurer;

use App\Models\Account;
use App\Models\CashFlow;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\LoanDocument;
use App\Models\LoanDisbursement;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoanDisbursementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_treasurer_can_disburse_approved_loan_with_verified_documents(): void
    {
        /*
         * ============================================================
         * 1. BUAT MEMBER PEMILIK PINJAMAN
         * ============================================================
         */
        $member = User::create([
            'name' => 'Member Disbursement Test',
            'nickname' => 'member_disbursement_' . uniqid(),
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
         * 2. BUAT ANALYST MANAGER
         * ============================================================
         *
         * Digunakan sebagai pihak yang mengunggah dokumen bertanda
         * tangan.
         */
        $analyst = User::create([
            'name' => 'Analyst Disbursement Test',
            'nickname' => 'analyst_disbursement_' . uniqid(),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'L',
            'password' => Hash::make('password'),
            'role' => 'analyst_manager',
            'status' => 'active',
        ]);

        $this->assertNotNull(
            $analyst->id,
            'User Analyst Manager gagal dibuat.'
        );

        $this->assertTrue(
            $analyst->isAnalystManager()
        );

        /*
         * ============================================================
         * 3. BUAT SECRETARY
         * ============================================================
         *
         * Digunakan sebagai pihak yang memverifikasi dokumen.
         */
        $secretary = User::create([
            'name' => 'Secretary Disbursement Test',
            'nickname' => 'secretary_disbursement_' . uniqid(),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'P',
            'password' => Hash::make('password'),
            'role' => 'secretary',
            'status' => 'active',
        ]);

        $this->assertNotNull(
            $secretary->id,
            'User Secretary gagal dibuat.'
        );

        $this->assertTrue(
            $secretary->isSecretary()
        );

        /*
         * ============================================================
         * 4. BUAT TREASURER
         * ============================================================
         */
        $treasurer = User::create([
            'name' => 'Treasurer Disbursement Test',
            'nickname' => 'treasurer_disbursement_' . uniqid(),
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

        $this->assertTrue(
            $treasurer->isActive()
        );

        /*
         * ============================================================
         * 5. BUAT ACCOUNT AKTIF
         * ============================================================
         */
        $account = Account::create([
            'name' => 'Rekening Disbursement Test',
            'type' => 'bank',
            'bank_name' => 'Bank Test',
            'account_number' => 'DISBURSEMENT-' . uniqid(),
            'account_name' => 'KOPKARMADA DISBURSEMENT TEST',
            'opening_balance' => 1800000,
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
         * 6. BUAT LOAN APPROVED
         * ============================================================
         *
         * Loan dibuat langsung approved karena test ini fokus pada
         * proses pencairan, bukan proses analisis/approval.
         */
        $sequenceNumber = ((int) Loan::max('sequence_number')) + 1;

        $loan = Loan::create([
            'code' => 'KOPKARMADA/TEST-DISBURSEMENT/' .
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
            'purpose_description' => 'Pengujian proses pencairan pinjaman.',

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

        $this->assertSame(
            1800000.00,
            (float) $loan->approved_amount
        );

        /*
         * ============================================================
         * 7. BUAT DOKUMEN WAJIB YANG SUDAH DIVERIFIKASI
         * ============================================================
         *
         * Treasurer hanya boleh mencairkan apabila:
         *
         * - approval_letter sudah ada
         * - credit_agreement sudah ada
         * - keduanya sudah diverifikasi
         *
         * File fisik tidak diperlukan untuk test ini karena yang diuji
         * adalah data/status dokumennya.
         */
        $approvalLetter = LoanDocument::create([
            'loan_id' => $loan->id,
            'document_type' => 'approval_letter',
            'document_stage' => 'signed',
            'generated_file_path' => null,
            'generated_at' => null,
            'file_path' => 'loan-documents/test/approval-letter.pdf',
            'uploaded_by' => $analyst->id,
            'uploaded_at' => now(),

            'verified_by' => $secretary->id,
            'verified_at' => now(),

            'status' => 'verified',
        ]);

        $creditAgreement = LoanDocument::create([
            'loan_id' => $loan->id,
            'document_type' => 'credit_agreement',
            'document_stage' => 'signed',
            'generated_file_path' => null,
            'generated_at' => null,
            'file_path' => 'loan-documents/test/credit-agreement.pdf',
            'uploaded_by' => $analyst->id,
            'uploaded_at' => now(),

            'verified_by' => $secretary->id,
            'verified_at' => now(),

            'status' => 'verified',
        ]);

        /*
         * Pastikan kedua dokumen memang sudah diverifikasi.
         */
        $this->assertNotNull(
            $approvalLetter->verified_by
        );

        $this->assertNotNull(
            $approvalLetter->verified_at
        );

        $this->assertSame(
            'verified',
            $approvalLetter->status
        );

        $this->assertNotNull(
            $creditAgreement->verified_by
        );

        $this->assertNotNull(
            $creditAgreement->verified_at
        );

        $this->assertSame(
            'verified',
            $creditAgreement->status
        );

        /*
         * ============================================================
         * 8. KONDISI AWAL
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
         * 9. LOGIN SEBAGAI TREASURER
         * ============================================================
         */
        $this->actingAs($treasurer);

        $this->assertAuthenticatedAs(
            $treasurer
        );

        /*
         * ============================================================
         * 10. LAKUKAN PENCAIRAN
         * ============================================================
         *
         * Gunakan endpoint aktual:
         *
         * POST treasurer/disbursements/{loan}
         */
        $disbursedAt = now()->format(
            'Y-m-d H:i:s'
        );

        $response = $this->post(
            route(
                'treasurer.disbursements.store',
                $loan
            ),
            [
                'account_id' => $account->id,
                'disbursed_at' => $disbursedAt,
                'notes' => 'Pencairan untuk pengujian automated test.',
            ]
        );

        /*
         * Request tidak boleh menghasilkan error server.
         */
        $this->assertNotSame(
            500,
            $response->getStatusCode()
        );

        /*
         * ============================================================
         * 11. LOAN HARUS MENJADI DISBURSED
         * ============================================================
         */
        $loan->refresh();

        $this->assertSame(
            'disbursed',
            $loan->status
        );

        /*
         * ============================================================
         * 12. LOAN DISBURSEMENT HARUS TERCATAT
         * ============================================================
         */
        $disbursement = LoanDisbursement::query()
            ->where('loan_id', $loan->id)
            ->first();

        $this->assertNotNull(
            $disbursement,
            'LoanDisbursement tidak tercatat.'
        );

        $this->assertSame(
            $treasurer->id,
            $disbursement->treasurer_id
        );

        $this->assertSame(
            $account->id,
            $disbursement->account_id
        );

        $this->assertSame(
            1800000.00,
            (float) $disbursement->amount
        );

        $this->assertNotNull(
            $disbursement->disbursed_at
        );

        /*
         * ============================================================
         * 13. CASH FLOW OUT HARUS TERCATAT
         * ============================================================
         */
        $this->assertDatabaseHas('cash_flows', [
            'loan_id' => $loan->id,
            'account_id' => $account->id,
            'type' => 'out',
            'category' => 'loan_disbursement',
            'amount' => 1800000,
        ]);

        /*
         * ============================================================
         * 14. INSTALLMENT OTOMATIS HARUS TERBENTUK
         * ============================================================
         */
        $installments = Installment::query()
            ->where('loan_id', $loan->id)
            ->orderBy('installment_number')
            ->get();

        $this->assertCount(
            12,
            $installments
        );

        /*
         * ============================================================
         * 15. NOMOR INSTALLMENT
         * ============================================================
         */
        $this->assertSame(
            range(1, 12),
            $installments
                ->pluck('installment_number')
                ->map(fn($number) => (int) $number)
                ->all()
        );

        /*
         * ============================================================
         * 16. SEMUA INSTALLMENT HARUS PENDING
         * ============================================================
         */
        $this->assertSame(
            12,
            $installments
                ->where('status', 'pending')
                ->count()
        );

        /*
         * ============================================================
         * 17. TOTAL POKOK HARUS SAMA DENGAN APPROVED AMOUNT
         * ============================================================
         */
        $totalPrincipal = $installments->sum(
            fn($installment) =>
            (float) $installment->principal_amount
        );

        $this->assertSame(
            1800000.00,
            round($totalPrincipal, 2)
        );

        /*
         * ============================================================
         * 18. TOTAL BUNGA
         * ============================================================
         *
         * 1.800.000 x 8% x (12 / 12)
         * = 144.000
         */
        $totalInterest = $installments->sum(
            fn($installment) =>
            (float) $installment->interest_amount
        );

        $this->assertSame(
            144000.00,
            round($totalInterest, 2)
        );

        /*
         * ============================================================
         * 19. TOTAL KEWAJIBAN
         * ============================================================
         */
        $totalAmount = $installments->sum(
            fn($installment) =>
            (float) $installment->total_amount
        );

        $this->assertSame(
            1944000.00,
            round($totalAmount, 2)
        );
    }
}
