<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\LoanDisbursement;
use App\Models\User;
use App\Services\LoanInstallmentService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoanInstallmentServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_monthly_loan_generates_installments_correctly(): void
    {
        /*
         * ============================================================
         * 1. SIAPKAN USER MEMBER
         * ============================================================
         */
        $member = $this->createUser(
            'member',
            'Member Monthly Service Test'
        );

        /*
         * ============================================================
         * 2. SIAPKAN TREASURER
         * ============================================================
         */
        $treasurer = $this->createUser(
            'treasurer',
            'Treasurer Monthly Service Test'
        );

        /*
         * ============================================================
         * 3. SIAPKAN ACCOUNT
         * ============================================================
         */
        $account = $this->createAccount(
            'Rekening Monthly Service Test'
        );

        /*
         * ============================================================
         * 4. BUAT LOAN MONTHLY
         * ============================================================
         *
         * Approved amount:
         * Rp1.800.000
         *
         * Interest:
         * 8% per tahun
         *
         * Tenor:
         * 12 bulan
         *
         * Flat interest:
         * 1.800.000 x 8% x (12 / 12)
         * = Rp144.000
         *
         * Total kewajiban:
         * Rp1.944.000
         *
         * Per bulan:
         * Rp162.000
         *
         * Pokok per bulan:
         * Rp150.000
         *
         * Bunga per bulan:
         * Rp12.000
         */
        $loan = $this->createLoan(
            $member,
            'monthly',
            12,
            1800000,
            8
        );

        /*
         * ============================================================
         * 5. BUAT DATA PENCAIRAN
         * ============================================================
         *
         * Jatuh tempo angsuran pertama diharapkan satu bulan
         * setelah tanggal pencairan.
         */
        $disbursedAt = Carbon::create(
            2026,
            9,
            16,
            10,
            0,
            0
        );

        LoanDisbursement::create([
            'loan_id' => $loan->id,
            'treasurer_id' => $treasurer->id,
            'account_id' => $account->id,
            'amount' => 1800000,
            'disbursed_at' => $disbursedAt,
            'notes' => 'Test pencairan monthly.',
        ]);

        /*
         * ============================================================
         * 6. GENERATE INSTALLMENTS
         * ============================================================
         */
        $service = app(
            LoanInstallmentService::class
        );

        $service->generate($loan->fresh());

        /*
         * ============================================================
         * 7. VALIDASI JUMLAH
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
         * 8. VALIDASI NOMOR ANGSURAN
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
         * 9. VALIDASI NOMINAL POKOK
         * ============================================================
         */
        foreach ($installments as $installment) {
            $this->assertSame(
                150000.00,
                (float) $installment->principal_amount,
                'Pokok setiap angsuran monthly seharusnya Rp150.000.'
            );
        }

        /*
         * ============================================================
         * 10. VALIDASI NOMINAL BUNGA
         * ============================================================
         */
        foreach ($installments as $installment) {
            $this->assertSame(
                12000.00,
                (float) $installment->interest_amount,
                'Bunga setiap angsuran monthly seharusnya Rp12.000.'
            );
        }

        /*
         * ============================================================
         * 11. VALIDASI TOTAL ANGSURAN
         * ============================================================
         */
        foreach ($installments as $installment) {
            $this->assertSame(
                162000.00,
                (float) $installment->total_amount,
                'Total setiap angsuran monthly seharusnya Rp162.000.'
            );
        }

        /*
         * ============================================================
         * 12. VALIDASI PENALTY AWAL
         * ============================================================
         */
        foreach ($installments as $installment) {
            $this->assertSame(
                0.00,
                (float) $installment->penalty_amount
            );
        }

        /*
         * ============================================================
         * 13. VALIDASI STATUS AWAL
         * ============================================================
         */
        foreach ($installments as $installment) {
            $this->assertSame(
                'pending',
                $installment->status
            );

            $this->assertNull(
                $installment->paid_at
            );
        }

        /*
         * ============================================================
         * 14. VALIDASI TOTAL POKOK
         * ============================================================
         */
        $totalPrincipal = $installments->sum(
            fn($installment) => (float) $installment->principal_amount
        );

        $this->assertSame(
            1800000.00,
            round($totalPrincipal, 2)
        );

        /*
         * ============================================================
         * 15. VALIDASI TOTAL BUNGA
         * ============================================================
         */
        $totalInterest = $installments->sum(
            fn($installment) => (float) $installment->interest_amount
        );

        $this->assertSame(
            144000.00,
            round($totalInterest, 2)
        );

        /*
         * ============================================================
         * 16. VALIDASI TOTAL KEWAJIBAN
         * ============================================================
         */
        $totalInstallment = $installments->sum(
            fn($installment) => (float) $installment->total_amount
        );

        $this->assertSame(
            1944000.00,
            round($totalInstallment, 2)
        );

        /*
         * ============================================================
         * 17. VALIDASI JATUH TEMPO
         * ============================================================
         */
        foreach ($installments as $installment) {
            $expectedDueDate = $disbursedAt
                ->copy()
                ->addMonthsNoOverflow(
                    (int) $installment->installment_number
                )
                ->toDateString();

            $actualDueDate = Carbon::parse(
                $installment->due_date
            )->toDateString();

            $this->assertSame(
                $expectedDueDate,
                $actualDueDate
            );
        }
    }

    public function test_lump_sum_loan_generates_one_installment_correctly(): void
    {
        /*
         * ============================================================
         * 1. SIAPKAN USER
         * ============================================================
         */
        $member = $this->createUser(
            'member',
            'Member Lump Sum Service Test'
        );

        $treasurer = $this->createUser(
            'treasurer',
            'Treasurer Lump Sum Service Test'
        );

        /*
         * ============================================================
         * 2. ACCOUNT
         * ============================================================
         */
        $account = $this->createAccount(
            'Rekening Lump Sum Service Test'
        );

        /*
         * ============================================================
         * 3. BUAT LOAN LUMP SUM
         * ============================================================
         *
         * Approved amount:
         * Rp1.800.000
         *
         * Interest:
         * 8% per tahun
         *
         * Tenor:
         * 6 bulan
         *
         * Bunga:
         * 1.800.000 x 8% x (6 / 12)
         * = Rp72.000
         *
         * Total:
         * Rp1.872.000
         */
        $loan = $this->createLoan(
            $member,
            'lump_sum',
            6,
            1800000,
            8
        );

        $disbursedAt = Carbon::create(
            2026,
            9,
            16,
            10,
            0,
            0
        );

        LoanDisbursement::create([
            'loan_id' => $loan->id,
            'treasurer_id' => $treasurer->id,
            'account_id' => $account->id,
            'amount' => 1800000,
            'disbursed_at' => $disbursedAt,
            'notes' => 'Test pencairan lump sum.',
        ]);

        /*
         * ============================================================
         * 4. GENERATE
         * ============================================================
         */
        $service = app(
            LoanInstallmentService::class
        );

        $service->generate($loan->fresh());

        /*
         * ============================================================
         * 5. JUMLAH ANGSURAN HARUS SATU
         * ============================================================
         */
        $installments = Installment::query()
            ->where('loan_id', $loan->id)
            ->orderBy('installment_number')
            ->get();

        $this->assertCount(
            1,
            $installments
        );

        $installment = $installments->first();

        /*
         * ============================================================
         * 6. NOMOR ANGSURAN
         * ============================================================
         */
        $this->assertSame(
            1,
            (int) $installment->installment_number
        );

        /*
         * ============================================================
         * 7. POKOK
         * ============================================================
         */
        $this->assertSame(
            1800000.00,
            (float) $installment->principal_amount
        );

        /*
         * ============================================================
         * 8. BUNGA
         * ============================================================
         */
        $this->assertSame(
            72000.00,
            (float) $installment->interest_amount
        );

        /*
         * ============================================================
         * 9. PENALTY
         * ============================================================
         */
        $this->assertSame(
            0.00,
            (float) $installment->penalty_amount
        );

        /*
         * ============================================================
         * 10. TOTAL
         * ============================================================
         */
        $this->assertSame(
            1872000.00,
            (float) $installment->total_amount
        );

        /*
         * ============================================================
         * 11. STATUS
         * ============================================================
         */
        $this->assertSame(
            'pending',
            $installment->status
        );

        $this->assertNull(
            $installment->paid_at
        );

        /*
         * ============================================================
         * 12. JATUH TEMPO
         * ============================================================
         */
        $expectedDueDate = $disbursedAt
            ->copy()
            ->addMonthsNoOverflow(6)
            ->toDateString();

        $actualDueDate = Carbon::parse(
            $installment->due_date
        )->toDateString();

        $this->assertSame(
            $expectedDueDate,
            $actualDueDate
        );
    }

    public function test_generate_does_not_create_duplicate_installments(): void
    {
        /*
         * ============================================================
         * 1. SIAPKAN USER
         * ============================================================
         */
        $member = $this->createUser(
            'member',
            'Member Idempotent Service Test'
        );

        $treasurer = $this->createUser(
            'treasurer',
            'Treasurer Idempotent Service Test'
        );

        /*
         * ============================================================
         * 2. ACCOUNT
         * ============================================================
         */
        $account = $this->createAccount(
            'Rekening Idempotent Service Test'
        );

        /*
         * ============================================================
         * 3. LOAN
         * ============================================================
         */
        $loan = $this->createLoan(
            $member,
            'monthly',
            12,
            1800000,
            8
        );

        $disbursedAt = Carbon::create(
            2026,
            9,
            16,
            10,
            0,
            0
        );

        LoanDisbursement::create([
            'loan_id' => $loan->id,
            'treasurer_id' => $treasurer->id,
            'account_id' => $account->id,
            'amount' => 1800000,
            'disbursed_at' => $disbursedAt,
            'notes' => 'Test idempotensi generate installment.',
        ]);

        /*
         * ============================================================
         * 4. GENERATE PERTAMA
         * ============================================================
         */
        $service = app(
            LoanInstallmentService::class
        );

        $service->generate($loan->fresh());

        $firstCount = Installment::query()
            ->where('loan_id', $loan->id)
            ->count();

        $this->assertSame(
            12,
            $firstCount
        );

        /*
         * Simpan ID installment untuk memastikan record lama
         * tidak diganti atau dibuat ulang.
         */
        $firstIds = Installment::query()
            ->where('loan_id', $loan->id)
            ->orderBy('installment_number')
            ->pluck('id')
            ->all();

        /*
         * ============================================================
         * 5. GENERATE KEDUA
         * ============================================================
         */
        $service->generate($loan->fresh());

        $secondCount = Installment::query()
            ->where('loan_id', $loan->id)
            ->count();

        /*
         * Jumlah harus tetap 12.
         */
        $this->assertSame(
            12,
            $secondCount
        );

        /*
         * ============================================================
         * 6. ID HARUS TETAP SAMA
         * ============================================================
         */
        $secondIds = Installment::query()
            ->where('loan_id', $loan->id)
            ->orderBy('installment_number')
            ->pluck('id')
            ->all();

        $this->assertSame(
            $firstIds,
            $secondIds
        );
    }

    public function test_monthly_loan_corrects_rounding_on_final_installment(): void
    {
        /*
     * ============================================================
     * 1. SIAPKAN USER
     * ============================================================
     */
        $member = $this->createUser(
            'member',
            'Member Rounding Service Test'
        );

        $treasurer = $this->createUser(
            'treasurer',
            'Treasurer Rounding Service Test'
        );

        /*
     * ============================================================
     * 2. ACCOUNT
     * ============================================================
     */
        $account = $this->createAccount(
            'Rekening Rounding Service Test'
        );

        /*
     * ============================================================
     * 3. BUAT LOAN DENGAN NILAI YANG MEMERLUKAN PEMBULATAN
     * ============================================================
     *
     * Approved amount = Rp1.000.000
     * Interest       = 8% per tahun
     * Tenor          = 12 bulan
     *
     * Total bunga:
     * Rp1.000.000 x 8% = Rp80.000
     *
     * Total kewajiban:
     * Rp1.080.000
     *
     * Pembagian matematis:
     *
     * Pokok:
     * Rp1.000.000 / 12 = Rp83.333,333...
     *
     * Bunga:
     * Rp80.000 / 12 = Rp6.666,666...
     *
     * Service menggunakan pembulatan rupiah utuh kemudian
     * mengoreksi sisa pada angsuran terakhir.
     */
        $loan = $this->createLoan(
            $member,
            'monthly',
            12,
            1000000,
            8
        );

        $disbursedAt = Carbon::create(
            2026,
            9,
            16,
            10,
            0,
            0
        );

        LoanDisbursement::create([
            'loan_id' => $loan->id,
            'treasurer_id' => $treasurer->id,
            'account_id' => $account->id,
            'amount' => 1000000,
            'disbursed_at' => $disbursedAt,
            'notes' => 'Test pembulatan installment.',
        ]);

        /*
     * ============================================================
     * 4. GENERATE
     * ============================================================
     */
        $service = app(
            LoanInstallmentService::class
        );

        $service->generate(
            $loan->fresh()
        );

        /*
     * ============================================================
     * 5. AMBIL INSTALLMENTS
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
     * 6. VALIDASI 11 ANGSURAN PERTAMA
     * ============================================================
     *
     * Service membulatkan ke rupiah utuh:
     *
     * Pokok  = Rp83.333
     * Bunga  = Rp6.667
     * Total  = Rp90.000
     */
        $firstEleven = $installments->take(11);

        foreach ($firstEleven as $installment) {
            $this->assertSame(
                83333.00,
                (float) $installment->principal_amount
            );

            $this->assertSame(
                6667.00,
                (float) $installment->interest_amount
            );

            $this->assertSame(
                90000.00,
                (float) $installment->total_amount
            );
        }

        /*
     * ============================================================
     * 7. VALIDASI ANGSURAN TERAKHIR
     * ============================================================
     *
     * Sisa pembulatan dikoreksi pada angsuran terakhir:
     *
     * Pokok  = Rp83.337
     * Bunga  = Rp6.663
     * Total  = Rp90.000
     */
        $lastInstallment = $installments->last();

        $this->assertSame(
            83337.00,
            (float) $lastInstallment->principal_amount
        );

        $this->assertSame(
            6663.00,
            (float) $lastInstallment->interest_amount
        );

        $this->assertSame(
            90000.00,
            (float) $lastInstallment->total_amount
        );

        /*
     * ============================================================
     * 8. TOTAL POKOK HARUS TEPAT
     * ============================================================
     */
        $totalPrincipal = $installments->sum(
            fn($installment) =>
            (float) $installment->principal_amount
        );

        $this->assertSame(
            1000000.00,
            round($totalPrincipal, 2)
        );

        /*
     * ============================================================
     * 9. TOTAL BUNGA HARUS TEPAT
     * ============================================================
     */
        $totalInterest = $installments->sum(
            fn($installment) =>
            (float) $installment->interest_amount
        );

        $this->assertSame(
            80000.00,
            round($totalInterest, 2)
        );

        /*
     * ============================================================
     * 10. TOTAL KEWAJIBAN HARUS TEPAT
     * ============================================================
     */
        $totalAmount = $installments->sum(
            fn($installment) =>
            (float) $installment->total_amount
        );

        $this->assertSame(
            1080000.00,
            round($totalAmount, 2)
        );
    }

    /*
     * ================================================================
     * HELPER: BUAT USER
     * ================================================================
     */
    private function createUser(
        string $role,
        string $name
    ): User {
        return User::create([
            'name' => $name,
            'nickname' => strtolower(
                str_replace(
                    ' ',
                    '_',
                    $name
                )
            ) . '_' . uniqid(),

            'phone' => '08' . random_int(
                1000000000,
                9999999999
            ),

            'gender' => 'L',

            'password' => Hash::make(
                'password'
            ),

            'role' => $role,
            'status' => 'active',
        ]);
    }

    /*
     * ================================================================
     * HELPER: BUAT ACCOUNT
     * ================================================================
     */
    private function createAccount(
        string $name
    ): Account {
        return Account::create([
            'name' => $name,
            'type' => 'bank',
            'bank_name' => 'Bank Test',
            'account_number' => 'TEST-' . uniqid(),
            'account_name' => 'KOPKARMADA TEST',
            'is_active' => true,
        ]);
    }

    /*
     * ================================================================
     * HELPER: BUAT LOAN
     * ================================================================
     */
    private function createLoan(
        User $member,
        string $repaymentType,
        int $termMonths,
        float $approvedAmount,
        float $interestRate
    ): Loan {
        $sequenceNumber = ((int) Loan::max(
            'sequence_number'
        )) + 1;

        return Loan::create([
            'code' => 'KOPKARMADA/TEST-SERVICE/' .
                now()->format('YmdHis') .
                '/' .
                $sequenceNumber,

            'sequence_number' => $sequenceNumber,
            'user_id' => $member->id,
            'submitted_at' => now(),

            'requested_amount' => $approvedAmount,

            'purpose_category' => 'consumer',
            'business_type' => null,
            'business_type_other' => null,
            'purpose_description' => 'Pengujian LoanInstallmentService.',

            'term_months' => $termMonths,
            'repayment_type' => $repaymentType,
            'monthly_installment' => $repaymentType === 'monthly'
                ? 162000
                : null,

            'work_unit' => 'Unit Test',
            'position' => 'Tester',
            'employment_duration_years' => 1,

            'other_monthly_income' => 0,

            'salary_slip' => 'private/test/salary-slip/test.pdf',

            'interest_rate' => $interestRate,
            'approved_amount' => $approvedAmount,

            'status' => 'disbursed',
        ]);
    }
}
