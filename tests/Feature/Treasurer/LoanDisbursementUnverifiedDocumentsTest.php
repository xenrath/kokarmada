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

class LoanDisbursementUnverifiedDocumentsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_treasurer_cannot_disburse_loan_when_required_document_is_not_verified(): void
    {
        /*
         * ============================================================
         * 1. BUAT MEMBER PEMILIK PINJAMAN
         * ============================================================
         */
        $member = User::create([
            'name' => 'Member Unverified Document Test',
            'nickname' => 'member_unverified_document_' . uniqid(),
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
         * Digunakan sebagai pengunggah dokumen.
         */
        $analyst = User::create([
            'name' => 'Analyst Unverified Document Test',
            'nickname' => 'analyst_unverified_document_' . uniqid(),
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
            'name' => 'Secretary Unverified Document Test',
            'nickname' => 'secretary_unverified_document_' . uniqid(),
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
            'name' => 'Treasurer Unverified Document Test',
            'nickname' => 'treasurer_unverified_document_' . uniqid(),
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
            'name' => 'Rekening Unverified Document Test',
            'type' => 'bank',
            'bank_name' => 'Bank Test',
            'account_number' => 'UNVERIFIED-DOCUMENT-' . uniqid(),
            'account_name' => 'KOPKARMADA UNVERIFIED DOCUMENT TEST',
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
            'purpose_description' => 'Pengujian pencairan dengan dokumen belum terverifikasi.',

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
            'approved',
            $loan->status
        );

        $this->assertSame(
            1800000.00,
            (float) $loan->approved_amount
        );

        /*
         * ============================================================
         * 7. DOKUMEN WAJIB
         * ============================================================
         *
         * Dokumen 1:
         * approval_letter -> sudah diverifikasi
         *
         * Dokumen 2:
         * credit_agreement -> BELUM diverifikasi
         *
         * Artinya persyaratan pencairan belum terpenuhi.
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

            'verified_by' => null,
            'verified_at' => null,

            'status' => 'pending',
        ]);

        /*
         * Pastikan kondisi dokumen sesuai skenario test.
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

        $this->assertNull(
            $creditAgreement->verified_by
        );

        $this->assertNull(
            $creditAgreement->verified_at
        );

        $this->assertSame(
            'pending',
            $creditAgreement->status
        );

        /*
         * ============================================================
         * 8. KONDISI AWAL SEBELUM PENCAIRAN
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
         * 10. COBA MELAKUKAN PENCAIRAN
         * ============================================================
         *
         * Account aktif.
         * Loan approved.
         *
         * Satu-satunya persyaratan yang sengaja tidak terpenuhi
         * adalah verifikasi credit_agreement.
         */
        $response = $this->post(
            route(
                'treasurer.disbursements.store',
                $loan
            ),
            [
                'account_id' => $account->id,
                'disbursed_at' => now()->format('Y-m-d H:i:s'),
                'notes' => 'Percobaan pencairan dengan dokumen belum terverifikasi.',
            ]
        );

        /*
         * Request harus ditolak.
         *
         * Kita tidak mengunci kode HTTP di tahap ini karena
         * implementasi controller dapat memilih 404 atau redirect
         * dengan pesan error.
         */
        $this->assertNotSame(
            500,
            $response->getStatusCode()
        );

        $this->assertFalse(
            $response->isSuccessful()
        );

        /*
         * ============================================================
         * 11. LOAN HARUS TETAP APPROVED
         * ============================================================
         */
        $loan->refresh();

        $this->assertSame(
            'approved',
            $loan->status
        );

        /*
         * ============================================================
         * 12. TIDAK BOLEH ADA LOAN DISBURSEMENT
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
         * 13. TIDAK BOLEH ADA CASH FLOW OUT
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
         * 14. INSTALLMENT TIDAK BOLEH TERBENTUK
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
         * 15. DOKUMEN TETAP BELUM TERVERIFIKASI
         * ============================================================
         */
        $creditAgreement->refresh();

        $this->assertNull(
            $creditAgreement->verified_by
        );

        $this->assertNull(
            $creditAgreement->verified_at
        );

        $this->assertSame(
            'pending',
            $creditAgreement->status
        );
    }
}
