<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function index()
    {
        $this->authorizeRole();

        $accounts = Account::query()
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        return view('admin.accounts.index', compact('accounts'));
    }

    public function create()
    {
        $this->authorizeRole();

        return view('admin.accounts.create');
    }

    public function store(Request $request)
    {
        $this->authorizeRole();

        $validated = $request->validate([
            'type' => [
                'required',
                'string',
                'max:20',
            ],
            'name' => [
                'required',
                'string',
                'max:150',
            ],
            'bank_name' => [
                'nullable',
                'string',
                'max:100',
            ],
            'account_number' => [
                'nullable',
                'string',
                'max:50',
            ],
            'account_name' => [
                'nullable',
                'string',
                'max:150',
            ],
            'opening_balance' => [
                'nullable',
                'numeric',
                'min:0',
            ],
        ], [
            'type.required' => 'Jenis rekening wajib diisi.',
            'name.required' => 'Nama rekening wajib diisi.',
            'opening_balance.numeric' => 'Saldo awal harus berupa angka.',
            'opening_balance.min' => 'Saldo awal tidak boleh kurang dari 0.',
        ]);

        Account::create([
            'type' => $validated['type'],
            'name' => $validated['name'],
            'bank_name' => $validated['bank_name'] ?? null,
            'account_number' => $validated['account_number'] ?? null,
            'account_name' => $validated['account_name'] ?? null,
            'opening_balance' => $validated['opening_balance'] ?? 0,
            'opening_balance_initialized_at' => now(),
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.accounts.index')
            ->with('success', 'Rekening berhasil ditambahkan.');
    }

    public function edit(Account $account)
    {
        $this->authorizeRole();

        return view('admin.accounts.edit', compact('account'));
    }

    public function update(Request $request, Account $account)
    {
        $this->authorizeRole();

        $validated = $request->validate([
            'type' => [
                'required',
                'string',
                'max:20',
            ],
            'name' => [
                'required',
                'string',
                'max:150',
            ],
            'bank_name' => [
                'nullable',
                'string',
                'max:100',
            ],
            'account_number' => [
                'nullable',
                'string',
                'max:50',
            ],
            'account_name' => [
                'nullable',
                'string',
                'max:150',
            ],
            'opening_balance' => [
                'nullable',
                'numeric',
                'min:0',
            ],
        ], [
            'type.required' => 'Jenis rekening wajib diisi.',
            'name.required' => 'Nama rekening wajib diisi.',
            'opening_balance.numeric' => 'Saldo awal harus berupa angka.',
            'opening_balance.min' => 'Saldo awal tidak boleh kurang dari 0.',
        ]);

        $hasCashFlows = $account->cashFlows()->exists();

        if ($hasCashFlows) {
            if ($request->has('opening_balance')) {
                $submittedOpeningBalance = number_format(
                    (float) ($validated['opening_balance'] ?? 0),
                    2,
                    '.',
                    ''
                );

                $currentOpeningBalance = number_format(
                    (float) $account->opening_balance,
                    2,
                    '.',
                    ''
                );

                if ($submittedOpeningBalance !== $currentOpeningBalance) {
                    return back()
                        ->withInput()
                        ->withErrors([
                            'opening_balance' => 'Saldo awal tidak dapat diubah karena rekening sudah memiliki transaksi.',
                        ]);
                }
            }

            $account->update([
                'type' => $validated['type'],
                'name' => $validated['name'],
                'bank_name' => $validated['bank_name'] ?? null,
                'account_number' => $validated['account_number'] ?? null,
                'account_name' => $validated['account_name'] ?? null,
            ]);
        } else {
            $account->update([
                'type' => $validated['type'],
                'name' => $validated['name'],
                'bank_name' => $validated['bank_name'] ?? null,
                'account_number' => $validated['account_number'] ?? null,
                'account_name' => $validated['account_name'] ?? null,
                'opening_balance' => $validated['opening_balance'] ?? 0,
            ]);
        }

        return redirect()
            ->route('admin.accounts.index')
            ->with('success', 'Rekening berhasil diperbarui.');
    }

    public function toggleStatus(Account $account)
    {
        $this->authorizeRole();

        if ($account->is_active) {
            $account->update([
                'is_active' => false,
            ]);

            $message = 'Rekening berhasil dinonaktifkan.';
        } else {
            $account->update([
                'is_active' => true,
            ]);

            $message = 'Rekening berhasil diaktifkan.';
        }

        return back()->with('success', $message);
    }

    public function showInitializeOpeningBalance(Account $account)
    {
        $this->authorizeRole();

        if (!$account->needsOpeningBalanceInitialization()) {
            abort(
                422,
                'Rekening ini tidak memerlukan inisialisasi saldo awal.'
            );
        }

        return view(
            'admin.accounts.initialize-opening-balance',
            compact('account')
        );
    }

    public function initializeOpeningBalance(Request $request, Account $account)
    {
        $this->authorizeRole();

        $validated = $request->validate([
            'opening_balance' => [
                'required',
                'numeric',
                'min:0',
            ],
        ], [
            'opening_balance.required' => 'Saldo awal wajib diisi.',
            'opening_balance.numeric' => 'Saldo awal harus berupa angka.',
            'opening_balance.min' => 'Saldo awal tidak boleh kurang dari 0.',
        ]);

        DB::transaction(function () use ($account, $validated) {
            $account = Account::query()
                ->lockForUpdate()
                ->findOrFail($account->id);

            if ($account->opening_balance_initialized_at !== null) {
                abort(
                    422,
                    'Saldo awal rekening sudah diinisialisasi dan tidak dapat diubah.'
                );
            }

            if (!$account->cashFlows()->exists()) {
                abort(
                    422,
                    'Rekening belum memiliki transaksi. Gunakan menu Edit Rekening untuk mengatur saldo awal.'
                );
            }

            $account->update([
                'opening_balance' => $validated['opening_balance'],
                'opening_balance_initialized_at' => now(),
            ]);
        });

        return redirect()
            ->route('admin.accounts.index')
            ->with('success', 'Saldo awal rekening berhasil diinisialisasi.');
    }

    private function authorizeRole(): void
    {
        abort_unless(
            auth()->check()
                && auth()->user()->isActive()
                && auth()->user()->isAdmin(),
            403
        );
    }
}
