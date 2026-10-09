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
use App\Services\LoanTopUpEligibilityService;
use App\Services\LoanTopUpService;
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

    public function show(
        Loan $loan,
        LoanTopUpEligibilityService $eligibilityService
    ): View {
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

        $topUpEligibility = null;

        if ($loan->status === 'disbursed') {
            $evaluatedEligibility = $eligibilityService->evaluate($user);

            if (
                $evaluatedEligibility['loan']
                && $evaluatedEligibility['loan']->id === $loan->id
            ) {
                $topUpEligibility = $evaluatedEligibility;
            }
        }

        return view(
            'member.loans.show',
            compact('loan', 'topUpEligibility')
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
        // NORMALISASI DATA
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
        // VALIDASI REQUEST
        // =========================================================

        $validator = Validator::make(
            $request->all(),
            [
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

                // Bukti pendapatan lain bersifat OPSIONAL.
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
            ],
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
        // VALIDASI BISNIS
        // =========================================================

        $businessErrors = [];

        // ---------------------------------------------------------
        // NOMINAL
        // ---------------------------------------------------------

        if ($requestedAmount <= 0) {
            $businessErrors['requested_amount'][] =
                'Nominal pengajuan harus lebih dari Rp0.';
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
                $businessErrors['business_type'][] =
                    'Jenis usaha harus dipilih.';
            }

            if (
                $businessType === 'other' &&
                $businessTypeOther === ''
            ) {
                $businessErrors['business_type_other'][] =
                    'Jenis usaha lainnya harus diisi.';
            }
        }

        if (
            $purposeCategory === 'consumer'
            && $businessType !== null
            && $businessType !== ''
        ) {
            $businessErrors['business_type'][] =
                'Jenis usaha tidak diperlukan untuk pinjaman konsumtif.';
        }

        if ($purposeDescription === '') {
            $businessErrors['purpose_description'][] =
                'Tujuan pengajuan kredit harus diisi.';
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
                $businessErrors['term_months'][] =
                    'Jangka waktu angsuran bulanan harus 12, 24, 36, 48, atau 60 bulan.';
            }
        }

        if ($repaymentType === 'lump_sum') {
            if ($termMonths < 1 || $termMonths > 6) {
                $businessErrors['term_months'][] =
                    'Jangka waktu sekaligus harus antara 1 sampai 6 bulan.';
            }
        }

        // ---------------------------------------------------------
        // PEKERJAAN
        // ---------------------------------------------------------

        if ($workUnit === '') {
            $businessErrors['work_unit'][] =
                'Unit kerja harus diisi.';
        }

        if ($position === '') {
            $businessErrors['position'][] =
                'Jabatan harus diisi.';
        }

        if ($employmentDurationYears === null) {
            $businessErrors['employment_duration_years'][] =
                'Lama bekerja harus diisi.';
        }

        // ---------------------------------------------------------
        // PENDAPATAN
        // ---------------------------------------------------------

        if ($otherMonthlyIncome < 0) {
            $businessErrors['other_monthly_income'][] =
                'Pendapatan di luar gaji tidak boleh negatif.';
        }

        if ($netMonthlyIncome < 0) {
            $businessErrors['net_monthly_income'][] =
                'Pendapatan bersih tidak boleh negatif.';
        }

        // Catatan:
        // other_income_proof TIDAK diwajibkan.
        // Jika file ada, akan disimpan.
        // Jika tidak ada, tetap valid.

        // ---------------------------------------------------------
        // AGUNAN
        // ---------------------------------------------------------

        if ($requestedAmount > 25000000) {
            if (blank($request->input('collateral_type'))) {
                $businessErrors['collateral_type'][] =
                    'Jenis agunan harus dipilih.';
            }

            if (blank($request->input('ownership_status'))) {
                $businessErrors['ownership_status'][] =
                    'Status kepemilikan harus dipilih.';
            }

            if (blank($request->input('ownership_proof'))) {
                $businessErrors['ownership_proof'][] =
                    'Bukti kepemilikan harus dipilih.';
            }

            if (! $request->hasFile('collateral_proof_file')) {
                $businessErrors['collateral_proof_file'][] =
                    'File bukti agunan wajib diupload.';
            }
        }

        // ---------------------------------------------------------
        // JIKA BUSINESS VALIDATION GAGAL
        // ---------------------------------------------------------

        if ($businessErrors !== []) {
            return back()
                ->withInput()
                ->withErrors($businessErrors)
                ->with(
                    'error',
                    'Gagal mengajukan pinjaman.'
                );
        }

        // =========================================================
        // SNAPSHOT SUKU BUNGA
        // =========================================================

        $interestRate = (float) (
            Setting::query()
            ->where('key', 'loan_interest_rate')
            ->value('value') ?? 0
        );

        // =========================================================
        // ANGSURAN FINAL
        // =========================================================

        // Angsuran final belum dihitung pada tahap pengajuan.
        // Nominal final ditentukan saat approval Chairman.

        $monthlyInstallment = null;

        // =========================================================
        // FILE TRACKING
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

                $lastSequence = Loan::query()
                    ->lockForUpdate()
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
                // BUKTI PENDAPATAN LAIN
                // =====================================================

                if ($request->hasFile('other_income_proof')) {
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

                    'loan_type' => 'regular',

                    'requested_amount' => $requestedAmount,

                    'purpose_category' => $purposeCategory,

                    'business_type' =>
                    $purposeCategory === 'business'
                        ? $businessType
                        : null,

                    'business_type_other' =>
                    $purposeCategory === 'business'
                        && $businessType === 'other'
                        ? $businessTypeOther
                        : null,

                    'purpose_description' =>
                    $purposeDescription,

                    'term_months' => $termMonths,

                    'repayment_type' => $repaymentType,

                    'monthly_installment' =>
                    $monthlyInstallment,

                    'work_unit' =>
                    $workUnit,

                    'position' =>
                    $position,

                    'employment_duration_years' =>
                    $employmentDurationYears,

                    'other_monthly_income' =>
                    $otherMonthlyIncome,

                    'other_income_proof' =>
                    $otherIncomeProofPath,

                    'net_monthly_income' =>
                    $netMonthlyIncome,

                    'salary_slip' =>
                    $salarySlipPath,

                    'interest_rate' =>
                    $interestRate,

                    'approved_amount' =>
                    null,

                    'status' =>
                    'submitted',
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
                        'loan_id' =>
                        $loan->id,

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

                        'status' =>
                        'pending',
                    ]);
                }

                // =====================================================
                // LOAN PROCESS
                // =====================================================

                LoanProcess::create([
                    'loan_id' =>
                    $loan->id,

                    'user_id' =>
                    $user->id,

                    'role' =>
                    'member',

                    'action' =>
                    'submitted',

                    'notes' =>
                    'Anggota mengajukan pinjaman.',
                ]);

                // =====================================================
                // ACTIVITY
                // =====================================================

                Activity::create([
                    'user_id' =>
                    $user->id,

                    'action' =>
                    'loan_submitted',

                    'subject_type' =>
                    Loan::class,

                    'subject_id' =>
                    $loan->id,

                    'description' =>
                    'Mengajukan pinjaman ' .
                        $loan->code,

                    'ip_address' =>
                    request()->ip(),

                    'user_agent' =>
                    request()->userAgent(),
                ]);

                // =====================================================
                // NOTIFIKASI
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

    public function storeTopUp(
        Request $request,
        Loan $loan,
        LoanTopUpEligibilityService $eligibilityService,
        LoanTopUpService $topUpService
    ): RedirectResponse {
        $user = auth()->user();

        // =========================================================
        // OTORISASI
        // =========================================================

        abort_unless(
            $user->isMember() && $user->isActive(),
            403
        );

        abort_if(
            ! $user->memberProfile,
            422,
            'Data diri Anda belum lengkap.'
        );

        abort_unless(
            $loan->user_id === $user->id,
            404
        );

        // =========================================================
        // NORMALISASI NOMINAL
        // =========================================================

        $topUpAmount = $this->parseMoney(
            $request->input('top_up_amount')
        );

        // =========================================================
        // VALIDASI REQUEST DASAR
        // =========================================================

        $validator = Validator::make(
            $request->all(),
            [
                'top_up_amount' => [
                    'required',
                ],

                'purpose_category' => [
                    'nullable',
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
                    'nullable',
                    'string',
                    'max:5000',
                ],

                'term_months' => [
                    'required',
                    'integer',
                    'in:12,24,36,48,60',
                ],

                'work_unit' => [
                    'nullable',
                    'string',
                    'max:150',
                ],

                'position' => [
                    'nullable',
                    'string',
                    'max:150',
                ],

                'employment_duration_years' => [
                    'nullable',
                    'integer',
                    'min:0',
                    'max:100',
                ],

                'net_monthly_income' => [
                    'nullable',
                ],

                'other_monthly_income' => [
                    'nullable',
                ],

                // Bukti pendapatan lain bersifat OPSIONAL.
                'other_income_proof' => [
                    'nullable',
                    'file',
                    'mimes:pdf,jpg,jpeg,png',
                    'max:2048',
                ],

                'declared_external_monthly_obligations' => [
                    'nullable',
                ],

                'declared_external_obligations_note' => [
                    'nullable',
                    'string',
                    'max:1000',
                ],

                'declared_external_obligations_proof' => [
                    'nullable',
                    'file',
                    'mimes:pdf,jpg,jpeg,png',
                    'max:2048',
                ],

                // Top Up wajib menggunakan slip gaji terbaru.
                'salary_slip' => [
                    'required',
                    'file',
                    'mimes:pdf,jpg,jpeg,png',
                    'max:2048',
                ],

                // =====================================================
                // AGUNAN
                // =====================================================

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
            ],
            [
                'top_up_amount.required' =>
                'Nominal tambahan Top Up harus diisi.',

                'term_months.required' =>
                'Jangka waktu Top Up harus dipilih.',

                'term_months.integer' =>
                'Jangka waktu Top Up tidak valid.',

                'term_months.in' =>
                'Jangka waktu Top Up harus 12, 24, 36, 48, atau 60 bulan.',

                'salary_slip.required' =>
                'Slip gaji terbaru wajib diupload.',

                'salary_slip.mimes' =>
                'Slip gaji harus berupa PDF, JPG, JPEG, atau PNG.',

                'salary_slip.max' =>
                'Ukuran slip gaji maksimal 2 MB.',

                'other_income_proof.mimes' =>
                'Bukti pendapatan harus berupa PDF, JPG, JPEG, atau PNG.',

                'other_income_proof.max' =>
                'Ukuran bukti pendapatan maksimal 2 MB.',

                'declared_external_obligations_proof.mimes' =>
                'Bukti kewajiban eksternal harus berupa PDF, JPG, JPEG, atau PNG.',

                'declared_external_obligations_proof.max' =>
                'Ukuran bukti kewajiban eksternal maksimal 2 MB.',

                'collateral_proof_file.mimes' =>
                'File bukti agunan harus berupa PDF, JPG, JPEG, atau PNG.',

                'collateral_proof_file.max' =>
                'Ukuran file bukti agunan maksimal 2 MB.',
            ]
        );

        if ($validator->fails()) {
            return back()
                ->withInput()
                ->withErrors($validator)
                ->with(
                    'error',
                    'Gagal mengajukan Top Up.'
                );
        }

        // =========================================================
        // CEK KELAYAKAN TOP UP
        // =========================================================

        $eligibility = $eligibilityService->evaluate($user);

        if (
            ! $eligibility['eligible']
            || ! $eligibility['loan']
            || $eligibility['loan']->id !== $loan->id
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'top_up_amount' =>
                    $eligibility['reason']
                        ?? 'Pinjaman belum memenuhi syarat Top Up.',
                ])
                ->with(
                    'error',
                    'Pengajuan Top Up belum dapat diproses.'
                );
        }

        // =========================================================
        // DATA KELAYAKAN
        // =========================================================

        $minimumAdditionalAmount =
            (float) $eligibility['minimum_additional_amount'];

        $maximumLoanAmount =
            (float) $eligibility['maximum_loan_amount'];

        $outstandingPrincipal =
            (float) $eligibility['outstanding_principal'];

        /*
     * Total kontrak baru:
     *
     * outstanding pokok lama
     * +
     * tambahan Top Up
     */
        $requestedAmount =
            $outstandingPrincipal
            + $topUpAmount;

        // =========================================================
        // NORMALISASI DATA KEUANGAN
        // =========================================================

        $otherMonthlyIncome = $this->parseMoney(
            $request->input('other_monthly_income')
        );

        $netMonthlyIncome = $this->parseMoney(
            $request->input('net_monthly_income')
        );

        $externalMonthlyObligations = $this->parseMoney(
            $request->input(
                'declared_external_monthly_obligations'
            )
        );

        // =========================================================
        // VALIDASI BISNIS TOP UP
        // =========================================================

        $businessErrors = [];

        // ---------------------------------------------------------
        // NOMINAL TAMBAHAN
        // ---------------------------------------------------------

        if ($topUpAmount <= 0) {
            $businessErrors['top_up_amount'][] =
                'Nominal tambahan Top Up harus lebih dari Rp0.';
        }

        if (
            $topUpAmount > 0
            && $topUpAmount < $minimumAdditionalAmount
        ) {
            $businessErrors['top_up_amount'][] =
                'Nominal tambahan Top Up belum mencapai minimum yang ditetapkan.';
        }

        // ---------------------------------------------------------
        // BATAS MAKSIMUM TOTAL KONTRAK
        // ---------------------------------------------------------

        if (
            $topUpAmount > 0
            && $requestedAmount > $maximumLoanAmount
        ) {
            $businessErrors['top_up_amount'][] =
                'Total kontrak baru melebihi batas maksimum pinjaman.';
        }

        // ---------------------------------------------------------
        // DATA KEUANGAN
        // ---------------------------------------------------------

        if ($netMonthlyIncome < 0) {
            $businessErrors['net_monthly_income'][] =
                'Pendapatan bersih tidak boleh negatif.';
        }

        if ($otherMonthlyIncome < 0) {
            $businessErrors['other_monthly_income'][] =
                'Pendapatan di luar gaji tidak boleh negatif.';
        }

        if ($externalMonthlyObligations < 0) {
            $businessErrors['declared_external_monthly_obligations'][] =
                'Kewajiban bulanan di luar koperasi tidak boleh negatif.';
        }

        // IMPORTANT:
        // other_income_proof TIDAK diwajibkan.
        // Bukti hanya diproses jika memang diupload.

        // ---------------------------------------------------------
        // TUJUAN PINJAMAN
        // ---------------------------------------------------------

        $purposeCategory = $request->input(
            'purpose_category'
        );

        $businessType = $request->input(
            'business_type'
        );

        $businessTypeOther = trim(
            (string) $request->input(
                'business_type_other'
            )
        );

        $purposeDescription = trim(
            (string) $request->input(
                'purpose_description'
            )
        );

        if (
            $purposeCategory === 'business'
            && ! in_array(
                $businessType,
                ['trade', 'agriculture', 'service', 'other'],
                true
            )
        ) {
            $businessErrors['business_type'][] =
                'Jenis usaha harus dipilih.';
        }

        if (
            $purposeCategory === 'business'
            && $businessType === 'other'
            && $businessTypeOther === ''
        ) {
            $businessErrors['business_type_other'][] =
                'Jenis usaha lainnya harus diisi.';
        }

        if (
            $purposeCategory === 'consumer'
            && $businessType !== null
            && $businessType !== ''
        ) {
            $businessErrors['business_type'][] =
                'Jenis usaha tidak diperlukan untuk pinjaman konsumtif.';
        }

        /*
     * Purpose description boleh kosong pada boundary request
     * karena service dapat menggunakan data pinjaman lama.
     */

        // ---------------------------------------------------------
        // PEKERJAAN
        // ---------------------------------------------------------

        $workUnit = trim(
            (string) $request->input('work_unit')
        );

        $position = trim(
            (string) $request->input('position')
        );

        $employmentDurationYears =
            $request->input(
                'employment_duration_years'
            );

        if (
            $workUnit !== ''
            && mb_strlen($workUnit) > 150
        ) {
            $businessErrors['work_unit'][] =
                'Unit kerja maksimal 150 karakter.';
        }

        if (
            $position !== ''
            && mb_strlen($position) > 150
        ) {
            $businessErrors['position'][] =
                'Jabatan maksimal 150 karakter.';
        }

        if (
            $employmentDurationYears !== null
            && (int) $employmentDurationYears < 0
        ) {
            $businessErrors['employment_duration_years'][] =
                'Lama bekerja tidak boleh negatif.';
        }

        // ---------------------------------------------------------
        // AGUNAN
        // ---------------------------------------------------------

        if ($requestedAmount > 25000000) {
            if (blank($request->input('collateral_type'))) {
                $businessErrors['collateral_type'][] =
                    'Jenis agunan harus dipilih.';
            }

            if (blank($request->input('ownership_status'))) {
                $businessErrors['ownership_status'][] =
                    'Status kepemilikan harus dipilih.';
            }

            if (blank($request->input('ownership_proof'))) {
                $businessErrors['ownership_proof'][] =
                    'Bukti kepemilikan harus dipilih.';
            }

            if (! $request->hasFile('collateral_proof_file')) {
                $businessErrors['collateral_proof_file'][] =
                    'File bukti agunan wajib diupload.';
            }
        }

        // =========================================================
        // JIKA BUSINESS VALIDATION GAGAL
        // =========================================================

        if ($businessErrors !== []) {
            return back()
                ->withInput()
                ->withErrors($businessErrors)
                ->with(
                    'error',
                    'Gagal mengajukan Top Up.'
                );
        }

        // =========================================================
        // CEK SUKU BUNGA
        // =========================================================

        $interestRateValue = Setting::query()
            ->where('key', 'loan_interest_rate')
            ->value('value');

        if (
            $interestRateValue === null
            || trim((string) $interestRateValue) === ''
            || ! is_numeric($interestRateValue)
            || (float) $interestRateValue < 0
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'top_up_amount' =>
                    'Pengaturan suku bunga pinjaman belum dikonfigurasi dengan benar.',
                ])
                ->with(
                    'error',
                    'Pengajuan Top Up belum dapat diproses.'
                );
        }

        // =========================================================
        // STORAGE
        // =========================================================

        $storedPaths = [];

        $storageToken =
            now()->format('YmdHis')
            . '-'
            . bin2hex(random_bytes(8));

        try {
            $basePath =
                'private/top-up/'
                . $user->id
                . '/'
                . $storageToken;

            // =====================================================
            // SLIP GAJI
            // =====================================================

            $salarySlipPath = $request
                ->file('salary_slip')
                ->store(
                    $basePath . '/salary-slip',
                    'local'
                );

            $storedPaths[] =
                $salarySlipPath;

            // =====================================================
            // BUKTI PENDAPATAN LAIN
            // =====================================================

            $otherIncomeProofPath = null;

            if ($request->hasFile('other_income_proof')) {
                $otherIncomeProofPath = $request
                    ->file('other_income_proof')
                    ->store(
                        $basePath . '/other-income',
                        'local'
                    );

                $storedPaths[] =
                    $otherIncomeProofPath;
            }

            // =====================================================
            // BUKTI KEWAJIBAN EKSTERNAL
            // =====================================================

            $externalObligationsProofPath = null;

            if (
                $request->hasFile(
                    'declared_external_obligations_proof'
                )
            ) {
                $externalObligationsProofPath =
                    $request
                    ->file(
                        'declared_external_obligations_proof'
                    )
                    ->store(
                        $basePath . '/external-obligations',
                        'local'
                    );

                $storedPaths[] =
                    $externalObligationsProofPath;
            }

            // =====================================================
            // BUKTI AGUNAN
            // =====================================================

            $collateralProofPath = null;

            if ($requestedAmount > 25000000) {
                $collateralProofPath = $request
                    ->file('collateral_proof_file')
                    ->store(
                        $basePath . '/collateral',
                        'local'
                    );

                $storedPaths[] =
                    $collateralProofPath;
            }

            // =====================================================
            // SUBMIT TOP UP
            // =====================================================

            $newLoan = $topUpService->submit(
                $user,
                [
                    'top_up_amount' =>
                    $topUpAmount,

                    'term_months' =>
                    (int) $request->input(
                        'term_months'
                    ),

                    'purpose_category' =>
                    $request->input(
                        'purpose_category'
                    ),

                    'business_type' =>
                    $request->input(
                        'business_type'
                    ),

                    'business_type_other' =>
                    $request->input(
                        'business_type_other'
                    ),

                    'purpose_description' =>
                    $request->input(
                        'purpose_description'
                    ),

                    'work_unit' =>
                    $request->input(
                        'work_unit'
                    ),

                    'position' =>
                    $request->input(
                        'position'
                    ),

                    'employment_duration_years' =>
                    $request->input(
                        'employment_duration_years'
                    ),

                    /*
                 * Hanya override data lama jika field
                 * memang dikirim oleh anggota.
                 */

                    'net_monthly_income' =>
                    $request->has(
                        'net_monthly_income'
                    )
                        ? $netMonthlyIncome
                        : null,

                    'other_monthly_income' =>
                    $request->has(
                        'other_monthly_income'
                    )
                        ? $otherMonthlyIncome
                        : null,

                    'declared_external_monthly_obligations' =>
                    $request->has(
                        'declared_external_monthly_obligations'
                    )
                        ? $externalMonthlyObligations
                        : null,

                    'declared_external_obligations_note' =>
                    $request->input(
                        'declared_external_obligations_note'
                    ),

                    'declared_external_obligations_proof' =>
                    $externalObligationsProofPath,

                    'other_income_proof' =>
                    $otherIncomeProofPath,

                    'salary_slip' =>
                    $salarySlipPath,

                    // =================================================
                    // AGUNAN
                    // =================================================

                    'collateral' =>
                    $requestedAmount > 25000000
                        ? [
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
                        ]
                        : null,
                ]
            );

            // =========================================================
            // BERHASIL
            // =========================================================

            return redirect()
                ->route('member.loans.index')
                ->with(
                    'success',
                    'Pengajuan Top Up berhasil dibuat dengan kode '
                        . $newLoan->code
                );
        } catch (Throwable $e) {
            // =========================================================
            // CLEANUP FILE
            // =========================================================

            foreach ($storedPaths as $path) {
                if ($path) {
                    Storage::disk('local')->delete(
                        $path
                    );
                }
            }

            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Pengajuan Top Up gagal diproses. Silakan coba lagi.'
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
