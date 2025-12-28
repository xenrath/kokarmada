<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $user = User::where('role', 'anggota')->count();

        return view('admin.index', compact('user'));
    }
}
