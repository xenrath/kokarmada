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

    public function anggota_set($id)
    {
        $anggota = User::where('role', 'anggota')
            ->where('id', $id)
            ->select('id', 'nama')
            ->first();

        if (!$anggota) {
            return response()->json([
                'message' => 'Anggota tidak ditemukan'
            ], 404);
        }

        return response()->json($anggota);
    }
}
