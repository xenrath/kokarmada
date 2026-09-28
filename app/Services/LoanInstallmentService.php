<?php

namespace App\Services;

use App\Models\Installment;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class LoanInstallmentService
{
    public function generate(Loan $loan): void
    {
        if ($loan->status !== 'disbursed') {
            throw new RuntimeException(
                'Angsuran hanya dapat dibuat setelah pinjaman dicairkan.'
            );
        }

        if (! $loan->approved_amount || $loan->approved_amount <= 0) {
            throw new RuntimeException(
                'Nominal pinjaman yang disetujui tidak valid.'
            );
        }

        if (! $loan->term_months || $loan->term_months <= 0) {
            throw new RuntimeException(
                'Jangka waktu pinjaman tidak valid.'
            );
        }

        $loan->loadMissing('disbursement');

        $disbursement = $loan->disbursement;

        if (! $disbursement || ! $disbursement->disbursed_at) {
            throw new RuntimeException(
                'Tanggal pencairan belum tersedia.'
            );
        }

        $disbursedAt = Carbon::parse(
            $disbursement->disbursed_at
        );

        if ($loan->installments()->exists()) {
            return;
        }

        if ($loan->repayment_type === 'lump_sum') {
            $this->generateLumpSum(
                $loan,
                $disbursedAt
            );

            return;
        }

        if ($loan->repayment_type !== 'monthly') {
            throw new RuntimeException(
                'Jenis pembayaran pinjaman tidak valid.'
            );
        }

        $this->generateMonthly(
            $loan,
            $disbursedAt
        );
    }

    private function generateMonthly(
        Loan $loan,
        $disbursedAt
    ): void {
        $principalTotal = (float) $loan->approved_amount;
        $termMonths = (int) $loan->term_months;
        $interestRate = (float) $loan->interest_rate;

        /*
         * Bunga flat sederhana:
         *
         * total bunga =
         * approved_amount × bunga tahunan × tenor / 12
         */
        $interestTotal = round(
            $principalTotal
                * ($interestRate / 100)
                * ($termMonths / 12),
            0
        );

        $principalPerInstallment = round(
            $principalTotal / $termMonths,
            0
        );

        $interestPerInstallment = round(
            $interestTotal / $termMonths,
            0
        );

        $principalAllocated = 0;
        $interestAllocated = 0;

        $rows = [];

        $disbursedAt = $disbursedAt->copy();

        for ($number = 1; $number <= $termMonths; $number++) {
            $principalAmount = $principalPerInstallment;
            $interestAmount = $interestPerInstallment;

            /*
             * Penyesuaian angsuran terakhir untuk mengatasi
             * kemungkinan pembulatan.
             */
            if ($number === $termMonths) {
                $principalAmount = $principalTotal
                    - $principalAllocated;

                $interestAmount = $interestTotal
                    - $interestAllocated;
            }

            $principalAmount = round($principalAmount, 0);
            $interestAmount = round($interestAmount, 0);

            $totalAmount =
                $principalAmount +
                $interestAmount;

            $dueDate = $disbursedAt
                ->copy()
                ->addMonthsNoOverflow($number)
                ->startOfDay();

            $rows[] = [
                'loan_id' => $loan->id,
                'installment_number' => $number,
                'due_date' => $dueDate,
                'principal_amount' => $principalAmount,
                'interest_amount' => $interestAmount,
                'penalty_amount' => 0,
                'total_amount' => $totalAmount,
                'paid_at' => null,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $principalAllocated += $principalAmount;
            $interestAllocated += $interestAmount;
        }

        DB::table('installments')->insert($rows);
    }

    private function generateLumpSum(
        Loan $loan,
        $disbursedAt
    ): void {
        $principalAmount = (float) $loan->approved_amount;
        $interestRate = (float) $loan->interest_rate;
        $termMonths = (int) $loan->term_months;

        $interestAmount = round(
            $principalAmount
                * ($interestRate / 100)
                * ($termMonths / 12),
            0
        );

        $totalAmount =
            $principalAmount +
            $interestAmount;

        $dueDate = $disbursedAt
            ->copy()
            ->addMonthsNoOverflow($termMonths)
            ->startOfDay();

        Installment::create([
            'loan_id' => $loan->id,
            'installment_number' => 1,
            'due_date' => $dueDate,
            'principal_amount' => $principalAmount,
            'interest_amount' => $interestAmount,
            'penalty_amount' => 0,
            'total_amount' => $totalAmount,
            'paid_at' => null,
            'status' => 'pending',
        ]);
    }
}
