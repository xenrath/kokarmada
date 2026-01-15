<?php

namespace App\Http\Controllers\Anggota\Manajer;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Models\Pinjaman;
use App\Models\PinjamanAgunan;
use App\Models\PinjamanUser;
use App\Models\User;
use App\Models\UserDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PinjamanController extends Controller
{
    public function index()
    {
        $pinjamans = Pinjaman::where('status', 'diajukan')
            ->select(
                'id',
                'user_id',
                'tanggal_pengajuan',
                'nominal',
                'jangka_waktu',
                'tipe_angsuran',
                'status',
            )
            ->with('user:id,nama')
            ->get();

        return view('anggota.manajer.pinjaman.index', compact('pinjamans'));
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
        $user_detail_exists = UserDetail::where('user_id', auth()->user()->id)->exists();
        if (!$user_detail_exists) {
            return redirect('anggota/pinjaman')->with('error', 'Anda belum melengkapi Data Diri!');
        }

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

        DB::transaction(function () use ($request) {
            $last = Pinjaman::lockForUpdate()
                ->orderBy('urutan', 'desc')
                ->first();
            $urutan = $last ? $last->urutan + 1 : 100;
            $kode = $this->generate_kode($urutan);

            $nominal = (int) str_replace('.', '', $request->nominal);
            $pendapatan_kotor = (int) str_replace('.', '', $request->pendapatan_kotor);
            $pendapatan_bersih = (int) str_replace('.', '', $request->pendapatan_bersih);

            $bunga = Pengaturan::where('id', 1)->value('bunga_pinjaman');
            $bunga_tahun = $bunga / 100;
            $total = $nominal + ($nominal * $bunga_tahun * $request->jangka_waktu);

            $waktu = Carbon::now()->format('ymdhis');
            $slip_gaji = 'slip_gaji/' . $urutan . '-' . $waktu . '.' . $request->slip_gaji->getClientOriginalExtension();
            $bukti_file = 'bukti_agunan/' . $urutan . '-' . $waktu . '.' . $request->bukti_file->getClientOriginalExtension();

            $pinjaman = Pinjaman::create([
                'urutan' => $urutan,
                'kode' => $kode,
                'user_id' => auth()->user()->id,
                'tanggal_pengajuan' => Carbon::now()->format('Y-m-d'),
                'nominal' => $nominal,
                'tujuan' => $request->tujuan,
                'usaha' => $request->usaha,
                'usaha_lainnya' => $request->usaha_lainnya,
                'jangka_waktu' => $request->jangka_waktu,
                'tipe_angsuran' => $request->tipe_angsuran,
                'tempat_kerja' => $request->tempat_kerja,
                'jabatan_terakhir' => $request->jabatan_terakhir,
                'lama_kerja' => $request->lama_kerja,
                'pendapatan_kotor' => $pendapatan_kotor,
                'pendapatan_bersih' => $pendapatan_bersih,
                'slip_gaji' => $slip_gaji,
                'bunga_persen' => $bunga,
                'total_pinjaman' => $total,
                'status' => 'diajukan',
            ]);

            if ($nominal > 25000000) {
                PinjamanAgunan::create([
                    'pinjaman_id' => $pinjaman->id,
                    'jenis_agunan' => $request->jenis_agunan,
                    'jenis_agunan_lainnya' => $request->jenis_agunan_lainnya,
                    'bukti_agunan' => $request->bukti_agunan,
                    'bukti_kepemilikan' => $request->bukti_kepemilikan,
                    'bukti_file' => $bukti_file,
                ]);
            }

            PinjamanUser::create([
                'pinjaman_id' => $pinjaman->id,
                'telp' => auth()->user()->telp,
                'nama' => auth()->user()->nama,
                'panggilan' => auth()->user()->panggilan,
                'gender' => auth()->user()->gender,
                'file_kk' => auth()->user()->detail->file_kk,
                'file_ktp' => auth()->user()->detail->file_ktp,
                'tempat_lahir' => auth()->user()->detail->tempat_lahir,
                'tanggal_lahir' => auth()->user()->detail->tanggal_lahir,
                'alamat' => auth()->user()->detail->alamat,
                'kode_pos' => auth()->user()->detail->kode_pos,
                'pekerjaan' => auth()->user()->detail->pekerjaan,
                'no_npwp' => auth()->user()->detail->no_npwp,
                'nama_ibu' => auth()->user()->detail->nama_ibu,
                'tinggal_bersama' => auth()->user()->detail->tinggal_bersama,
                'nama_pasangan' => auth()->user()->detail->nama_pasangan,
                'pekerjaan_pasangan' => auth()->user()->detail->pekerjaan_pasangan,
            ]);

            $request->slip_gaji->storeAs('public/uploads/', $slip_gaji);
            $request->bukti_file->storeAs('public/uploads/', $bukti_file);
        });

        return redirect('anggota/pinjaman')->with('success', 'Berhasil membuat Pinjaman');
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
            ->with('pinjaman_user:pinjaman_id,telp')
            ->with('pinjaman_agunan:pinjaman_id,jenis_agunan,jenis_agunan_lainnya,bukti_agunan,bukti_kepemilikan,bukti_file')
            ->first();

        $user = User::where('id', $pinjaman->user_id)
            ->select(
                'telp',
                'nama',
                'panggilan',
                'gender',
            )
            ->first();

        $user_detail = UserDetail::where('user_id', $pinjaman->user_id)
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
                'nama_pasangan',
                'pekerjaan_pasangan',
            )
            ->first();

        return view('anggota.manajer.pinjaman.show', compact('pinjaman', 'user', 'user_detail'));
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

    public function print($id)
    {
        $pinjaman = Pinjaman::where('id', $id)
            ->select(
                'id',
                'user_id',
                'kode',
                'nominal',
                'tujuan',
                'usaha',
                'usaha_lainnya',
                'jangka_waktu',
                'tipe_angsuran',
                'tempat_kerja',
                'jabatan_terakhir',
                'lama_kerja',
                'pendapatan_kotor',
                'pendapatan_bersih',
            )
            ->with('pinjaman_agunan', function ($query) {
                $query->select(
                    'pinjaman_id',
                    'jenis_agunan',
                    'jenis_agunan_lainnya',
                    'bukti_agunan',
                    'bukti_kepemilikan',
                );
            })
            ->first();

        $user = User::where('id', $pinjaman->user_id)
            ->select(
                'telp',
                'nama',
                'panggilan',
                'gender',
            )
            ->first();

        $user_detail = UserDetail::where('user_id', $pinjaman->user_id)
            ->select(
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
                'nama_pasangan',
                'pekerjaan_pasangan',
            )
            ->first();

        $dana_terbilang = $this->terbilang($pinjaman->nominal) . 'rupiah';

        $pdf = Pdf::loadview('anggota.manajer.pinjaman.print', compact('pinjaman', 'dana_terbilang', 'user', 'user_detail'));
        return $pdf->stream('Formulir Pengajuan Pinjaman Koperasi');
    }

    public function terbilang($value)
    {
        $angka = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
        $space = $value > 0 ? " " : null;

        if ($value < 12) {
            return $angka[$value] . $space;
        } elseif ($value < 20) {
            return $this->terbilang($value - 10) . "belas" . $space;
        } elseif ($value < 100) {
            return $this->terbilang($value / 10) . "puluh" . $space . $this->terbilang($value % 10);
        } elseif ($value < 200) {
            return "seratus" . $this->terbilang($value - 100);
        } elseif ($value < 1000) {
            return $this->terbilang($value / 100) . "ratus" . $space . $this->terbilang($value % 100);
        } elseif ($value < 2000) {
            return "seribu" . $this->terbilang($value - 1000);
        } elseif ($value < 1000000) {
            return $this->terbilang($value / 1000) . "ribu" . $space . $this->terbilang($value % 1000);
        } elseif ($value < 1000000000) {
            return $this->terbilang($value / 1000000) . "juta" . $space . $this->terbilang($value % 1000000);
        }
    }
}
