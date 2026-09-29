<?php

namespace App\Http\Controllers\Treasurer;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Activity;
use App\Models\Loan;
use App\Models\LoanProcess;
use App\Models\LoanTreasurerReview;
use App\Models\Notification;
use App\Models\User;
use App\Models\CashFlow;
use App\Models\LoanDisbursement;
use App\Services\LoanInstallmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function index()
    {
        $this->authorizeRole();

        $loans = Loan::with([
            'user.memberProfile',
            'analysis.analyst',
            'treasurerReview',
        ])
            ->where('status', 'waiting_treasurer_review')
            ->latest('submitted_at')
            ->get();

        return view('treasurer.loans.index', compact('loans'));
    }

    public function show(Loan $loan)
    {
        $this->authorizeRole();

        abort_unless(
            $loan->status === 'waiting_treasurer_review',
            404
        );

        $loan->load([
            'user.memberProfile',
            'analysis.analyst',
            'treasurerReview.treasurer',
            'processes.user',
            'collaterals',
        ]);

        return view('treasurer.loans.show', compact('loan'));
    }

    public function review(Request $request, Loan $loan)
    {
        $this->authorizeRole();

        if ($loan->status !== 'waiting_treasurer_review') {
            return back()->with(
                'error',
                'Pengajuan ini tidak dapat direview pada tahap sekarang.'
            );
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
            'notes.required' => 'Catatan review wajib diisi.',
        ]);

        DB::transaction(function () use ($loan, $validated) {
            $user = auth()->user();

            $loan = Loan::query()
                ->whereKey($loan->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($loan->status !== 'waiting_treasurer_review') {
                abort(
                    422,
                    'Pengajuan ini tidak dapat direview pada tahap sekarang.'
                );
            }

            LoanTreasurerReview::updateOrCreate(
                [
                    'loan_id' => $loan->id,
                ],
                [
                    'treasurer_id' => $user->id,
                    'recommended_amount' => $validated['recommended_amount'],
                    'notes' => $validated['notes'],
                    'reviewed_at' => now(),
                ]
            );

            LoanProcess::create([
                'loan_id' => $loan->id,
                'user_id' => $user->id,
                'role' => 'treasurer',
                'action' => 'review_completed',
                'notes' => $validated['notes'],
            ]);

            $loan->update([
                'status' => 'waiting_chairman_approval',
            ]);

            Activity::create([
                'user_id' => $user->id,
                'action' => 'loan_treasurer_review_completed',
                'subject_type' => Loan::class,
                'subject_id' => $loan->id,
                'description' => 'Review Treasurer untuk pinjaman ' . $loan->code . ' telah diselesaikan.',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            $chairman = User::query()
                ->where('role', 'chairman')
                ->where('status', 'active')
                ->first();

            if ($chairman) {
                Notification::create([
                    'user_id' => $chairman->id,
                    'type' => 'loan_approval',
                    'title' => 'Pengajuan Siap Disetujui',
                    'message' => 'Pengajuan ' . $loan->code . ' telah direview Treasurer dan menunggu persetujuan Chairman.',
                    'reference_type' => Loan::class,
                    'reference_id' => $loan->id,
                ]);
            }
        });

        return redirect()
            ->route('treasurer.loans.index')
            ->with('success', 'Review Treasurer berhasil disimpan dan pengajuan diteruskan ke Chairman.');
    }

    public function disbursements()
    {
        $this->authorizeRole();

        $loans = Loan::with([
            'user.memberProfile',
            'analysis.analyst',
            'treasurerReview.treasurer',
            'documents',
        ])
            ->where('status', 'approved')
            ->whereHas('documents', function ($query) {
                $query->where('document_type', 'approval_letter')
                    ->where('status', 'verified');
            })
            ->whereHas('documents', function ($query) {
                $query->where('document_type', 'credit_agreement')
                    ->where('status', 'verified');
            })
            ->latest('updated_at')
            ->get();

        return view(
            'treasurer.disbursements.index',
            compact('loans')
        );
    }

    public function showDisbursement(Loan $loan)
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
            'processes.user',
        ]);

        $requiredDocumentsVerified =
            $loan->documents
            ->whereIn('document_type', [
                'approval_letter',
                'credit_agreement',
            ])
            ->where('status', 'verified')
            ->count() === 2;

        abort_unless($requiredDocumentsVerified, 404);

        $accounts = Account::query()
            ->where('is_active', true)
            ->whereNotNull('opening_balance_initialized_at')
            ->withSum([
                'cashFlows as total_in' => function ($query) {
                    $query->where('type', 'in');
                },
            ], 'amount')
            ->withSum([
                'cashFlows as total_out' => function ($query) {
                    $query->where('type', 'out');
                },
            ], 'amount')
            ->orderBy('name')
            ->get();

        $disbursementAmount = (float) $loan->approved_amount;

        foreach ($accounts as $account) {
            $account->available_balance =
                (float) $account->opening_balance
                + (float) ($account->total_in ?? 0)
                - (float) ($account->total_out ?? 0);

            $account->can_disburse =
                $account->available_balance >= $disbursementAmount;
        }

        return view(
            'treasurer.disbursements.show',
            compact('loan', 'accounts', 'disbursementAmount')
        );
    }

    public function disburse(
        Request $request,
        Loan $loan,
        LoanInstallmentService $installmentService
    ) {
        $this->authorizeRole();

        $validated = $request->validate([
            'account_id' => [
                'required',
                'integer',
                'exists:accounts,id',
            ],
            'disbursed_at' => [
                'required',
                'date',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ], [
            'account_id.required' => 'Rekening sumber dana wajib dipilih.',
            'account_id.exists' => 'Rekening sumber dana tidak ditemukan.',
            'disbursed_at.required' => 'Tanggal pencairan wajib diisi.',
            'disbursed_at.date' => 'Tanggal pencairan tidak valid.',
        ]);

        DB::transaction(function () use (
            $loan,
            $validated,
            $installmentService
        ) {
            $loan = Loan::query()
                ->whereKey($loan->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($loan->status !== 'approved') {
                abort(
                    422,
                    'Pinjaman ini tidak dapat dicairkan pada tahap sekarang.'
                );
            }

            $requiredDocumentsVerified =
                $loan->documents()
                ->whereIn('document_type', [
                    'approval_letter',
                    'credit_agreement',
                ])
                ->where('status', 'verified')
                ->count() === 2;

            if (! $requiredDocumentsVerified) {
                abort(
                    422,
                    'Dokumen pinjaman belum seluruhnya terverifikasi.'
                );
            }

            if (
                LoanDisbursement::where('loan_id', $loan->id)->exists()
            ) {
                abort(
                    422,
                    'Pinjaman ini sudah pernah dicairkan.'
                );
            }

            $account = \App\Models\Account::query()
                ->where('id', $validated['account_id'])
                ->where('is_active', true)
                ->first();

            if (! $account) {
                abort(
                    422,
                    'Rekening sumber dana tidak aktif atau tidak ditemukan.'
                );
            }

            $amount = $loan->approved_amount;

            LoanDisbursement::create([
                'loan_id' => $loan->id,
                'treasurer_id' => auth()->id(),
                'account_id' => $account->id,
                'amount' => $amount,
                'disbursed_at' => $validated['disbursed_at'],
                'notes' => $validated['notes'] ?? null,
            ]);

            CashFlow::create([
                'account_id' => $account->id,
                'loan_id' => $loan->id,
                'type' => 'out',
                'category' => 'loan_disbursement',
                'amount' => $amount,
                'description' => 'Pencairan pinjaman ' . $loan->code,
                'occurred_at' => $validated['disbursed_at'],
            ]);

            $loan->update([
                'status' => 'disbursed',
            ]);

            /*
         * Generate jadwal angsuran otomatis
         * setelah pencairan berhasil.
         */
            $installmentService->generate($loan);

            LoanProcess::create([
                'loan_id' => $loan->id,
                'user_id' => auth()->id(),
                'role' => 'treasurer',
                'action' => 'loan_disbursed',
                'notes' => 'Pinjaman dicairkan sebesar Rp ' .
                    number_format($amount, 0, ',', '.') .
                    ' melalui ' . $account->name . '.',
            ]);

            Activity::create([
                'user_id' => auth()->id(),
                'action' => 'loan_disbursed',
                'subject_type' => Loan::class,
                'subject_id' => $loan->id,
                'description' => 'Pencairan pinjaman ' . $loan->code .
                    ' sebesar Rp ' .
                    number_format($amount, 0, ',', '.') . '.',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        return redirect()
            ->route('treasurer.disbursements.index')
            ->with(
                'success',
                'Pinjaman ' . $loan->code . ' berhasil dicairkan.'
            );
    }

    public function disbursementHistory()
    {
        $this->authorizeRole();

        $loans = Loan::with([
            'user.memberProfile',
            'disbursement.account',
            'disbursement.treasurer',
        ])
            ->where('status', 'disbursed')
            ->whereHas('disbursement')
            ->latest('updated_at')
            ->get();

        return view(
            'treasurer.disbursements.history',
            compact('loans')
        );
    }

    private function authorizeRole(): void
    {
        abort_unless(
            auth()->check()
                && auth()->user()->isActive()
                && auth()->user()->isTreasurer(),
            403
        );
    }
}
