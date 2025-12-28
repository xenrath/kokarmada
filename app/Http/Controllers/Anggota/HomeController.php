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

        return view('user.index', compact('user'));
    }
}
