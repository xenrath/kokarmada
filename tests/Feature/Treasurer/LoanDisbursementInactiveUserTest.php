<?php

namespace Tests\Feature\Treasurer;

use App\Models\Account;
use App\Models\CashFlow;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\LoanDisbursement;
use App\Models\LoanDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoanDisbursementInactiveUserTest extends TestCase
{
    use DatabaseTransactions;

    public function test_inactive_treasurer_cannot_disburse_loan(): void
    {
        /*
         * ============================================================
         * 1. BUAT MEMBER
         * ============================================================
         */
        $member = User::create([
            'name' => 'Member Inactive Treasurer Test',
            'nickname' => 'member_inactive_treasurer_' . uniqid(),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'L',
            'password' => Hash::make('password'),
            'role' => 'member',
            'status' => 'active',
        ]);

        $this->assertNotNull($member->id);
        $this->assertTrue($member->isMember());

        /*
         * ============================================================
         * 2. BUAT ANALYST MANAGER
         * ============================================================
         *
         * Digunakan sebagai uploader dokumen.
         */
        $analyst = User::create([
            'name' => 'Analyst Inactive Treasurer Test',
            'nickname' => 'analyst_inactive_treasurer_' . uniqid(),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'L',
            'password' => Hash::make('password'),
            'role' => 'analyst_manager',
            'status' => 'active',
        ]);

        $this->assertNotNull($analyst->id);
        $this->assertTrue($analyst->isAnalystManager());

        /*
         * ============================================================
         * 3. BUAT SECRETARY
         * ============================================================
         *
         * Digunakan sebagai verifier dokumen.
         */
        $secretary = User::create([
            'name' => 'Secretary Inactive Treasurer Test',
            'nickname' => 'secretary_inactive_treasurer_' . uniqid(),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'P',
            'password' => Hash::make('password'),
            'role' => 'secretary',
            'status' => 'active',
        ]);

        $this->assertNotNull($secretary->id);
        $this->assertTrue($secretary->isSecretary());

        /*
         * ============================================================
         * 4. BUAT TREASURER TIDAK AKTIF
         * ============================================================
         *
         * Role benar sebagai treasurer,
         * tetapi status inactive.
         */
        $treasurer = User::create([
            'name' => 'Inactive Treasurer Test',
            'nickname' => 'treasurer_inactive_' . uniqid(),
            'phone' => '08' . random_int(1000000000, 9999999999),
            'gender' => 'L',
            'password' => Hash::make('password'),
            'role' => 'treasurer',
            'status' => 'inactive',
        ]);

        $this->assertNotNull($treasurer->id);
        $this->assertTrue($treasurer->isTreasurer());
        $this->assertFalse($treasurer->isActive());

        /*
         * ============================================================
         * 5. BUAT ACCOUNT AKTIF
         * ============================================================
         *
         * Account sengaja aktif agar bukan account yang menjadi
         * alasan request ditolak.
         */
        $account = Account::create([
            'name' => 'Rekening Inactive Treasurer Test',
            'type' => 'bank',
            'bank_name' => 'Bank Test',
            'account_number' => 'INACTIVE-TREASURER-' . uniqid(),
            'account_name' => 'KOPKARMADA INACTIVE TREASURER TEST',
            'is_active' => true,
        ]);

        $this->assertNotNull($account->id);
        $this->assertTrue((bool) $account->is_active);

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
            'purpose_description' => 'Pengujian pencairan oleh Treasurer tidak aktif.',

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
        $this->assertSame('approved', $loan->status);
        $this->assertSame(1800000.00, (float) $loan->approved_amount);

        /*
         * ============================================================
         * 7. DOKUMEN WAJIB SUDAH VERIFIED
         * ============================================================
         *
         * Kedua dokumen dibuat verified supaya request secara bisnis
         * sebenarnya sudah memenuhi syarat pencairan.
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

        $this->assertSame('verified', $approvalLetter->status);
        $this->assertSame('verified', $creditAgreement->status);

        $this->assertNotNull($approvalLetter->verified_by);
        $this->assertNotNull($approvalLetter->verified_at);

        $this->assertNotNull($creditAgreement->verified_by);
        $this->assertNotNull($creditAgreement->verified_at);

        /*
         * ============================================================
         * 8. KONDISI AWAL
         * ============================================================
         *
         * Belum ada pencairan, cash flow, maupun installment.
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
         * 9. LOGIN SEBAGAI TREASURER INACTIVE
         * ============================================================
         */
        $this->actingAs($treasurer);

        $this->assertAuthenticatedAs($treasurer);

        /*
         * ============================================================
         * 10. COBA AKSES ENDPOINT PENCAIRAN
         * ============================================================
         *
         * Middleware ActiveUser seharusnya menghentikan user
         * sebelum proses disbursement dijalankan.
         */
        $response = $this->post(
            route('treasurer.disbursements.store', $loan),
            [
                'account_id' => $account->id,
                'disbursed_at' => now()->format('Y-m-d H:i:s'),
                'notes' => 'Percobaan pencairan oleh Treasurer tidak aktif.',
            ]
        );

        /*
         * Kita tidak mengunci HTTP status tertentu karena middleware
         * dapat melakukan redirect/logout sesuai implementasi.
         *
         * Yang penting:
         * - bukan 500
         * - request tidak dianggap sukses
         * - user tidak lagi authenticated
         */
        $this->assertNotSame(
            500,
            $response->getStatusCode()
        );

        $this->assertFalse(
            $response->isSuccessful()
        );

        $this->assertGuest();

        /*
         * ============================================================
         * 11. LOAN TIDAK BOLEH BERUBAH
         * ============================================================
         */
        $loan->refresh();

        $this->assertSame(
            'approved',
            $loan->status
        );

        /*
         * ============================================================
         * 12. TIDAK ADA DISBURSEMENT
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
         * 13. TIDAK ADA CASH FLOW OUT
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
         * 14. TIDAK ADA INSTALLMENT
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
         * 15. ACCOUNT TETAP AKTIF
         * ============================================================
         *
         * Memastikan tidak ada perubahan terhadap sumber dana.
         */
        $account->refresh();

        $this->assertTrue(
            (bool) $account->is_active
        );
    }
}
