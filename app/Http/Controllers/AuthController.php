<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Throwable;

class AuthController extends Controller
{
    public function index(): RedirectResponse
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (! $user->isActive()) {
            $this->logoutCurrentSession($request = request());

            return redirect()
                ->route('login')
                ->with('error', 'Akun Anda sudah dinonaktifkan.');
        }

        return match ($user->role) {
            'developer' => redirect('/dev'),
            'admin' => redirect('/admin'),
            'member' => redirect('/member'),
            'analyst_manager' => redirect('/analyst-manager'),
            'treasurer' => redirect('/treasurer'),
            'chairman' => redirect('/chairman'),
            'secretary' => redirect('/secretary'),
            'supervisor' => redirect('/supervisor'),
            default => abort(403, 'Role pengguna tidak dikenali.'),
        };
    }

    public function showLoginForm(): View|RedirectResponse
    {
        if (auth()->check()) {
            return redirect('/');
        }

        return view('login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $validator = Validator::make(
            $request->all(),
            [
                'phone' => ['required', 'string'],
                'password' => ['required', 'string'],
            ],
            [
                'phone.required' => 'Nomor telepon harus diisi.',
                'password.required' => 'Password harus diisi.',
            ]
        );

        if ($validator->fails()) {
            return back()
                ->withInput($request->only('phone'))
                ->withErrors($validator)
                ->with('error', 'Gagal melakukan login.');
        }

        $credentials = [
            'phone' => $request->input('phone'),
            'password' => $request->input('password'),
            'status' => 'active',
        ];

        if (! Auth::attempt($credentials)) {
            return back()
                ->withInput($request->only('phone'))
                ->with('error', 'Nomor telepon atau password salah.');
        }

        $request->session()->regenerate();

        return redirect('/');
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->logoutCurrentSession($request);

        return redirect()
            ->route('login')
            ->with('success', 'Berhasil keluar dari sistem.');
    }

    private function logoutCurrentSession(Request $request): void
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
