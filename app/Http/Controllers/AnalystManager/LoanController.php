<?php

namespace App\Http\Controllers\AnalystManager;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Loan;
use App\Models\LoanAnalysis;
use App\Models\LoanDocument;
use App\Models\LoanProcess;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class LoanController extends Controller
{
    public function index()
    {
        $this->authorizeRole();

        $loans = Loan::with('user.memberProfile', 'analysis')
            ->whereIn('status', [
                'submitted',
                'under_analysis',
                'waiting_treasurer_review',
                'waiting_chairman_approval',
                'approved',
            ])
            ->latest('submitted_at')
            ->get();

        return view('analyst-manager.loans.index', compact('loans'));
    }

    public function show(Loan $loan)
    {
        $this->authorizeRole();

        $loan->load([
            'user.memberProfile',
            'analysis.analyst',
            'processes.user',
            'collaterals',
        ]);

        return view('analyst-manager.loans.show', compact('loan'));
    }

    public function startAnalysis(Loan $loan)
    {
        $this->authorizeRole();

        if ($loan->status !== 'submitted') {
            return back()->with(
                'error',
                'Pengajuan ini tidak dapat memulai analisis pada tahap sekarang.'
            );
        }

        DB::transaction(function () use ($loan) {
            $user = auth()->user();

            $loan = Loan::query()
                ->whereKey($loan->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($loan->status !== 'submitted') {
                abort(
                    422,
                    'Pengajuan ini tidak dapat memulai analisis pada tahap sekarang.'
                );
            }

            $loan->update([
                'status' => 'under_analysis',
            ]);

            LoanProcess::create([
                'loan_id' => $loan->id,
                'user_id' => $user->id,
                'role' => 'analyst_manager',
                'action' => 'analysis_started',
                'notes' => 'Analyst Manager mulai melakukan analisis.',
            ]);

            Activity::create([
                'user_id' => $user->id,
                'action' => 'loan_analysis_started',
                'subject_type' => Loan::class,
                'subject_id' => $loan->id,
                'description' => 'Analisis pinjaman ' . $loan->code . ' telah dimulai.',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        return redirect()
            ->route('analyst-manager.loans.show', $loan)
            ->with(
                'success',
                'Analisis pinjaman telah dimulai.'
            );
    }

    public function analyze(Request $request, Loan $loan)
    {
        $this->authorizeRole();

        if ($loan->status !== 'under_analysis') {
            return back()->with('error', 'Pengajuan ini tidak dapat dianalisis pada tahap sekarang.');
        }

        $validated = $request->validate([
            'recommended_amount' => [
                'required',
                'numeric',
                'min:1',
                'lte:' . $loan->requested_amount,
            ],
            'notes' => [
                'required',
                'string',
                'max:5000',
            ],
        ], [
            'recommended_amount.required' => 'Nominal rekomendasi wajib diisi.',
            'recommended_amount.numeric' => 'Nominal rekomendasi harus berupa angka.',
            'recommended_amount.min' => 'Nominal rekomendasi harus lebih dari 0.',
            'recommended_amount.lte' => 'Nominal rekomendasi tidak boleh melebihi nominal pengajuan.',
            'notes.required' => 'Catatan analisis wajib diisi.',
        ]);

        DB::transaction(function () use ($loan, $validated) {
            $user = auth()->user();

            $loan = Loan::query()
                ->whereKey($loan->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($loan->status !== 'under_analysis') {
                abort(
                    422,
                    'Pengajuan ini belum berada dalam tahap analisis.'
                );
            }

            LoanAnalysis::updateOrCreate(
                ['loan_id' => $loan->id],
                [
                    'analyst_id' => $user->id,
                    'recommended_amount' => $validated['recommended_amount'],
                    'notes' => $validated['notes'],
                    'reviewed_at' => now(),
                ]
            );

            LoanProcess::create([
                'loan_id' => $loan->id,
                'user_id' => $user->id,
                'role' => 'analyst_manager',
                'action' => 'analysis_completed',
                'notes' => $validated['notes'],
            ]);

            $loan->update([
                'status' => 'waiting_treasurer_review',
            ]);

            Activity::create([
                'user_id' => $user->id,
                'action' => 'loan_analysis_completed',
                'subject_type' => Loan::class,
                'subject_id' => $loan->id,
                'description' => 'Analisis pinjaman ' . $loan->code . ' telah diselesaikan.',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            $treasurer = \App\Models\User::query()
                ->where('role', 'treasurer')
                ->where('status', 'active')
                ->first();

            if ($treasurer) {
                Notification::create([
                    'user_id' => $treasurer->id,
                    'type' => 'loan_review',
                    'title' => 'Pengajuan Siap Direview',
                    'message' => 'Pengajuan ' . $loan->code . ' telah selesai dianalisis dan menunggu review Treasurer.',
                    'reference_type' => Loan::class,
                    'reference_id' => $loan->id,
                ]);
            }
        });

        return redirect()
            ->route('analyst-manager.loans.show', $loan)
            ->with('success', 'Analisis pinjaman berhasil disimpan.');
    }

    public function documents(Loan $loan)
    {
        $this->authorizeRole();

        abort_unless(
            $loan->status === 'approved',
            404
        );

        $loan->load([
            'user.memberProfile',
            'analysis.analyst',
            'treasurerReview.treasurer',
            'documents.uploader',
            'documents.verifier',
        ]);

        return view(
            'analyst-manager.loans.documents',
            compact('loan')
        );
    }

    public function generateDocuments(Loan $loan)
    {
        $this->authorizeRole();

        abort_unless(
            $loan->status === 'approved',
            404
        );

        $loan->load([
            'user.memberProfile',
            'analysis.analyst',
            'treasurerReview.treasurer',
            'collaterals',
        ]);

        $secretary = User::query()
            ->where('role', 'secretary')
            ->where('status', 'active')
            ->first();

        if (! $secretary) {
            return back()->with(
                'error',
                'Akun Secretary aktif belum tersedia.'
            );
        }

        $chairman = User::query()
            ->where('role', 'chairman')
            ->where('status', 'active')
            ->first();

        if (! $chairman) {
            return back()->with(
                'error',
                'Akun Chairman aktif belum tersedia.'
            );
        }

        $documents = $loan->documents()
            ->whereIn('document_type', [
                'approval_letter',
                'credit_agreement',
            ])
            ->get()
            ->keyBy('document_type');

        /*
     * Jangan membuat ulang dokumen apabila
     * salah satu dokumen sudah memiliki scan
     * bertanda tangan.
     */
        foreach ($documents as $document) {
            if ($document->file_path) {
                return back()->with(
                    'error',
                    'Dokumen bertanda tangan sudah tersedia. Dokumen tidak dapat dibuat ulang.'
                );
            }
        }

        $generatedPaths = [];

        try {
            /*
         * =========================================================
         * SURAT PERSETUJUAN KREDIT
         * =========================================================
         */

            $approvalPdf = Pdf::loadView(
                'analyst-manager.loans.documents.approval-letter',
                [
                    'loan' => $loan,
                    'secretary' => $secretary,
                    'chairman' => $chairman,
                ]
            );

            $approvalPdf->setPaper('a4');

            $approvalPath =
                'loan-documents/' .
                $loan->id .
                '/generated/surat-persetujuan-kredit.pdf';

            Storage::disk('local')->put(
                $approvalPath,
                $approvalPdf->output()
            );

            $generatedPaths[] = $approvalPath;

            /*
         * =========================================================
         * SURAT PERJANJIAN KREDIT
         * =========================================================
         */

            $creditAgreementPdf = Pdf::loadView(
                'analyst-manager.loans.documents.credit-agreement',
                [
                    'loan' => $loan,
                    'chairman' => $chairman,
                ]
            );

            $creditAgreementPdf->setPaper('a4');

            $creditAgreementPath =
                'loan-documents/' .
                $loan->id .
                '/generated/surat-perjanjian-kredit.pdf';

            Storage::disk('local')->put(
                $creditAgreementPath,
                $creditAgreementPdf->output()
            );

            $generatedPaths[] = $creditAgreementPath;

            /*
         * =========================================================
         * SIMPAN DATA DOKUMEN
         * =========================================================
         */

            DB::transaction(function () use (
                $loan,
                $approvalPath,
                $creditAgreementPath
            ) {
                LoanDocument::updateOrCreate(
                    [
                        'loan_id' => $loan->id,
                        'document_type' => 'approval_letter',
                    ],
                    [
                        'document_stage' => 'generated',
                        'generated_file_path' => $approvalPath,
                        'generated_at' => now(),
                        'file_path' => null,
                        'uploaded_by' => null,
                        'uploaded_at' => null,
                        'verified_by' => null,
                        'verified_at' => null,
                        'status' => 'pending',
                    ]
                );

                LoanDocument::updateOrCreate(
                    [
                        'loan_id' => $loan->id,
                        'document_type' => 'credit_agreement',
                    ],
                    [
                        'document_stage' => 'generated',
                        'generated_file_path' => $creditAgreementPath,
                        'generated_at' => now(),
                        'file_path' => null,
                        'uploaded_by' => null,
                        'uploaded_at' => null,
                        'verified_by' => null,
                        'verified_at' => null,
                        'status' => 'pending',
                    ]
                );

                LoanProcess::create([
                    'loan_id' => $loan->id,
                    'user_id' => auth()->id(),
                    'role' => 'analyst_manager',
                    'action' => 'documents_generated',
                    'notes' => 'Surat Persetujuan Kredit dan Surat Perjanjian Kredit berhasil dibuat.',
                ]);

                Activity::create([
                    'user_id' => auth()->id(),
                    'action' => 'loan_documents_generated',
                    'subject_type' => Loan::class,
                    'subject_id' => $loan->id,
                    'description' =>
                    'Dokumen kredit untuk ' .
                        $loan->code .
                        ' berhasil dibuat.',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            });

            return redirect()
                ->route(
                    'analyst-manager.loans.documents',
                    $loan
                )
                ->with(
                    'success',
                    'Surat Persetujuan Kredit dan Surat Perjanjian Kredit berhasil dibuat.'
                );
        } catch (\Throwable $e) {
            foreach ($generatedPaths as $path) {
                Storage::disk('local')->delete($path);
            }

            report($e);

            return back()->with(
                'error',
                'Dokumen kredit gagal dibuat. Silakan coba lagi.'
            );
        }
    }

    public function viewGeneratedDocument(
        Loan $loan,
        LoanDocument $document
    ) {
        $this->authorizeRole();

        abort_unless(
            $document->loan_id === $loan->id,
            404
        );

        abort_unless(
            $document->generated_file_path
                && Storage::disk('local')->exists(
                    $document->generated_file_path
                ),
            404
        );

        return response()->file(
            Storage::disk('local')->path(
                $document->generated_file_path
            )
        );
    }

    public function downloadGeneratedDocument(
        Loan $loan,
        LoanDocument $document
    ) {
        $this->authorizeRole();

        abort_unless(
            $document->loan_id === $loan->id,
            404
        );

        abort_unless(
            $document->generated_file_path
                && Storage::disk('local')->exists(
                    $document->generated_file_path
                ),
            404
        );

        $filename = 'dokumen-kredit.pdf';

        if ($document->document_type === 'approval_letter') {
            $filename = 'surat-persetujuan-kredit.pdf';
        } elseif ($document->document_type === 'credit_agreement') {
            $filename = 'surat-perjanjian-kredit.pdf';
        }

        return Storage::disk('local')->download(
            $document->generated_file_path,
            $filename
        );
    }

    public function uploadDocuments(Request $request, Loan $loan)
    {
        $this->authorizeRole();

        abort_unless(
            $loan->status === 'approved',
            404
        );

        $validated = $request->validate([
            'documents' => [
                'required',
                'array',
                'min:1',
            ],
            'documents.*' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:10240',
            ],
        ], [
            'documents.required' => 'Dokumen wajib dipilih.',
            'documents.*.required' => 'File dokumen wajib diunggah.',
            'documents.*.mimes' => 'Format dokumen harus PDF, JPG, JPEG, atau PNG.',
            'documents.*.max' => 'Ukuran setiap file maksimal 10 MB.',
        ]);

        $documents = $loan->documents()
            ->whereIn('id', array_keys($validated['documents']))
            ->get()
            ->keyBy('id');

        DB::transaction(function () use (
            $validated,
            $documents,
            $loan
        ) {
            foreach ($validated['documents'] as $documentId => $file) {
                $document = $documents->get($documentId);

                if (! $document) {
                    continue;
                }

                if ($document->file_path) {
                    Storage::disk('local')->delete(
                        $document->file_path
                    );
                }

                $path = $file->store(
                    'loan-documents/' . $loan->id . '/signed',
                    'local'
                );

                $document->update([
                    'document_stage' => 'signed',
                    'file_path' => $path,
                    'uploaded_by' => auth()->id(),
                    'uploaded_at' => now(),
                    'verified_by' => null,
                    'verified_at' => null,
                    'status' => 'pending',
                ]);
            }

            LoanProcess::create([
                'loan_id' => $loan->id,
                'user_id' => auth()->id(),
                'role' => 'analyst_manager',
                'action' => 'documents_uploaded',
                'notes' => 'Dokumen bertanda tangan berhasil diunggah.',
            ]);

            Activity::create([
                'user_id' => auth()->id(),
                'action' => 'loan_documents_uploaded',
                'subject_type' => Loan::class,
                'subject_id' => $loan->id,
                'description' =>
                'Dokumen bertanda tangan untuk ' .
                    $loan->code .
                    ' telah diunggah.',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        $documentsComplete = $loan->documents()
            ->whereIn('document_type', [
                'approval_letter',
                'credit_agreement',
            ])
            ->whereNotNull('file_path')
            ->count() === 2;

        $secretary = User::query()
            ->where('role', 'secretary')
            ->where('status', 'active')
            ->first();

        if ($documentsComplete && $secretary) {
            Notification::create([
                'user_id' => $secretary->id,
                'type' => 'loan_documents',
                'title' => 'Dokumen Kredit Siap Divalidasi',
                'message' =>
                'Seluruh dokumen kredit ' .
                    $loan->code .
                    ' telah diunggah dan menunggu validasi.',
                'reference_type' => Loan::class,
                'reference_id' => $loan->id,
            ]);

            LoanProcess::create([
                'loan_id' => $loan->id,
                'user_id' => auth()->id(),
                'role' => 'analyst_manager',
                'action' => 'documents_uploaded_complete',
                'notes' =>
                'Seluruh dokumen kredit telah lengkap dan menunggu validasi Sekretaris.',
            ]);
        }

        return redirect()
            ->route(
                'analyst-manager.loans.documents',
                $loan
            )
            ->with(
                'success',
                $documentsComplete
                    ? 'Seluruh dokumen berhasil diunggah dan menunggu validasi Sekretaris.'
                    : 'Dokumen berhasil diunggah.'
            );
    }

    public function generateApprovalLetter(Loan $loan)
    {
        $this->authorizeRole();

        abort_unless(
            $loan->status === 'approved',
            404
        );

        $loan->load([
            'user.memberProfile',
            'collaterals',
        ]);

        $secretary = User::query()
            ->where('role', 'secretary')
            ->where('status', 'active')
            ->first();

        if (! $secretary) {
            return back()->with(
                'error',
                'Akun Secretary aktif belum tersedia.'
            );
        }

        $chairman = User::query()
            ->where('role', 'chairman')
            ->where('status', 'active')
            ->first();

        if (! $chairman) {
            return back()->with(
                'error',
                'Akun Chairman aktif belum tersedia.'
            );
        }

        $pdf = Pdf::loadView(
            'analyst-manager.loans.documents.approval-letter',
            [
                'loan' => $loan,
                'secretary' => $secretary,
                'chairman' => $chairman,
            ]
        );

        $pdf->setPaper('a4');

        $path = 'loan-documents/' . $loan->id
            . '/generated/surat-persetujuan-kredit.pdf';

        Storage::disk('local')->put(
            $path,
            $pdf->output()
        );

        $document = LoanDocument::updateOrCreate(
            [
                'loan_id' => $loan->id,
                'document_type' => 'approval_letter',
            ],
            [
                'document_stage' => 'generated',
                'generated_file_path' => $path,
                'generated_at' => now(),
                'file_path' => null,
                'uploaded_by' => null,
                'uploaded_at' => null,
                'verified_by' => null,
                'verified_at' => null,
                'status' => 'pending',
            ]
        );

        LoanProcess::create([
            'loan_id' => $loan->id,
            'user_id' => auth()->id(),
            'role' => 'analyst_manager',
            'action' => 'approval_letter_generated',
            'notes' => 'Surat Persetujuan Kredit berhasil dibuat.',
        ]);

        Activity::create([
            'user_id' => auth()->id(),
            'action' => 'approval_letter_generated',
            'subject_type' => Loan::class,
            'subject_id' => $loan->id,
            'description' => 'Surat Persetujuan Kredit untuk ' . $loan->code . ' berhasil dibuat.',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()
            ->route('analyst-manager.loans.documents', $loan)
            ->with(
                'success',
                'Surat Persetujuan Kredit berhasil dibuat.'
            );
    }

    public function generateCreditAgreement(Loan $loan)
    {
        $this->authorizeRole();

        abort_unless(
            $loan->status === 'approved',
            404
        );

        $loan->load([
            'user.memberProfile',
            'analysis.analyst',
            'treasurerReview.treasurer',
            'collaterals',
        ]);

        $chairman = \App\Models\User::query()
            ->where('role', 'chairman')
            ->where('status', 'active')
            ->first();

        abort_unless(
            $chairman,
            422,
            'Data Ketua koperasi belum tersedia.'
        );

        $document = $loan->documents()
            ->where('document_type', 'credit_agreement')
            ->first();

        /*
     * Jangan membuat ulang dokumen apabila
     * scan bertanda tangan sudah ada.
     */
        if ($document && $document->file_path) {
            return back()->with(
                'error',
                'Surat Perjanjian Kredit sudah memiliki dokumen bertanda tangan.'
            );
        }

        $pdf = Pdf::loadView(
            'analyst-manager.loans.documents.credit-agreement',
            [
                'loan' => $loan,
                'chairman' => $chairman,
            ]
        );

        $path = 'loan-documents/' .
            $loan->id .
            '/generated/surat-perjanjian-kredit.pdf';

        Storage::disk('local')->put(
            $path,
            $pdf->output()
        );

        DB::transaction(function () use ($loan, $path) {
            LoanDocument::updateOrCreate(
                [
                    'loan_id' => $loan->id,
                    'document_type' => 'credit_agreement',
                ],
                [
                    'document_stage' => 'generated',
                    'generated_file_path' => $path,
                    'generated_at' => now(),
                    'status' => 'pending',
                ]
            );

            LoanProcess::create([
                'loan_id' => $loan->id,
                'user_id' => auth()->id(),
                'role' => 'analyst_manager',
                'action' => 'credit_agreement_generated',
                'notes' => 'Surat Perjanjian Kredit berhasil dibuat.',
            ]);

            Activity::create([
                'user_id' => auth()->id(),
                'action' => 'loan_credit_agreement_generated',
                'subject_type' => Loan::class,
                'subject_id' => $loan->id,
                'description' => 'Surat Perjanjian Kredit untuk ' .
                    $loan->code .
                    ' telah dibuat.',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        return redirect()
            ->route(
                'analyst-manager.loans.documents',
                $loan
            )
            ->with(
                'success',
                'Surat Perjanjian Kredit berhasil dibuat.'
            );
    }

    private function authorizeRole(): void
    {
        abort_unless(
            auth()->check()
                && auth()->user()->isActive()
                && auth()->user()->isAnalystManager(),
            403
        );
    }
}
