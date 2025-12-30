<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnggotaController extends Controller
{
    public function index(Request $request)
    {
        $spesial = $request->spesial;
        $keyword = $request->keyword;

        $users = User::where('role', '!=', 'admin')
            ->when($spesial, function ($query) use ($spesial) {
                $query->where('spesial', $spesial);
            })
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('nama', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%");
                });
            })
            ->select(
                'id',
                'telp',
                'nama',
                'panggilan',
                'gender',
                'role',
                'spesial'
            )
            ->orderBy('status')
            ->orderBy('nama')
            ->get();

        return view('admin.anggota.index', compact('users'));
    }

    public function create()
    {
        return view('admin.anggota.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'panggilan' => 'required',
            'gender' => 'required',
            'telp' => 'required|unique:users,telp',
        ], [
            'nama.required' => 'Nama Lengkap harus diisi!',
            'panggilan.required' => 'Nama Panggilan harus diisi!',
            'gender.required' => 'Jenis Kelamin harus dipilih!',
            'telp.required' => 'No. HP / WhatsApp harus diisi!',
            'telp.unique' => 'No. HP / WhatsApp sudah digunakan!',
        ]);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator)->with('error', 'Gagal menambahkan Anggota!');
        }

        $create = User::create([
            'nama' => $request->nama,
            'panggilan' => $request->panggilan,
            'telp' => $request->telp,
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
        $user = User::where('id', $id)
            ->select(
                'telp',
                'nama',
                'panggilan',
                'gender',
                'role',
                'spesial'
            )
            ->first();

        $user_detail = UserDetail::where('user_id', $id)
            ->select(
                'file_ktp',
                'file_kk',
                'tempat_lahir',
                'tanggal_lahir',
                'alamat',
                'kode_pos',
                'pekerjaan',
                'no_npwp',
                'nama_ibu',
                'tinggal_bersama',
            )
            ->first();

        return view('admin.anggota.show', compact('user', 'user_detail'));
    }

    public function edit($id)
    {
        $user = User::where('id', $id)
            ->select(
                'id',
                'telp',
                'nama',
                'panggilan',
                'gender',
                'spesial'
            )
            ->first();

        return view('admin.anggota.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'panggilan' => 'required',
            'gender' => 'required',
            'telp' => 'required|unique:users,telp,' . $id,
        ], [
            'nama.required' => 'Nama Lengkap harus diisi!',
            'panggilan.required' => 'Nama Panggilan harus diisi!',
            'gender.required' => 'Jenis Kelamin harus dipilih!',
            'telp.required' => 'No. HP / WhatsApp harus diisi!',
            'telp.unique' => 'No. HP / WhatsApp sudah digunakan!',
        ]);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator)->with('error', 'Gagal memperbarui Pengguna!');
        }

        $create = User::where('id', $id)->update([
            'nama' => $request->nama,
            'panggilan' => $request->panggilan,
            'telp' => $request->telp,
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

    public function reset_password($id)
    {
        User::where('id', $id)->update([
            'password' => bcrypt('bhamada'),
        ]);

        return redirect('admin/anggota')->with('success', 'Berhasil mereset password Pengguna');
    }
}
