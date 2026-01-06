<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Models\Pinjaman;
use App\Models\PinjamanAgunan;
use App\Models\PinjamanUser;
use App\Models\User;
use App\Models\UserDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PinjamanController extends Controller
{
    public function index()
    {
        $user = User::where('id', auth()->user()->id)
            ->select('telp', 'nama', 'panggilan', 'gender', 'role', 'spesial')
            ->first();

        $user_detail_exists = UserDetail::where('user_id', auth()->user()->id)->exists();

        $user_detail = UserDetail::where('user_id', auth()->user()->id)
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

        return view('anggota.pinjaman.index', compact('user', 'user_detail_exists', 'user_detail'));
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
        return auth()->user()->detail->pekerjaan;

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
            'tempat_kerja' => 'required',
            'jabatan_terakhir' => 'required',
            'lama_kerja' => 'required',
            'pendapatan_kotor' => 'required',
            'pendapatan_bersih' => 'required',
            'slip_gaji' => 'required',
        ], [
            'nominal.required' => 'Nominal Pengajuan harus diisi!',
            'tujuan.required' => 'Tujuan Pengajuan harus diisi!',
            'usaha.required' => 'Usaha harus dipilih!',
            'usaha_lainnya.required' => 'Usaha Lainnya harus diisi!',
            'jangka_waktu.required' => 'Jangka Waktu harus diisi!',
            'tipe_angsuran.required' => 'Tipe Angsuran harus dipilih!',
            'jenis_agunan.required' => 'Jenis Agunan harus diisi!',
            'jenis_agunan_lainnya.required' => 'Jenis Agunan Lainnya harus diisi!',
            'bukti_agunan.required' => 'Bukti Agunan harus diisi!',
            'bukti_kepemilikan.required' => 'Bukti Kepemilikan harus diisi!',
            'bukti_file.required' => 'File Bukti harus diisi!',
            'tempat_kerja.required' => 'Tempat Bekerja harus diisi!',
            'jabatan_terakhir.required' => 'Jabatan Terakhir harus diisi!',
            'lama_kerja.required' => 'Lama Bekerja harus diisi!',
            'pendapatan_kotor.required' => 'Pendapatan Kotor harus diisi!',
            'pendapatan_bersih.required' => 'Pendapatan Bersih harus diisi!',
            'slip_gaji.required' => 'Slip Gaji harus ditambahkan!',
        ]);

        if ($validator->fails()) {
            return back()
                ->withInput()
                ->withErrors($validator)
                ->with('error', 'Gagal membuat Pinjaman!');
        }

        $user = User::where('id', auth()->user()->id)
            ->select(
                'telp',
                'user',
                'panggilan',
                'gender',
            )->first();

        $user_detail = UserDetail::where('user_id', auth()->user()->id)
            ->select(
                'file_kk',
                'file_ktp',
                'tempat_lahir',
                'tanggal_lahir',
                'alamat',
                'kode_pos',
                'pekerjaan',
                'no_npwp',
                'nama_ibu',
                'tinggal_bersama',
                'nama_pasangan',
                'pekerjaan_pasangan',
            )->first();

        DB::transaction(function () use ($request,) {
            $last = Pinjaman::lockForUpdate()
                ->orderBy('urutan', 'desc')
                ->first();
            $urutan = $last ? $last->urutan + 1 : 100;
            $kode = $this->generate_kode($urutan);
            $nominal = (int) str_replace('.', '', $request->nominal);

            $pinjaman = Pinjaman::create([
                'urutan' => $urutan,
                'kode' => $kode,
                'anggota_id' => auth()->user()->id,
                'tanggal_pengajuan' => Carbon::now()->format('Y-m-d'),
                'nominal' => $nominal,
                'tujuan' => $request->tujuan,
                'usaha' => $request->usaha,
                'usaha_lainnya' => $request->usaha_lainnya,
                'jangka_waktu' => $request->jangka_waktu,
                'tipe_angsuran' => $request->tipe_angsuran,
                'jenis_agunan' => $request->jenis_agunan,
                'jenis_agunan_lainnya' => $request->jenis_agunan_lainnya,
                'bukti_agunan' => $request->bukti_agunan,
                'bukti_kepemilikan' => $request->bukti_kepemilikan,
                'bukti_file' => $request->bukti_file,
                'tempat_kerja' => $request->tempat_kerja,
                'jabatan_terakhir' => $request->jabatan_terakhir,
                'lama_kerja' => $request->lama_kerja,
                'pendapatan_kotor' => $request->pendapatan_kotor,
                'pendapatan_bersih' => $request->pendapatan_bersih,
                'slip_gaji' => $request->slip_gaji,
            ]);

            if ($nominal > 25000000) {
                PinjamanAgunan::create([
                    'pinjaman_id' => $pinjaman->id,
                    'jenis_agunan' => $request->jenis_agunan,
                    'jenis_agunan_lainnya' => $request->jenis_agunan_lainnya,
                    'bukti_agunan' => $request->bukti_agunan,
                    'bukti_kepemilikan' => $request->bukti_kepemilikan,
                    'bukti_file' => $request->bukti_file,
                ]);
            }

            PinjamanUser::create([
                'pinjaman_id' => $user->pinjaman_id,
                'telp' => $user->telp,
                'nama' => $user->nama,
                'panggilan' => $user->panggilan,
                'gender' => $user->gender,
                'file_kk' => $user->file_kk,
                'file_ktp' => $user->file_ktp,
                'tempat_lahir' => $user->tempat_lahir,
                'tanggal_lahir' => $user->tanggal_lahir,
                'alamat' => $user->alamat,
                'kode_pos' => $user->kode_pos,
                'pekerjaan' => $user->pekerjaan,
                'no_npwp' => $user->no_npwp,
                'nama_ibu' => $user->nama_ibu,
                'tinggal_bersama' => $user->tinggal_bersama,
                'nama_pasangan' => $user->nama_pasangan,
                'pekerjaan_pasangan' => $user->pekerjaan_pasangan,
            ]);
        });

        return redirect('admin/anggota')->with('success', 'Berhasil menambahkan Anggota');
    }

    public function generate_kode($nomor_urut)
    {
        $prefix = 'KOPKARMADA';
        $bulan = $this->bulan_romawi(Carbon::now()->month);
        $tahun = Carbon::now()->year;

        return "{$prefix}/{$nomor_urut}/{$bulan}/{$tahun}";
    }

    function bulan_romawi($bulan)
    {
        $romawi = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];

        return $romawi[$bulan] ?? '';
    }
}
