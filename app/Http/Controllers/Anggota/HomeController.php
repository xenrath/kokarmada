<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $user = User::where('id', auth()->user()->id)
            ->select('nama', 'email', 'gender', 'role', 'spesial')
            ->first();

        return view('anggota.index', compact('user'));
    }

    public function profile()
    {
        $user = User::where('id', auth()->user()->id)
            ->select('nama', 'email', 'gender', 'role', 'spesial')
            ->first();

        return view('anggota.profile', compact('user'));
    }

    public function profile_proses()
    {
        $user = User::where('id', auth()->user()->id)->first();
        $user->nama = request('nama');
        $user->email = request('email');
        $user->gender = request('gender');
        $user->save();
        return redirect('anggota/profile')->with('success', 'Profile updated successfully');;
    }
}
