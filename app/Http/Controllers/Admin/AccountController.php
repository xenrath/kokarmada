<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;

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
        ], [
            'type.required' => 'Jenis rekening wajib diisi.',
            'name.required' => 'Nama rekening wajib diisi.',
        ]);

        Account::create([
            'type' => $validated['type'],
            'name' => $validated['name'],
            'bank_name' => $validated['bank_name'] ?? null,
            'account_number' => $validated['account_number'] ?? null,
            'account_name' => $validated['account_name'] ?? null,
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
        ], [
            'type.required' => 'Jenis rekening wajib diisi.',
            'name.required' => 'Nama rekening wajib diisi.',
        ]);

        $account->update([
            'type' => $validated['type'],
            'name' => $validated['name'],
            'bank_name' => $validated['bank_name'] ?? null,
            'account_number' => $validated['account_number'] ?? null,
            'account_name' => $validated['account_name'] ?? null,
        ]);

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
