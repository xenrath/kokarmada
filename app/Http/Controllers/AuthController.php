<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            if (auth()->user()->isDev()) {
                return redirect('dev');
            }
            if (auth()->user()->isAdmin()) {
                return redirect('admin');
            }
            if (auth()->user()->isAnggota()) {
                return redirect('anggota');
            }
        }

        return redirect('login');
    }

    public function login()
    {
        if (auth()->check()) {
            return redirect('/');
        } else {
            return view('login');
        }
    }

    public function login_proses(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ], [
            'email.required' => 'Email harus diisi!',
            'password.required' => 'Password harus diisi!',
        ]);

        if ($validator->fails()) {
            return back()
                ->withInput()
                ->withErrors($validator)
                ->with('error', 'Gagal melakukan Login!');
        }

        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
            'status' => 'aktif'
        ])) {
            $request->session()->regenerate();
            return redirect('/');
        } else {
            return back()
                ->withInput()
                ->with('error', 'Email atau Password salah!');
        }
    }

    public function logout()
    {
        if (auth()->check()) {
            Auth::logout();
        }

        return redirect('login');
    }
}
