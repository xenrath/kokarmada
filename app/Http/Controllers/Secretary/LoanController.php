<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Loan;
use App\Models\LoanDocument;
use App\Models\LoanProcess;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LoanController extends Controller
{
    public function index()
    {
        $this->authorizeRole();

        $loans = Loan::query()
            ->with([
                'user.memberProfile',
                'documents',
            ])
            ->whereHas('documents', function ($query) {
                $query
                    ->whereIn('document_type', [
                        'approval_letter',
                        'credit_agreement',
                    ])
                    ->whereNotNull('file_path')
                    ->where('status', 'pending');
            })
            ->latest('updated_at')
            ->get();

        return view(
            'secretary.loans.index',
            compact('loans')
        );
    }

    public function show(Loan $loan)
    {
        $this->authorizeRole();

        $loan->load([
            'user.memberProfile',
            'analysis.analyst',
            'treasurerReview.treasurer',
            'documents.uploader',
            'documents.verifier',
        ]);

        return view(
            'secretary.loans.show',
            compact('loan')
        );
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

    public function viewSignedDocument(
        Loan $loan,
        LoanDocument $document
    ) {
        $this->authorizeRole();

        abort_unless(
            $document->loan_id === $loan->id,
            404
        );

        abort_unless(
            $document->file_path
                && Storage::disk('local')->exists(
                    $document->file_path
                ),
            404
        );

        return response()->file(
            Storage::disk('local')->path(
                $document->file_path
            )
        );
    }

    public function verifyDocument(
        Loan $loan,
        LoanDocument $document
    ) {
        $this->authorizeRole();

        abort_unless(
            $document->loan_id === $loan->id,
            404
        );

        abort_unless(
            $document->status === 'pending'
                && $document->file_path,
            404
        );

        DB::transaction(function () use ($loan, $document) {
            $document->update([
                'status' => 'verified',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);

            LoanProcess::create([
                'loan_id' => $loan->id,
                'user_id' => auth()->id(),
                'role' => 'secretary',
                'action' => 'document_verified',
                'notes' =>
                'Dokumen ' .
                    $this->documentLabel($document->document_type) .
                    ' telah divalidasi.',
            ]);

            Activity::create([
                'user_id' => auth()->id(),
                'action' => 'loan_document_verified',
                'subject_type' => Loan::class,
                'subject_id' => $loan->id,
                'description' =>
                $this->documentLabel($document->document_type) .
                    ' untuk ' .
                    $loan->code .
                    ' telah divalidasi oleh Sekretaris.',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        $documentsComplete = $loan->documents()
            ->whereIn('document_type', [
                'approval_letter',
                'credit_agreement',
            ])
            ->where('status', 'verified')
            ->count() === 2;

        if ($documentsComplete) {
            $treasurer = User::query()
                ->where('role', 'treasurer')
                ->where('status', 'active')
                ->first();

            LoanProcess::create([
                'loan_id' => $loan->id,
                'user_id' => auth()->id(),
                'role' => 'secretary',
                'action' => 'documents_verified_complete',
                'notes' =>
                'Seluruh dokumen kredit telah divalidasi dan siap dilanjutkan ke tahap pencairan.',
            ]);

            if ($treasurer) {
                Notification::create([
                    'user_id' => $treasurer->id,
                    'type' => 'loan_disbursement',
                    'title' => 'Pinjaman Siap Dicairkan',
                    'message' =>
                    'Dokumen kredit ' .
                        $loan->code .
                        ' telah lengkap dan divalidasi oleh Sekretaris.',
                    'reference_type' => Loan::class,
                    'reference_id' => $loan->id,
                ]);
            }
        }

        return back()->with(
            'success',
            $documentsComplete
                ? 'Dokumen berhasil divalidasi. Seluruh dokumen telah lengkap dan pinjaman siap dilanjutkan ke tahap pencairan.'
                : 'Dokumen berhasil divalidasi.'
        );
    }

    public function verifyCollateral(Request $request, Loan $loan, LoanCollateral $collateral)
    {
        $this->authorizeRole();

        abort_unless($collateral->loan_id === $loan->id, 404);

        abort_unless(
            $collateral->status === 'pending',
            422,
            'Agunan ini tidak berada dalam status menunggu verifikasi.'
        );

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($loan, $collateral, $validated) {
            $collateral->update([
                'status' => 'verified',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
                'verification_notes' => $validated['notes'] ?? null,
            ]);

            LoanProcess::create([
                'loan_id' => $loan->id,
                'user_id' => auth()->id(),
                'role' => 'secretary',
                'action' => 'collateral_verified',
                'notes' => 'Agunan ' . $collateral->id . ' telah diverifikasi.',
            ]);

            Activity::create([
                'user_id' => auth()->id(),
                'action' => 'loan_collateral_verified',
                'subject_type' => Loan::class,
                'subject_id' => $loan->id,
                'description' => 'Agunan pinjaman ' . $loan->code . ' telah diverifikasi oleh Sekretaris.',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        return back()->with('success', 'Agunan berhasil diverifikasi.');
    }

    public function rejectCollateral(Request $request, Loan $loan, LoanCollateral $collateral)
    {
        $this->authorizeRole();

        abort_unless($collateral->loan_id === $loan->id, 404);

        abort_unless(
            $collateral->status === 'pending',
            422,
            'Agunan ini tidak berada dalam status menunggu verifikasi.'
        );

        $validated = $request->validate([
            'notes' => ['required', 'string', 'max:2000'],
        ], [
            'notes.required' => 'Catatan penolakan agunan wajib diisi.',
        ]);

        DB::transaction(function () use ($loan, $collateral, $validated) {
            $collateral->update([
                'status' => 'rejected',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
                'verification_notes' => $validated['notes'],
            ]);

            LoanProcess::create([
                'loan_id' => $loan->id,
                'user_id' => auth()->id(),
                'role' => 'secretary',
                'action' => 'collateral_rejected',
                'notes' => 'Agunan ' . $collateral->id . ': ' . $validated['notes'],
            ]);

            Activity::create([
                'user_id' => auth()->id(),
                'action' => 'loan_collateral_rejected',
                'subject_type' => Loan::class,
                'subject_id' => $loan->id,
                'description' => 'Agunan pinjaman ' . $loan->code . ' perlu diperbaiki.',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        return back()->with('success', 'Agunan ditandai perlu diperbaiki.');
    }

    public function rejectDocument(
        Request $request,
        Loan $loan,
        LoanDocument $document
    ) {
        $this->authorizeRole();

        abort_unless(
            $document->loan_id === $loan->id,
            404
        );

        abort_unless(
            $document->status === 'pending'
                && $document->file_path,
            404
        );

        $validated = $request->validate([
            'notes' => [
                'required',
                'string',
                'max:2000',
            ],
        ], [
            'notes.required' =>
            'Catatan perbaikan wajib diisi.',
        ]);

        DB::transaction(function () use (
            $loan,
            $document,
            $validated
        ) {
            $document->update([
                'status' => 'rejected',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);

            LoanProcess::create([
                'loan_id' => $loan->id,
                'user_id' => auth()->id(),
                'role' => 'secretary',
                'action' => 'document_rejected',
                'notes' =>
                $this->documentLabel($document->document_type) .
                    ': ' .
                    $validated['notes'],
            ]);

            Activity::create([
                'user_id' => auth()->id(),
                'action' => 'loan_document_rejected',
                'subject_type' => Loan::class,
                'subject_id' => $loan->id,
                'description' =>
                $this->documentLabel($document->document_type) .
                    ' untuk ' .
                    $loan->code .
                    ' perlu diperbaiki.',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            Notification::create([
                'user_id' => $loan->user_id,
                'type' => 'loan_documents',
                'title' => 'Dokumen Pinjaman Perlu Diperbaiki',
                'message' =>
                'Dokumen ' .
                    $this->documentLabel($document->document_type) .
                    ' perlu diperbaiki. ' .
                    $validated['notes'],
                'reference_type' => Loan::class,
                'reference_id' => $loan->id,
            ]);
        });

        return back()->with(
            'success',
            'Dokumen ditandai perlu diperbaiki.'
        );
    }

    private function documentLabel(string $documentType): string
    {
        if ($documentType === 'approval_letter') {
            return 'Surat Persetujuan Kredit';
        } elseif ($documentType === 'credit_agreement') {
            return 'Surat Perjanjian Kredit';
        }

        return 'Dokumen Kredit';
    }

    private function authorizeRole(): void
    {
        abort_unless(
            auth()->check()
                && auth()->user()->isActive()
                && auth()->user()->isSecretary(),
            403
        );
    }
}
