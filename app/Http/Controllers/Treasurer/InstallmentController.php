<?php

namespace App\Http\Controllers\Treasurer;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Activity;
use App\Models\CashFlow;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\LoanProcess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InstallmentController extends Controller
{
    public function index(): View
    {
        $this->authorizeRole();

        $loans = Loan::query()
            ->whereIn('status', ['disbursed', 'paid_off'])
            ->with([
                'user.memberProfile',
                'installments' => function ($query) {
                    $query->orderBy('installment_number');
                },
            ])
            ->latest('id')
            ->paginate(10);

        return view('treasurer.installments.index', compact('loans'));
    }

    public function show(Loan $loan): View
    {
        $this->authorizeRole();

        abort_unless(
            in_array($loan->status, ['disbursed', 'paid_off'], true),
            404
        );

        $loan->load([
            'user.memberProfile',
            'disbursement.account',
            'disbursement.treasurer',
            'installments' => function ($query) {
                $query->orderBy('installment_number');
            },
        ]);

        $accounts = Account::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'treasurer.installments.show',
            compact('loan', 'accounts')
        );
    }

    public function pay(
        Request $request,
        Installment $installment
    ): RedirectResponse {
        $this->authorizeRole();

        $validated = $request->validate([
            'account_id' => [
                'required',
                'integer',
                'exists:accounts,id',
            ],
        ]);

        DB::transaction(function () use ($validated, $installment) {

            $installment->load('loan');

            $loan = $installment->loan;

            if (!$loan) {
                abort(422, 'Pinjaman untuk angsuran tidak ditemukan.');
            }

            if ($loan->status !== 'disbursed') {
                abort(
                    422,
                    'Pinjaman tidak berada pada status disbursed. Status saat ini: ' . $loan->status
                );
            }

            $installment->refresh();

            if ($installment->status === 'paid') {
                abort(422, 'Angsuran ini sudah dibayar.');
            }

            $account = Account::query()
                ->where('id', $validated['account_id'])
                ->where('is_active', true)
                ->firstOrFail();

            $paidAt = now();

            $installment->update([
                'status' => 'paid',
                'paid_at' => $paidAt,
            ]);

            CashFlow::create([
                'account_id' => $account->id,
                'loan_id' => $loan->id,
                'installment_id' => $installment->id,
                'type' => 'in',
                'category' => 'installment_payment',
                'amount' => $installment->total_amount,
                'description' => sprintf(
                    'Pembayaran angsuran ke-%d - %s',
                    $installment->installment_number,
                    $loan->code
                ),
                'occurred_at' => $paidAt,
            ]);

            LoanProcess::create([
                'loan_id' => $loan->id,
                'user_id' => auth()->id(),
                'role' => 'treasurer',
                'action' => 'installment_paid',
                'notes' => sprintf(
                    'Angsuran ke-%d dibayar sebesar Rp %s melalui akun %s.',
                    $installment->installment_number,
                    number_format(
                        $installment->total_amount,
                        0,
                        ',',
                        '.'
                    ),
                    $account->name
                ),
                'created_at' => $paidAt,
            ]);

            Activity::create([
                'user_id' => auth()->id(),
                'action' => 'installment_paid',
                'description' => sprintf(
                    'Mencatat pembayaran angsuran ke-%d untuk pinjaman %s.',
                    $installment->installment_number,
                    $loan->code
                ),
            ]);

            $hasInstallments = $loan->installments()->exists();

            $hasUnpaidInstallments = $loan->installments()
                ->where('status', '!=', 'paid')
                ->exists();

            if ($hasInstallments && ! $hasUnpaidInstallments) {
                $loan->update([
                    'status' => 'paid_off',
                ]);

                LoanProcess::create([
                    'loan_id' => $loan->id,
                    'user_id' => auth()->id(),
                    'role' => 'treasurer',
                    'action' => 'loan_paid_off',
                    'notes' => 'Seluruh angsuran pinjaman telah dibayar dan pinjaman dinyatakan lunas.',
                    'created_at' => $paidAt,
                ]);

                Activity::create([
                    'user_id' => auth()->id(),
                    'action' => 'loan_paid_off',
                    'description' => sprintf(
                        'Pinjaman %s telah lunas setelah seluruh angsuran dibayar.',
                        $loan->code
                    ),
                ]);
            }
        });

        return redirect()
            ->route('treasurer.installments.show', $installment->loan_id)
            ->with(
                'success',
                'Pembayaran angsuran berhasil dicatat.'
            );
    }

    private function authorizeRole(): void
    {
        abort_unless(auth()->check(), 403);

        abort_unless(
            auth()->user()->isTreasurer(),
            403
        );
    }
}
