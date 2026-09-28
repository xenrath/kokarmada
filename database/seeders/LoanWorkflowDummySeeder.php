<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Loan;
use App\Models\LoanAnalysis;
use App\Models\LoanDocument;
use App\Models\LoanProcess;
use App\Models\LoanTreasurerReview;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LoanWorkflowDummySeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            /*
             * =========================================================
             * USER
             * =========================================================
             */

            $member = User::where('phone', '080000000098')
                ->where('role', 'member')
                ->where('status', 'active')
                ->firstOrFail();

            $analyst = User::where('role', 'analyst_manager')
                ->where('status', 'active')
                ->firstOrFail();

            $treasurer = User::where('role', 'treasurer')
                ->where('status', 'active')
                ->firstOrFail();

            $chairman = User::where('role', 'chairman')
                ->where('status', 'active')
                ->firstOrFail();

            $secretary = User::where('role', 'secretary')
                ->where('status', 'active')
                ->firstOrFail();


            /*
             * =========================================================
             * NOMOR PINJAMAN
             * =========================================================
             */

            $lastSequence = Loan::lockForUpdate()
                ->max('sequence_number');

            $sequenceNumber = $lastSequence
                ? $lastSequence + 1
                : 100;

            $month = [
                1 => 'I',
                2 => 'II',
                3 => 'III',
                4 => 'IV',
                5 => 'V',
                6 => 'VI',
                7 => 'VII',
                8 => 'VIII',
                9 => 'IX',
                10 => 'X',
                11 => 'XI',
                12 => 'XII',
            ][now()->month];

            $code = 'KOPKARMADA/' .
                $sequenceNumber .
                '/' .
                $month .
                '/' .
                now()->year;


            /*
             * =========================================================
             * 1. PENGAJUAN MEMBER
             * =========================================================
             */

            $salarySlipPath =
                'private/loans/' .
                $sequenceNumber .
                '/salary-slip/dummy-slip-gaji.pdf';

            Storage::disk('local')->put(
                $salarySlipPath,
                "%PDF-1.4\n% Dummy Slip Gaji KOPKARMADA\n"
            );

            $loan = Loan::create([
                'code' => $code,
                'sequence_number' => $sequenceNumber,
                'user_id' => $member->id,
                'submitted_at' => now(),

                'requested_amount' => 2400000,

                'purpose_category' => 'consumer',
                'business_type' => null,
                'business_type_other' => null,
                'purpose_description' =>
                'Keperluan konsumtif untuk kebutuhan keluarga.',

                'term_months' => 12,
                'repayment_type' => 'monthly',

                'monthly_installment' => null,

                'work_unit' => 'Universitas Bhamada Slawi',
                'position' => 'Pegawai',
                'employment_duration_years' => 5,

                'other_monthly_income' => 0,
                'other_income_proof' => null,

                'net_monthly_income' => 4500000,

                'salary_slip' => $salarySlipPath,

                'interest_rate' => 8,

                'approved_amount' => null,

                'status' => 'submitted',
            ]);

            LoanProcess::create([
                'loan_id' => $loan->id,
                'user_id' => $member->id,
                'role' => 'member',
                'action' => 'submitted',
                'notes' => 'Anggota mengajukan pinjaman.',
            ]);

            Activity::create([
                'user_id' => $member->id,
                'action' => 'loan_submitted',
                'subject_type' => Loan::class,
                'subject_id' => $loan->id,
                'description' => 'Mengajukan pinjaman ' . $loan->code,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'LoanWorkflowDummySeeder',
            ]);


            /*
             * =========================================================
             * 2. ANALYST MANAGER
             * =========================================================
             */

            LoanAnalysis::create([
                'loan_id' => $loan->id,
                'analyst_id' => $analyst->id,
                'recommended_amount' => 2400000,
                'notes' =>
                'Kemampuan pembayaran cukup. ' .
                    'Direkomendasikan sesuai nominal pengajuan.',
                'reviewed_at' => now(),
            ]);

            LoanProcess::create([
                'loan_id' => $loan->id,
                'user_id' => $analyst->id,
                'role' => 'analyst_manager',
                'action' => 'analysis_completed',
                'notes' => 'Analisis pinjaman telah diselesaikan.',
            ]);

            Activity::create([
                'user_id' => $analyst->id,
                'action' => 'loan_analysis_completed',
                'subject_type' => Loan::class,
                'subject_id' => $loan->id,
                'description' =>
                'Analisis pinjaman ' . $loan->code .
                    ' telah diselesaikan.',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'LoanWorkflowDummySeeder',
            ]);

            $loan->update([
                'status' => 'waiting_treasurer_review',
            ]);


            /*
             * =========================================================
             * 3. TREASURER REVIEW
             * =========================================================
             */

            LoanTreasurerReview::create([
                'loan_id' => $loan->id,
                'treasurer_id' => $treasurer->id,
                'recommended_amount' => 2000000,
                'notes' =>
                'Berdasarkan hasil review, nominal yang disarankan ' .
                    'sebesar Rp2.000.000.',
                'reviewed_at' => now(),
            ]);

            LoanProcess::create([
                'loan_id' => $loan->id,
                'user_id' => $treasurer->id,
                'role' => 'treasurer',
                'action' => 'review_completed',
                'notes' => 'Review Treasurer telah diselesaikan.',
            ]);

            Activity::create([
                'user_id' => $treasurer->id,
                'action' => 'loan_treasurer_review_completed',
                'subject_type' => Loan::class,
                'subject_id' => $loan->id,
                'description' =>
                'Review Treasurer untuk pinjaman ' .
                    $loan->code .
                    ' telah diselesaikan.',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'LoanWorkflowDummySeeder',
            ]);

            $loan->update([
                'status' => 'waiting_chairman_approval',
            ]);


            /*
             * =========================================================
             * 4. CHAIRMAN APPROVAL
             * =========================================================
             *
             * Requested = 2.400.000
             * Analyst   = 2.400.000
             * Treasurer = 2.000.000
             * Chairman  = 1.800.000
             *
             * Bunga 8% / tahun
             * Tenor 12 bulan
             * Bunga total = 144.000
             * Total       = 1.944.000
             * Cicilan     = 162.000
             */

            $approvedAmount = 1800000;

            $monthlyInstallment = 162000;

            $loan->update([
                'approved_amount' => $approvedAmount,
                'monthly_installment' => $monthlyInstallment,
                'status' => 'approved',
            ]);

            LoanProcess::create([
                'loan_id' => $loan->id,
                'user_id' => $chairman->id,
                'role' => 'chairman',
                'action' => 'loan_approved',
                'notes' =>
                'Pinjaman disetujui sebesar Rp1.800.000.',
            ]);

            Activity::create([
                'user_id' => $chairman->id,
                'action' => 'loan_approved',
                'subject_type' => Loan::class,
                'subject_id' => $loan->id,
                'description' =>
                'Pinjaman ' . $loan->code .
                    ' disetujui Chairman sebesar Rp1.800.000.',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'LoanWorkflowDummySeeder',
            ]);


            /*
             * =========================================================
             * 5. DOKUMEN BERTANDA TANGAN
             * =========================================================
             */

            $approvalGeneratedPath =
                'loan-documents/' .
                $loan->id .
                '/generated/surat-persetujuan-kredit.pdf';

            $creditAgreementGeneratedPath =
                'loan-documents/' .
                $loan->id .
                '/generated/surat-perjanjian-kredit.pdf';

            $approvalSignedPath =
                'loan-documents/' .
                $loan->id .
                '/signed/dummy-surat-persetujuan.pdf';

            $creditAgreementSignedPath =
                'loan-documents/' .
                $loan->id .
                '/signed/dummy-surat-perjanjian.pdf';


            Storage::disk('local')->put(
                $approvalGeneratedPath,
                "%PDF-1.4\n% Dummy Surat Persetujuan Kredit\n"
            );

            Storage::disk('local')->put(
                $creditAgreementGeneratedPath,
                "%PDF-1.4\n% Dummy Surat Perjanjian Kredit\n"
            );

            Storage::disk('local')->put(
                $approvalSignedPath,
                "%PDF-1.4\n% Dummy Scan Surat Persetujuan Bertanda Tangan\n"
            );

            Storage::disk('local')->put(
                $creditAgreementSignedPath,
                "%PDF-1.4\n% Dummy Scan Surat Perjanjian Bertanda Tangan\n"
            );


            /*
             * =========================================================
             * SURAT PERSETUJUAN
             * =========================================================
             */

            LoanDocument::create([
                'loan_id' => $loan->id,
                'document_type' => 'approval_letter',
                'document_stage' => 'signed',
                'generated_file_path' => $approvalGeneratedPath,
                'generated_at' => now(),
                'file_path' => $approvalSignedPath,
                'uploaded_by' => $analyst->id,
                'uploaded_at' => now(),
                'verified_by' => $secretary->id,
                'verified_at' => now(),
                'status' => 'verified',
            ]);


            /*
             * =========================================================
             * SURAT PERJANJIAN
             * =========================================================
             */

            LoanDocument::create([
                'loan_id' => $loan->id,
                'document_type' => 'credit_agreement',
                'document_stage' => 'signed',
                'generated_file_path' => $creditAgreementGeneratedPath,
                'generated_at' => now(),
                'file_path' => $creditAgreementSignedPath,
                'uploaded_by' => $analyst->id,
                'uploaded_at' => now(),
                'verified_by' => $secretary->id,
                'verified_at' => now(),
                'status' => 'verified',
            ]);


            LoanProcess::create([
                'loan_id' => $loan->id,
                'user_id' => $analyst->id,
                'role' => 'analyst_manager',
                'action' => 'documents_generated',
                'notes' =>
                'Surat Persetujuan Kredit dan Surat Perjanjian Kredit berhasil dibuat.',
            ]);

            LoanProcess::create([
                'loan_id' => $loan->id,
                'user_id' => $analyst->id,
                'role' => 'analyst_manager',
                'action' => 'documents_uploaded_complete',
                'notes' =>
                'Seluruh dokumen kredit telah diunggah.',
            ]);

            LoanProcess::create([
                'loan_id' => $loan->id,
                'user_id' => $secretary->id,
                'role' => 'secretary',
                'action' => 'documents_verified_complete',
                'notes' =>
                'Seluruh dokumen kredit telah diverifikasi Secretary.',
            ]);


            /*
             * =========================================================
             * NOTIFIKASI TREASURER
             * =========================================================
             */

            Notification::create([
                'user_id' => $treasurer->id,
                'type' => 'loan_disbursement',
                'title' => 'Pinjaman Siap Dicairkan',
                'message' =>
                'Pinjaman ' . $loan->code .
                    ' telah melalui seluruh proses dan siap dicairkan.',
                'reference_type' => Loan::class,
                'reference_id' => $loan->id,
            ]);


            /*
             * =========================================================
             * HASIL AKHIR
             * =========================================================
             *
             * STOP sebelum pencairan.
             */

            $this->command?->info(
                'Dummy workflow berhasil dibuat.'
            );

            $this->command?->info(
                'Loan ID   : ' . $loan->id
            );

            $this->command?->info(
                'Kode      : ' . $loan->code
            );

            $this->command?->info(
                'Status    : ' . $loan->status
            );

            $this->command?->info(
                'Approved  : Rp' .
                    number_format(
                        $loan->approved_amount,
                        0,
                        ',',
                        '.'
                    )
            );

            $this->command?->info(
                'Cicilan   : Rp' .
                    number_format(
                        $loan->monthly_installment,
                        0,
                        ',',
                        '.'
                    )
            );

            $this->command?->info(
                'Stop      : Sebelum pencairan Treasurer.'
            );
        });
    }
}
