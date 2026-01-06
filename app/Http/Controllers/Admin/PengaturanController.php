<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PengaturanController extends Controller
{
    public function index()
    {
        $pengaturan = Pengaturan::select(
            'id',
            'bunga_pinjaman_pertahun',
            'bunga_pinjaman_perbulan',
            'jangka_waktu_pinjaman'
        )->first();

        return view('admin.pengaturan.index', compact('pengaturan'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'bunga_pinjaman_pertahun' => 'required',
            'bunga_pinjaman_perbulan' => 'required',
            'jangka_waktu_pinjaman' => 'required',
        ], [
            'bunga_pinjaman_pertahun.required' => 'Bunga Pinjaman harus diisi!',
            'bunga_pinjaman_perbulan.required' => 'Bunga Pinjaman harus diisi!',
            'jangka_waktu_pinjaman.required' => 'Jangka Waktu Pinjaman harus diisi!',
        ]);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator)->with('error', 'Gagal memperbarui Pengaturan!');
        }

        $update = Pengaturan::where('id', $id)->update([
            'bunga_pinjaman_pertahun' => $request->bunga_pinjaman_pertahun,
            'bunga_pinjaman_perbulan' => $request->bunga_pinjaman_perbulan,
            'jangka_waktu_pinjaman' => $request->jangka_waktu_pinjaman,
        ]);

        if (!$update) {
            return back()->with('error', 'Gagal memperbarui Pengaturan!');
        }

        return redirect('admin/pengaturan')->with('success', 'Berhasil memperbarui Pengaturan');
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
