<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Loan;
use App\Models\LoanCollateral;
use App\Models\LoanProcess;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Throwable;

class LoanController extends Controller
{
    public function index(): View
    {
        $user = auth()->user()->load('memberProfile');

        $loans = $user->loans()
            ->latest('submitted_at')
            ->get();

        return view('member.loans.index', compact(
            'user',
            'loans'
        ));
    }

    public function show(Loan $loan): View
    {
        $user = auth()->user();

        abort_unless(
            $loan->user_id === $user->id,
            404
        );

        $loan->load([
            'user.memberProfile',
            'analysis.analyst',
            'treasurerReview.treasurer',
            'documents.verifier',
            'disbursement.account',
            'disbursement.treasurer',
            'installments' => function ($query) {
                $query->orderBy('installment_number');
            },
        ]);

        return view(
            'member.loans.show',
            compact('loan')
        );
    }

    public function create(): View
    {
        $interestRate = (float) (
            Setting::where('key', 'loan_interest_rate')->value('value') ?? 0
        );

        return view('member.loans.create', compact(
            'interestRate'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        abort_unless(
            $user->isMember() && $user->isActive(),
            403
        );

        // =========================================================
        // DATA DIRI
        // =========================================================

        abort_if(
            ! $user->memberProfile,
            422,
            'Data diri Anda belum lengkap.'
        );

        // =========================================================
        // NORMALISASI ANGKA
        // =========================================================

        $requestedAmount = $this->parseMoney(
            $request->input('requested_amount')
        );

        $otherMonthlyIncome = $this->parseMoney(
            $request->input('other_monthly_income')
        );

        $netMonthlyIncome = $this->parseMoney(
            $request->input('net_monthly_income')
        );

        $termMonths = (int) $request->input('term_months');

        $purposeCategory = $request->input('purpose_category');
        $businessType = $request->input('business_type');
        $businessTypeOther = trim(
            (string) $request->input('business_type_other')
        );

        $purposeDescription = trim(
            (string) $request->input('purpose_description')
        );

        $repaymentType = $request->input('repayment_type');

        $workUnit = trim(
            (string) $request->input('work_unit')
        );

        $position = trim(
            (string) $request->input('position')
        );

        $employmentDurationYears = $request->input(
            'employment_duration_years'
        );

        // =========================================================
        // VALIDASI
        // =========================================================

        $rules = [
            'requested_amount' => [
                'required',
            ],

            'purpose_category' => [
                'required',
                'in:business,consumer',
            ],

            'business_type' => [
                'nullable',
                'in:trade,agriculture,service,other',
            ],

            'business_type_other' => [
                'nullable',
                'string',
                'max:150',
            ],

            'purpose_description' => [
                'required',
                'string',
                'max:5000',
            ],

            'term_months' => [
                'required',
                'integer',
            ],

            'repayment_type' => [
                'required',
                'in:monthly,lump_sum',
            ],

            'work_unit' => [
                'required',
                'string',
                'max:150',
            ],

            'position' => [
                'required',
                'string',
                'max:150',
            ],

            'employment_duration_years' => [
                'required',
                'integer',
                'min:0',
                'max:100',
            ],

            'other_monthly_income' => [
                'nullable',
            ],

            'other_income_proof' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048',
            ],

            'net_monthly_income' => [
                'nullable',
            ],

            'salary_slip' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048',
            ],

            'collateral_type' => [
                'nullable',
                'string',
                'max:50',
            ],

            'collateral_description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'ownership_status' => [
                'nullable',
                'in:self,spouse,parent,other',
            ],

            'ownership_proof' => [
                'nullable',
                'in:shm,hgb,hgu,hak_pakai,bpkb',
            ],

            'collateral_proof_file' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048',
            ],
        ];

        $validator = Validator::make(
            $request->all(),
            $rules,
            [
                'requested_amount.required' =>
                'Nominal pengajuan harus diisi.',

                'purpose_category.required' =>
                'Kategori tujuan pinjaman harus dipilih.',

                'purpose_category.in' =>
                'Kategori tujuan pinjaman tidak valid.',

                'business_type.in' =>
                'Jenis usaha tidak valid.',

                'purpose_description.required' =>
                'Tujuan pengajuan kredit harus diisi.',

                'term_months.required' =>
                'Jangka waktu harus dipilih.',

                'term_months.integer' =>
                'Jangka waktu tidak valid.',

                'repayment_type.required' =>
                'Jenis angsuran harus dipilih.',

                'repayment_type.in' =>
                'Jenis angsuran tidak valid.',

                'work_unit.required' =>
                'Unit kerja harus diisi.',

                'position.required' =>
                'Jabatan harus diisi.',

                'employment_duration_years.required' =>
                'Lama bekerja harus diisi.',

                'salary_slip.required' =>
                'Slip gaji harus diupload.',

                'salary_slip.mimes' =>
                'Slip gaji harus berupa PDF, JPG, JPEG, atau PNG.',

                'salary_slip.max' =>
                'Ukuran slip gaji maksimal 2 MB.',

                'collateral_proof_file.mimes' =>
                'File bukti agunan harus berupa PDF, JPG, JPEG, atau PNG.',

                'collateral_proof_file.max' =>
                'Ukuran file bukti agunan maksimal 2 MB.',

                'other_income_proof.mimes' =>
                'Bukti pendapatan harus berupa PDF, JPG, JPEG, atau PNG.',

                'other_income_proof.max' =>
                'Ukuran bukti pendapatan maksimal 2 MB.',
            ]
        );

        $validator->after(function ($validator) use (
            $request,
            $requestedAmount,
            $purposeCategory,
            $businessType,
            $businessTypeOther,
            $purposeDescription,
            $termMonths,
            $repaymentType,
            $workUnit,
            $position,
            $employmentDurationYears,
            $otherMonthlyIncome,
            $netMonthlyIncome
        ) {
            // ---------------------------------------------------------
            // NOMINAL
            // ---------------------------------------------------------

            if ($requestedAmount <= 0) {
                $validator->errors()->add(
                    'requested_amount',
                    'Nominal pengajuan harus lebih dari Rp0.'
                );
            }

            // ---------------------------------------------------------
            // TUJUAN
            // ---------------------------------------------------------

            if ($purposeCategory === 'business') {
                if (! in_array(
                    $businessType,
                    ['trade', 'agriculture', 'service', 'other'],
                    true
                )) {
                    $validator->errors()->add(
                        'business_type',
                        'Jenis usaha harus dipilih.'
                    );
                }

                if (
                    $businessType === 'other' &&
                    $businessTypeOther === ''
                ) {
                    $validator->errors()->add(
                        'business_type_other',
                        'Jenis usaha lainnya harus diisi.'
                    );
                }
            }

            if ($purposeCategory === 'consumer') {
                if ($businessType !== null && $businessType !== '') {
                    $validator->errors()->add(
                        'business_type',
                        'Jenis usaha tidak diperlukan untuk pinjaman konsumtif.'
                    );
                }
            }

            if ($purposeDescription === '') {
                $validator->errors()->add(
                    'purpose_description',
                    'Tujuan pengajuan kredit harus diisi.'
                );
            }

            // ---------------------------------------------------------
            // JANGKA WAKTU
            // ---------------------------------------------------------

            if ($repaymentType === 'monthly') {
                if (! in_array(
                    $termMonths,
                    [12, 24, 36, 48, 60],
                    true
                )) {
                    $validator->errors()->add(
                        'term_months',
                        'Jangka waktu angsuran bulanan harus 12, 24, 36, 48, atau 60 bulan.'
                    );
                }
            }

            if ($repaymentType === 'lump_sum') {
                if ($termMonths < 1 || $termMonths > 6) {
                    $validator->errors()->add(
                        'term_months',
                        'Jangka waktu sekaligus harus antara 1 sampai 6 bulan.'
                    );
                }
            }

            // ---------------------------------------------------------
            // PEKERJAAN
            // ---------------------------------------------------------

            if ($workUnit === '') {
                $validator->errors()->add(
                    'work_unit',
                    'Unit kerja harus diisi.'
                );
            }

            if ($position === '') {
                $validator->errors()->add(
                    'position',
                    'Jabatan harus diisi.'
                );
            }

            if ($employmentDurationYears === null) {
                $validator->errors()->add(
                    'employment_duration_years',
                    'Lama bekerja harus diisi.'
                );
            }

            // ---------------------------------------------------------
            // PENDAPATAN
            // ---------------------------------------------------------

            if ($otherMonthlyIncome < 0) {
                $validator->errors()->add(
                    'other_monthly_income',
                    'Pendapatan di luar gaji tidak boleh negatif.'
                );
            }

            if ($netMonthlyIncome < 0) {
                $validator->errors()->add(
                    'net_monthly_income',
                    'Pendapatan bersih tidak boleh negatif.'
                );
            }

            if (
                $otherMonthlyIncome > 0 &&
                ! $request->hasFile('other_income_proof')
            ) {
                $validator->errors()->add(
                    'other_income_proof',
                    'Bukti pendapatan di luar gaji wajib diupload.'
                );
            }

            // ---------------------------------------------------------
            // AGUNAN
            // ---------------------------------------------------------

            if ($requestedAmount > 25000000) {
                if (blank($request->input('collateral_type'))) {
                    $validator->errors()->add(
                        'collateral_type',
                        'Jenis agunan harus dipilih.'
                    );
                }

                if (blank($request->input('ownership_status'))) {
                    $validator->errors()->add(
                        'ownership_status',
                        'Status kepemilikan harus dipilih.'
                    );
                }

                if (blank($request->input('ownership_proof'))) {
                    $validator->errors()->add(
                        'ownership_proof',
                        'Bukti kepemilikan harus dipilih.'
                    );
                }

                if (! $request->hasFile('collateral_proof_file')) {
                    $validator->errors()->add(
                        'collateral_proof_file',
                        'File bukti agunan wajib diupload.'
                    );
                }
            }
        });

        if ($validator->fails()) {
            return back()
                ->withInput()
                ->withErrors($validator)
                ->with(
                    'error',
                    'Gagal mengajukan pinjaman.'
                );
        }

        // =========================================================
        // SNAPSHOT SUKU BUNGA
        // =========================================================

        $interestRate = (float) (
            Setting::where('key', 'loan_interest_rate')
            ->value('value') ?? 0
        );

        // =========================================================
        // ANGSURAN FINAL
        // =========================================================
        //
        // Angsuran final belum dihitung pada tahap pengajuan.
        // Nominal final baru ditentukan oleh Chairman.
        // Perhitungan monthly_installment dilakukan saat approval
        // berdasarkan approved_amount dan interest_rate snapshot.

        $monthlyInstallment = null;

        // =========================================================
        // FILE PATH TRACKING
        // =========================================================

        $salarySlipPath = null;
        $otherIncomeProofPath = null;
        $collateralProofPath = null;

        try {
            $loan = DB::transaction(function () use (
                $request,
                $user,
                $requestedAmount,
                $purposeCategory,
                $businessType,
                $businessTypeOther,
                $purposeDescription,
                $termMonths,
                $repaymentType,
                $monthlyInstallment,
                $workUnit,
                $position,
                $employmentDurationYears,
                $otherMonthlyIncome,
                $netMonthlyIncome,
                $interestRate,
                &$salarySlipPath,
                &$otherIncomeProofPath,
                &$collateralProofPath
            ) {
                // =====================================================
                // NOMOR URUT
                // =====================================================

                $lastSequence = Loan::lockForUpdate()
                    ->max('sequence_number');

                $sequenceNumber = $lastSequence
                    ? $lastSequence + 1
                    : 100;

                $code = $this->generateCode(
                    $sequenceNumber
                );

                // =====================================================
                // SLIP GAJI
                // =====================================================

                /** @var UploadedFile $salarySlip */
                $salarySlip = $request->file('salary_slip');

                $salarySlipFilename = $this->generateFilename(
                    'salary-slip',
                    $salarySlip
                );

                $salarySlipPath =
                    'private/loans/' .
                    $sequenceNumber .
                    '/salary-slip/' .
                    $salarySlipFilename;

                $salarySlip->storeAs(
                    'private/loans/' .
                        $sequenceNumber .
                        '/salary-slip',
                    $salarySlipFilename,
                    'local'
                );

                // =====================================================
                // BUKTI PENDAPATAN DI LUAR GAJI
                // =====================================================

                if (
                    $otherMonthlyIncome > 0 &&
                    $request->hasFile('other_income_proof')
                ) {
                    /** @var UploadedFile $otherIncomeProof */
                    $otherIncomeProof = $request->file(
                        'other_income_proof'
                    );

                    $otherIncomeFilename = $this->generateFilename(
                        'other-income',
                        $otherIncomeProof
                    );

                    $otherIncomeProofPath =
                        'private/loans/' .
                        $sequenceNumber .
                        '/other-income/' .
                        $otherIncomeFilename;

                    $otherIncomeProof->storeAs(
                        'private/loans/' .
                            $sequenceNumber .
                            '/other-income',
                        $otherIncomeFilename,
                        'local'
                    );
                }

                // =====================================================
                // LOAN
                // =====================================================

                $loan = Loan::create([
                    'code' => $code,
                    'sequence_number' => $sequenceNumber,
                    'user_id' => $user->id,
                    'submitted_at' => now(),

                    'requested_amount' => $requestedAmount,

                    'purpose_category' => $purposeCategory,
                    'business_type' => $purposeCategory === 'business'
                        ? $businessType
                        : null,

                    'business_type_other' =>
                    $purposeCategory === 'business' &&
                        $businessType === 'other'
                        ? $businessTypeOther
                        : null,

                    'purpose_description' =>
                    $purposeDescription,

                    'term_months' => $termMonths,
                    'repayment_type' => $repaymentType,
                    'monthly_installment' =>
                    $monthlyInstallment,

                    'work_unit' => $workUnit,
                    'position' => $position,
                    'employment_duration_years' =>
                    $employmentDurationYears,

                    'other_monthly_income' =>
                    $otherMonthlyIncome,

                    'other_income_proof' =>
                    $otherIncomeProofPath,

                    'net_monthly_income' =>
                    $netMonthlyIncome,

                    'salary_slip' => $salarySlipPath,

                    // Snapshot bunga saat pengajuan.
                    'interest_rate' => $interestRate,

                    // Belum disetujui Chairman.
                    'approved_amount' => null,

                    // Status awal.
                    'status' => 'submitted',
                ]);

                // =====================================================
                // AGUNAN
                // =====================================================

                if ($requestedAmount > 25000000) {
                    /** @var UploadedFile $collateralProofFile */
                    $collateralProofFile = $request->file(
                        'collateral_proof_file'
                    );

                    $collateralFilename = $this->generateFilename(
                        'collateral',
                        $collateralProofFile
                    );

                    $collateralProofPath =
                        'private/loans/' .
                        $sequenceNumber .
                        '/collateral/' .
                        $collateralFilename;

                    $collateralProofFile->storeAs(
                        'private/loans/' .
                            $sequenceNumber .
                            '/collateral',
                        $collateralFilename,
                        'local'
                    );

                    LoanCollateral::create([
                        'loan_id' => $loan->id,

                        'type' =>
                        $request->input(
                            'collateral_type'
                        ),

                        'description' =>
                        $request->input(
                            'collateral_description'
                        ),

                        'ownership_status' =>
                        $request->input(
                            'ownership_status'
                        ),

                        'ownership_proof' =>
                        $request->input(
                            'ownership_proof'
                        ),

                        'proof_file' =>
                        $collateralProofPath,
                    ]);
                }

                // =====================================================
                // LOAN PROCESS / AUDIT TRAIL
                // =====================================================

                LoanProcess::create([
                    'loan_id' => $loan->id,
                    'user_id' => $user->id,
                    'role' => 'member',
                    'action' => 'submitted',
                    'notes' =>
                    'Anggota mengajukan pinjaman.',
                ]);

                // =====================================================
                // ACTIVITY
                // =====================================================

                Activity::create([
                    'user_id' => $user->id,
                    'action' => 'loan_submitted',
                    'subject_type' => Loan::class,
                    'subject_id' => $loan->id,
                    'description' =>
                    'Mengajukan pinjaman ' .
                        $loan->code,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);

                // =====================================================
                // NOTIFIKASI ANALYST MANAGER
                // =====================================================

                $analystManager = User::query()
                    ->where('role', 'analyst_manager')
                    ->where('status', 'active')
                    ->first();

                if ($analystManager) {
                    Notification::create([
                        'user_id' =>
                        $analystManager->id,

                        'type' =>
                        'loan_submitted',

                        'title' =>
                        'Pengajuan Pinjaman Baru',

                        'message' =>
                        'Pengajuan ' .
                            $loan->code .
                            ' menunggu analisis.',

                        'reference_type' =>
                        Loan::class,

                        'reference_id' =>
                        $loan->id,
                    ]);
                }

                return $loan;
            });

            return redirect()
                ->route('member.loans.index')
                ->with(
                    'success',
                    'Pengajuan pinjaman berhasil dibuat dengan kode ' .
                        $loan->code
                );
        } catch (Throwable $e) {
            // =========================================================
            // CLEANUP FILE JIKA TRANSAKSI GAGAL
            // =========================================================

            foreach (
                [
                    $salarySlipPath,
                    $otherIncomeProofPath,
                    $collateralProofPath,
                ] as $path
            ) {
                if ($path) {
                    Storage::disk('local')->delete($path);
                }
            }

            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Pengajuan pinjaman gagal diproses. Silakan coba lagi.'
                );
        }
    }

    private function parseMoney(mixed $value): float
    {
        if ($value === null || $value === '') {
            return 0;
        }

        $value = str_replace(
            '.',
            '',
            (string) $value
        );

        $value = str_replace(
            ',',
            '.',
            $value
        );

        return max(0, (float) $value);
    }

    private function generateFilename(
        string $prefix,
        UploadedFile $file
    ): string {
        return $prefix . '-' .
            now()->format('YmdHis') . '-' .
            bin2hex(random_bytes(8)) . '.' .
            $file->extension();
    }

    private function generateCode(
        int $sequenceNumber
    ): string {
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

        return 'KOPKARMADA/' .
            $sequenceNumber . '/' .
            $month . '/' .
            now()->year;
    }
}
