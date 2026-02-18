<?php

namespace App\Http\Controllers\Anggota\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\Pinjaman;
use App\Models\PinjamanAnalis;
use App\Models\Rekening;
use App\Models\User;
use App\Models\UserDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KeuanganRekeningController extends Controller
{
    public function index()
    {
        $rekenings = Rekening::select(
            'id',
            'jenis',
            'bank',
            'nomor',
            'nama',
            'is_aktif',
        )->get();

        return view('anggota.bendahara.keuangan-rekening.index', compact('rekenings'));
    }

    public function create()
    {
        return view('anggota.bendahara.keuangan-rekening.create');
    }

    public function store(Request $request)
    {
        if ($request->jenis == 'bank') {
            $validator_bank = 'required';
            $validator_nomor = 'required';
            $validator_nama_atas = 'required';
            $validator_nama_rekening = 'nullable';
        } else {
            $validator_bank = 'nullable';
            $validator_nomor = 'nullable';
            $validator_nama_atas = 'nullable';
            $validator_nama_rekening = 'required';
        }

        $validator = Validator::make($request->all(), [
            'jenis' => 'required',
            'bank' => $validator_bank,
            'nomor' => $validator_nomor,
            'nama_atas' => $validator_nama_atas,
            'nama_rekening' => $validator_nama_rekening,
        ], [
            'jenis.required' => 'Jenis Rekening harus dipilih!',
            'bank.required' => 'Nama Bank harus diisi!',
            'nomor.required' => 'Nomor Rekening harus dipilih!',
            'nama_atas.required' => 'Atas Nama harus diisi!',
            'nama_rekening.required' => 'Nama Rekening harus diisi!',
        ]);

        if ($validator->fails()) {
            return back()
                ->withInput()
                ->withErrors($validator)
                ->with('error', 'Gagal menambahkan Rekening!');
        }

        if ($request->jenis == 'bank') {
            $bank = $request->bank;
            $nomor = $request->nomor;
            $nama = $request->nama_atas;
        } else {
            $bank = null;
            $nomor = null;
            $nama = $request->nama_rekening;
        }

        Rekening::create([
            'jenis' => $request->jenis,
            'bank' => $bank,
            'nomor' => $nomor,
            'nama' => $nama,
        ]);

        return redirect('anggota/bendahara/keuangan-rekening')->with('success', 'Berhasil menambahkan Rekening');
    }

    public function destroy($id)
    {
        $is_aktif = Rekening::where('id', $id)->value('is_aktif');

        if ($is_aktif) {
            $message = 'menonaktifkan';
        } else {
            $message = 'mengaktifkan';
        }

        Rekening::where('id', $id)->update([
            'is_aktif' => !$is_aktif
        ]);

        return back()->with('success', 'Berhasil ' . $message . ' Rekening');
    }
}
