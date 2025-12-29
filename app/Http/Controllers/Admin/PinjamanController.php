<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PinjamanController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.pinjaman.index');
    }

    public function create()
    {
        return view('admin.pinjaman.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'email' => 'required|unique:users,email',
            'gender' => 'required',
        ], [
            'nama.required' => 'Nama Lengkap harus diisi!',
            'email.required' => 'Email harus diisi!',
            'email.unique' => 'Email sudah digunakan!',
            'gender.required' => 'Jenis Kelamin harus dipilih!',
        ]);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator)->with('error', 'Gagal menambahkan Anggota!');
        }

        $create = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => bcrypt('bhamada'),
            'gender' => $request->gender,
            'role' => 'anggota',
            'spesial' => $request->spesial ?? 'normal',
        ]);

        if (!$create) {
            return back()->with('error', 'Gagal menambahkan Anggota!');
        }

        return redirect('admin/anggota')->with('success', 'Berhasil menambahkan Anggota');
    }

    public function show($id)
    {
        $user = User::where('id', $id)->first();

        return view('admin.pinjaman.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::where('id', $id)
            ->select('id', 'nama', 'email', 'gender', 'spesial')
            ->first();

        return view('admin.pinjaman.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'email' => 'required|unique:users,email,' . $id . ',id',
            'gender' => 'required',
        ], [
            'nama.required' => 'Nama Lengkap harus diisi!',
            'email.required' => 'Email harus diisi!',
            'email.unique' => 'Email sudah digunakan!',
            'gender.required' => 'Jenis Kelamin harus dipilih!',
        ]);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator)->with('error', 'Gagal memperbarui Pengguna!');
        }

        $create = User::where('id', $id)->update([
            'nama' => $request->nama,
            'email' => $request->email,
            'gender' => $request->gender,
            'spesial' => $request->spesial ?? 'normal',
        ]);

        if (!$create) {
            return back()->with('error', 'Gagal memperbarui Pengguna!');
        }

        return redirect('admin/anggota')->with('success', 'Berhasil memperbarui Pengguna');
    }

    public function destroy($id)
    {
        $status_lama = User::where('id', $id)->value('status');

        if ($status_lama == 'aktif') {
            $status_baru = 'nonaktif';
            $message = 'menonaktifkan';
        } else {
            $status_baru = 'aktif';
            $message = 'mengaktifkan';
        }

        User::where('id', $id)->update([
            'status' => $status_baru
        ]);

        return redirect('admin/anggota')->with('success', 'Berhasil ' . $message . ' Pengguna');
    }
}
