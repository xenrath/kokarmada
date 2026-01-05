<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Models\User;
use App\Models\UserDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PinjamanController extends Controller
{
    public function index()
    {
        return view('anggota.pinjaman.index');
    }

    public function create()
    {
        $pengaturan = Pengaturan::select(
            'bunga_pinjaman_pertahun',
            'jangka_waktu_pinjaman'
        )->first();

        return view('anggota.pinjaman.create', compact('pengaturan'));
    }

    public function store(Request $request)
    {

        if ($request->usaha == 'lainnya') {
            $validator_usaha_lainnya = 'required';
        } else {
            $validator_usaha_lainnya = 'nullable';
        }

        $nominal = (int) str_replace('.', '', $request->nominal);

        if ($nominal > 25000000) {
            $validator_jenis_agunan = 'required';
            $validator_bukti_agunan = 'required';
            $validator_bukti_kepemilikan = 'required';
            $validator_bukti_file = 'required';

            if ($request->jenis_agunan == 'lainnya') {
                $validator_jenis_agunan_lainnya = 'required';
            } else {
                $validator_jenis_agunan_lainnya = 'nullable';
            }
        } else {
            $validator_jenis_agunan = 'nullable';
            $validator_jenis_agunan_lainnya = 'nullable';
            $validator_bukti_agunan = 'nullable';
            $validator_bukti_kepemilikan = 'nullable';
            $validator_bukti_file = 'nullable';
        }

        $validator = Validator::make($request->all(), [
            'nominal' => 'required',
            'tujuan' => 'required',
            'usaha' => 'required',
            'usaha_lainnya' => $validator_usaha_lainnya,
            'jangka_waktu' => 'required',
            'tipe_angsuran' => 'required',
            'jenis_agunan' => $validator_jenis_agunan,
            'jenis_agunan_lainnya' => $validator_jenis_agunan_lainnya,
            'bukti_agunan' => $validator_bukti_agunan,
            'bukti_kepemilikan' => $validator_bukti_kepemilikan,
            'bukti_file' => $validator_bukti_file,
        ], [
            'nominal.required' => 'Nominal Pengajuan harus diisi!',
            'tujuan.required' => 'Tujuan Pengajuan harus diisi!',
            'usaha.required' => 'Usaha harus diisi!',
            'usaha_lainnya.required' => 'Usaha Lainnya harus diisi!',
            'jangka_waktu.required' => 'Jangka Waktu harus diisi!',
            'tipe_angsuran.required' => 'Tipe Angsuran harus diisi!',
            'jenis_agunan.required' => 'Jenis Agunan harus diisi!',
            'jenis_agunan_lainnya.required' => 'Jenis Agunan Lainnya harus diisi!',
            'bukti_agunan.required' => 'Bukti Agunan harus diisi!',
            'bukti_kepemilikan.required' => 'Bukti Kepemilikan harus diisi!',
            'bukti_file.required' => 'File Bukti harus diisi!',
        ]);

        if ($validator->fails()) {
            return back()
                ->withInput()
                ->withErrors($validator)
                ->with('error', 'Gagal membuat Pinjaman!');
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
}
