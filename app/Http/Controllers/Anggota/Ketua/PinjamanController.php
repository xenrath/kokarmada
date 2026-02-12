<?php

namespace App\Http\Controllers\Anggota\Ketua;

use App\Http\Controllers\Controller;
use App\Models\Pinjaman;
use App\Models\PinjamanAnalis;
use App\Models\User;
use App\Models\UserDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PinjamanController extends Controller
{
    public function index()
    {
        $pinjamans = Pinjaman::select(
            'id',
            'user_id',
            'tanggal_pengajuan',
            'nominal',
            'jangka_waktu',
            'tipe_angsuran',
            'status',
        )
            ->with('user:id,nama')
            ->with('pinjaman_analis:pinjaman_id,nominal')
            ->get();

        return view('anggota.ketua.pinjaman.index', compact('pinjamans'));
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

        $pinjaman_analis = PinjamanAnalis::where('pinjaman_id', $id)
            ->select('nominal', 'catatan')
            ->first();

        return view('anggota.ketua.pinjaman.show', compact(
            'pinjaman',
            'user',
            'user_detail',
            'pinjaman_analis',
        ));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nominal' => 'required',
            'catatan' => 'required',
        ], [
            'nominal.required' => 'Nominal Rekomendasi Analis harus diisi!',
            'catatan.required' => 'Catatan Hasil Analisis harus diisi!',
        ]);

        if ($validator->fails()) {
            return back()
                ->withInput()
                ->withErrors($validator)
                ->with('error', 'Pengajuan pinjaman gagal diproses!');
        }

        $nominal = (int) str_replace('.', '', $request->input('nominal'));
        $pinjaman_analis = PinjamanAnalis::where('pinjaman_id', $id)
            ->select('id', 'nominal', 'catatan')
            ->first();

        if (empty($pinjaman_analis)) {
            PinjamanAnalis::create([
                'pinjaman_id' => $id,
                'manajer_id' => auth()->user()->id,
                'manajer_nama' => auth()->user()->nama,
                'nominal' => $nominal,
                'catatan' => $request->catatan,
            ]);

            Pinjaman::where('id', $id)->update([
                'status' => 'disetujui_manajer',
            ]);
        } else {
            PinjamanAnalis::where('id', $pinjaman_analis->id)->update([
                'manajer_id' => auth()->user()->id,
                'manajer_nama' => auth()->user()->nama,
                'nominal' => $nominal,
                'catatan' => $request->catatan,
            ]);
        }

        return redirect('anggota/manajer/pinjaman')->with('success', 'Hasil analisis pinjaman berhasil dikirim');
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

        $pdf = Pdf::loadview('anggota.ketua.pinjaman.print', compact('pinjaman', 'dana_terbilang', 'user', 'user_detail'));
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
