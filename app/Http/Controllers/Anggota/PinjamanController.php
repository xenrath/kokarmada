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
                'foto_diri',
                'no_ktp',
                'masa_berlaku_ktp',
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
                'bank_nama',
                'bank_rekening',
            )
            ->first();

        $pinjamans = Pinjaman::where('user_id', auth()->user()->id)
            ->select(
                'id',
                'tanggal_pengajuan',
                'nominal',
                'jangka_waktu',
                'tipe_angsuran',
                'status',
            )
            ->with('user:id,nama')
            ->get();

        return view('anggota.pinjaman.index', compact('user', 'user_detail_exists', 'user_detail', 'pinjamans'));
    }

    public function create()
    {
        $pengaturan = Pengaturan::select(
            'bunga_pinjaman',
            'jangka_waktu_pinjaman'
        )->first();

        return view('anggota.pinjaman.create', compact('pengaturan'));
    }

    public function store(Request $request)
    {
        // ===============================
        // CEK DATA DIRI
        // ===============================

        if (!UserDetail::where('user_id', auth()->id())->exists()) {
            return redirect('anggota/pinjaman')->with('error', 'Anda belum melengkapi Data Diri!');
        }

        // ===============================
        // NORMALISASI ANGKA
        // ===============================
        $nominal = (int) str_replace('.', '', $request->input('nominal'));

        // ===============================
        // VALIDATOR DINAMIS
        // ===============================
        $validator_usaha_lainnya = $request->input('usaha') === 'lainnya'
            ? 'required'
            : 'nullable';

        $validator_jenis_agunan = 'nullable';
        $validator_jenis_agunan_lainnya = 'nullable';
        $validator_bukti_agunan = 'nullable';
        $validator_bukti_kepemilikan = 'nullable';
        $validator_bukti_file = 'nullable';

        if ($nominal > 25000000) {
            $validator_jenis_agunan = 'required';
            $validator_bukti_agunan = 'required';
            $validator_bukti_kepemilikan = 'required';
            $validator_bukti_file = 'required';

            if ($request->input('jenis_agunan') === 'lainnya') {
                $validator_jenis_agunan_lainnya = 'required';
            }
        }

        // ===============================
        // VALIDASI
        // ===============================
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

        // ===============================
        // TRANSAKSI DATABASE
        // ===============================
        DB::transaction(function () use ($request, $nominal) {

            $last = Pinjaman::lockForUpdate()
                ->orderBy('urutan', 'desc')
                ->first();

            $urutan = $last ? $last->urutan + 1 : 100;
            $kode = $this->generate_kode($urutan);

            $pendapatan_kotor = (int) str_replace('.', '', $request->input('pendapatan_kotor'));
            $pendapatan_bersih = (int) str_replace('.', '', $request->input('pendapatan_bersih'));

            $bunga = Pengaturan::where('id', 1)->value('bunga_pinjaman');
            $total = $nominal + ($nominal * ($bunga / 100) * $request->input('jangka_waktu'));

            $waktu = Carbon::now()->format('ymdhis');

            $slip_gaji_path = 'slip_gaji/' . $urutan . '-' . $waktu . '.' .
                $request->file('slip_gaji')->getClientOriginalExtension();

            $bukti_file_path = null;

            if ($nominal > 25000000) {
                $bukti_file_path = 'bukti_agunan/' . $urutan . '-' . $waktu . '.' .
                    $request->file('bukti_file')->getClientOriginalExtension();
            }

            $pinjaman = Pinjaman::create([
                'urutan' => $urutan,
                'kode' => $kode,
                'user_id' => auth()->id(),
                'tanggal_pengajuan' => now()->format('Y-m-d'),
                'nominal' => $nominal,
                'tujuan' => $request->input('tujuan'),
                'usaha' => $request->input('usaha'),
                'usaha_lainnya' => $request->input('usaha_lainnya'),
                'jangka_waktu' => $request->input('jangka_waktu'),
                'tipe_angsuran' => $request->input('tipe_angsuran'),
                'tempat_kerja' => $request->input('tempat_kerja'),
                'jabatan_terakhir' => $request->input('jabatan_terakhir'),
                'lama_kerja' => $request->input('lama_kerja'),
                'pendapatan_kotor' => $pendapatan_kotor,
                'pendapatan_bersih' => $pendapatan_bersih,
                'slip_gaji' => $slip_gaji_path,
                'bunga_persen' => $bunga,
                'total_pinjaman' => $total,
                'status' => 'diajukan',
            ]);

            if ($nominal > 25000000) {
                PinjamanAgunan::create([
                    'pinjaman_id' => $pinjaman->id,
                    'jenis_agunan' => $request->input('jenis_agunan'),
                    'jenis_agunan_lainnya' => $request->input('jenis_agunan_lainnya'),
                    'bukti_agunan' => $request->input('bukti_agunan'),
                    'bukti_kepemilikan' => $request->input('bukti_kepemilikan'),
                    'bukti_file' => $bukti_file_path,
                ]);
            }

            // ===============================
            // UPLOAD FILE
            // ===============================
            $request->file('slip_gaji')
                ->storeAs('public/uploads/', $slip_gaji_path);

            if ($bukti_file_path) {
                $request->file('bukti_file')
                    ->storeAs('public/uploads/', $bukti_file_path);
            }
        });

        return redirect('anggota/pinjaman')
            ->with('success', 'Berhasil membuat Pinjaman');
    }


    public function show($id)
    {
        $pinjaman = Pinjaman::where('id', $id)
            ->select(
                'id',
                'user_id',
                'kode',
                'tanggal_pengajuan',
                'nominal',
                'bunga_persen',
                'jangka_waktu',
                'total_pinjaman',
                'tujuan',
                'usaha',
                'usaha_lainnya',
                'tipe_angsuran',
                'tempat_kerja',
                'jabatan_terakhir',
                'lama_kerja',
                'pendapatan_kotor',
                'pendapatan_bersih',
                'slip_gaji',
                'status',
            )
            ->with('user:id,nama')
            ->with('pinjaman_user:pinjaman_id,telp')
            ->with('pinjaman_agunan:pinjaman_id,jenis_agunan,jenis_agunan_lainnya,bukti_agunan,bukti_kepemilikan,bukti_file')
            ->first();

        return view('anggota.pinjaman.show', compact('pinjaman'));
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
