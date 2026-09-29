<?php

namespace App\Http\Controllers\Chairman;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Loan;
use App\Models\LoanProcess;
use App\Models\Notification;
use App\Models\User;
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
            'treasurerReview.treasurer',
        ])
            ->where('status', 'waiting_chairman_approval')
            ->latest('submitted_at')
            ->get();

        return view('chairman.loans.index', compact('loans'));
    }

    public function show(Loan $loan)
    {
        $this->authorizeRole();

        abort_unless(
            $loan->status === 'waiting_chairman_approval',
            404
        );

        $loan->load([
            'user.memberProfile',
            'analysis.analyst',
            'treasurerReview.treasurer',
            'processes.user',
            'collaterals',
        ]);

        return view('chairman.loans.show', compact('loan'));
    }

    public function approve(Request $request, Loan $loan)
    {
        $this->authorizeRole();

        if ($loan->status !== 'waiting_chairman_approval') {
            return back()->with(
                'error',
                'Pengajuan ini tidak dapat diproses pada tahap sekarang.'
            );
        }

        $validated = $request->validate([
            'approved_amount' => [
                'required',
                'numeric',
                'min:1',
                'lte:' . $loan->requested_amount,
            ],
            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ], [
            'approved_amount.required' => 'Nominal persetujuan wajib diisi.',
            'approved_amount.numeric' => 'Nominal persetujuan harus berupa angka.',
            'approved_amount.min' => 'Nominal persetujuan harus lebih dari 0.',
            'approved_amount.lte' => 'Nominal persetujuan tidak boleh melebihi nominal pengajuan.',
        ]);

        DB::transaction(function () use ($loan, $validated) {
            $user = auth()->user();

            $loan = Loan::query()
                ->whereKey($loan->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($loan->status !== 'waiting_chairman_approval') {
                abort(
                    422,
                    'Pengajuan ini tidak dapat diproses pada tahap sekarang.'
                );
            }

            $approvedAmount = (float) $validated['approved_amount'];

            /*
             * Untuk pembayaran bulanan, cicilan harus dihitung ulang
             * berdasarkan nominal final Chairman.
             */
            $monthlyInstallment = null;

            if ($loan->repayment_type === 'monthly') {
                $annualInterest = $approvedAmount
                    * ((float) $loan->interest_rate / 100);

                $totalAmount = $approvedAmount + (
                    $annualInterest * ((int) $loan->term_months / 12)
                );

                $monthlyInstallment = ceil(
                    $totalAmount / (int) $loan->term_months
                );
            }

            $loan->update([
                'approved_amount' => $approvedAmount,
                'monthly_installment' => $monthlyInstallment,
                'status' => 'approved',
            ]);

            LoanProcess::create([
                'loan_id' => $loan->id,
                'user_id' => $user->id,
                'role' => 'chairman',
                'action' => 'loan_approved',
                'notes' => $validated['notes'] ?? null,
            ]);

            Activity::create([
                'user_id' => $user->id,
                'action' => 'loan_approved',
                'subject_type' => Loan::class,
                'subject_id' => $loan->id,
                'description' => sprintf(
                    'Pinjaman %s disetujui Chairman sebesar Rp%s.',
                    $loan->code,
                    number_format($approvedAmount, 0, ',', '.')
                ),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            /*
             * Notifikasi kepada Analyst Manager.
             */
            $analyst = $loan->analysis?->analyst;

            if ($analyst) {
                Notification::create([
                    'user_id' => $analyst->id,
                    'type' => 'loan_approved',
                    'title' => 'Pinjaman Disetujui',
                    'message' => 'Pinjaman ' . $loan->code . ' telah disetujui Chairman.',
                    'reference_type' => Loan::class,
                    'reference_id' => $loan->id,
                ]);
            }

            /*
             * Notifikasi kepada anggota.
             */
            Notification::create([
                'user_id' => $loan->user_id,
                'type' => 'loan_approved',
                'title' => 'Pinjaman Disetujui',
                'message' => 'Pengajuan pinjaman ' . $loan->code . ' telah disetujui sebesar Rp'
                    . number_format($approvedAmount, 0, ',', '.') . '.',
                'reference_type' => Loan::class,
                'reference_id' => $loan->id,
            ]);
        });

        return redirect()
            ->route('chairman.loans.index')
            ->with('success', 'Pinjaman berhasil disetujui.');
    }

    private function authorizeRole(): void
    {
        abort_unless(
            auth()->check()
                && auth()->user()->isActive()
                && auth()->user()->isChairman(),
            403
        );
    }
}
