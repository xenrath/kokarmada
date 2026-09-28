<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeRole();

        $keyword = $request->input('keyword');
        $role = $request->input('role');
        $status = $request->input('status');

        $users = User::query()
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('phone', 'like', "%{$keyword}%");
                });
            })
            ->when($role, function ($query) use ($role) {
                $query->where('role', $role);
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('status')
            ->orderBy('name')
            ->get();

        return view('admin.users.index', compact(
            'users',
            'keyword',
            'role',
            'status'
        ));
    }

    public function create()
    {
        $this->authorizeRole();

        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $this->authorizeRole();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                'unique:users,phone',
            ],
            'gender' => [
                'required',
                'in:L,P',
            ],
            'role' => [
                'required',
                'in:member,analyst_manager,treasurer,chairman,secretary,admin,supervisor',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'phone.required' => 'Nomor HP wajib diisi.',
            'phone.unique' => 'Nomor HP sudah digunakan.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'role.required' => 'Role wajib dipilih.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);

        User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'gender' => $validated['gender'],
            'role' => $validated['role'],
            'status' => 'active',
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $this->authorizeRole();

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeRole();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                'unique:users,phone,' . $user->id,
            ],
            'gender' => [
                'required',
                'in:L,P',
            ],
            'role' => [
                'required',
                'in:member,analyst_manager,treasurer,chairman,secretary,admin,supervisor',
            ],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'phone.required' => 'Nomor HP wajib diisi.',
            'phone.unique' => 'Nomor HP sudah digunakan.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'role.required' => 'Role wajib dipilih.',
        ]);

        $user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'gender' => $validated['gender'],
            'role' => $validated['role'],
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function toggleStatus(User $user)
    {
        $this->authorizeRole();

        if ($user->id === auth()->id()) {
            return back()->with(
                'error',
                'Anda tidak dapat menonaktifkan akun yang sedang digunakan.'
            );
        }

        if ($user->status === 'active') {
            $user->update([
                'status' => 'inactive',
            ]);

            $message = 'Pengguna berhasil dinonaktifkan.';
        } else {
            $user->update([
                'status' => 'active',
            ]);

            $message = 'Pengguna berhasil diaktifkan.';
        }

        return back()->with('success', $message);
    }

    public function resetPassword(User $user)
    {
        $this->authorizeRole();

        if ($user->id === auth()->id()) {
            return back()->with(
                'error',
                'Gunakan menu ubah password untuk mengubah password akun Anda sendiri.'
            );
        }

        $user->update([
            'password' => Hash::make('password123'),
        ]);

        return back()->with(
            'success',
            'Password pengguna berhasil direset menjadi password123.'
        );
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
