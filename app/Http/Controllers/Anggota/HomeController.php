<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
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

        return view('anggota.index', compact('user', 'user_detail_exists', 'user_detail'));
    }

    public function profile()
    {
        $user = User::where('id', auth()->user()->id)
            ->select('telp', 'nama', 'panggilan', 'gender', 'role', 'spesial')
            ->first();

        $user_detail = UserDetail::where('user_id', auth()->user()->id)
            ->select(
                'no_ktp',
                'masa_berlaku_ktp',
                'foto_diri',
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
                'bank_nama',
                'bank_rekening',
            )
            ->first();

        return view('anggota.profile', compact('user', 'user_detail'));
    }

    public function profile_proses(Request $request)
    {
        $user_detail_exists = UserDetail::where('user_id', auth()->user()->id)->exists();

        if ($user_detail_exists) {
            $validator_detail_no_ktp = 'unique:user_details,no_ktp,' . UserDetail::where('user_id', auth()->user()->id)->value('id');
            $validator_detail_foto_diri = 'nullable';
            $validator_detail_file_ktp = 'nullable';
            $validator_detail_file_kk = 'nullable';
        } else {
            $validator_detail_no_ktp = 'unique:user_details,no_ktp';
            $validator_detail_foto_diri = 'required';
            $validator_detail_file_ktp = 'required';
            $validator_detail_file_kk = 'required';
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'panggilan' => 'required',
            'gender' => 'required',
            'telp' => 'required|unique:users,telp,' . auth()->user()->id,
            'detail_no_ktp' => 'required|' . $validator_detail_no_ktp,
            'detail_masa_berlaku_ktp' => 'required',
            'detail_foto_diri' => $validator_detail_foto_diri . '|mimes:jpg,jpeg,png|max:2048',
            'detail_file_ktp' => $validator_detail_file_ktp . '|mimes:pdf,jpg,jpeg,png|max:2048',
            'detail_file_kk' => $validator_detail_file_kk . '|mimes:pdf,jpg,jpeg,png|max:2048',
            'detail_tempat_lahir' => 'required',
            'detail_tanggal_lahir' => 'required',
            'detail_bulan_lahir' => 'required',
            'detail_tahun_lahir' => 'required',
            'detail_alamat' => 'required',
            'detail_kode_pos' => 'required',
            'detail_pekerjaan' => 'required',
            'detail_no_npwp' => 'required',
            'detail_nama_ibu' => 'required',
            'detail_tinggal_bersama' => 'required',
            'detail_bank_nama' => 'required',
            'detail_bank_rekening' => 'required',
        ], [
            'nama.required' => 'Nama Lengkap harus diisi!',
            'panggilan.required' => 'Nama Panggilan harus diisi!',
            'gender.required' => 'Jenis Kelamin harus dipilih!',
            'telp.required' => 'No. Hp / WhatsApp harus diisi!',
            'telp.unique' => 'No. Hp / WhatsApp sudah digunakan!',
            'gender.required' => 'Jenis Kelamin harus dipilih!',
            'detail_no_ktp.required' => 'No. KTP harus diisi!',
            'detail_no_ktp.unique' => 'No. KTP sudah digunakan!',
            'detail_masa_berlaku_ktp.required' => 'Masa Berlaku KTP harus diisi!',
            'detail_foto_diri.required' => 'Foto Diri harus ditambahkan!',
            'detail_file_ktp.required' => 'File KTP harus ditambahkan!',
            'detail_file_ktp.mimes' => 'File KTP harus berupa pdf atau gambar (jpg, jpeg, png).',
            'detail_file_ktp.max' => 'File KTP yang ditambahkan terlalu besar!',
            'detail_file_kk.required' => 'File KK harus ditambahkan!',
            'detail_file_kk.mimes' => 'File KK harus berupa pdf atau gambar (jpg, jpeg, png).',
            'detail_file_kk.max' => 'File KK yang ditambahkan terlalu besar!',
            'detail_tempat_lahir.required' => 'Tempat Lahir harus diisi!',
            'detail_tanggal_lahir.required' => 'Tanggal harus dipilih!',
            'detail_bulan_lahir.required' => 'Bulan harus dipilih!',
            'detail_tahun_lahir.required' => 'Tahun harus diisi!',
            'detail_alamat.required' => 'Alamat Lengkap harus diisi!',
            'detail_kode_pos.required' => 'Kode POS harus diisi!',
            'detail_pekerjaan.required' => 'Pekerjaan harus diisi!',
            'detail_no_npwp.required' => 'No. NPWP harus diisi!',
            'detail_nama_ibu.required' => 'Nama Gadis Ibu Kandung harus diisi!',
            'detail_tinggal_bersama.required' => 'Tinggal Bersama harus diisi!',
            'detail_bank_nama.required' => 'Nama Bank harus diisi!',
            'detail_bank_rekening.required' => 'Nomor Rekening Bank harus diisi!',
        ]);

        if ($validator->fails()) {
            return back()
                ->withInput()
                ->withErrors($validator->errors())
                ->with('error', 'Gagal memperbarui Profile!');
        }

        $update_user = User::where('id', auth()->user()->id)->update([
            'nama' => $request->nama,
            'panggilan' => $request->panggilan,
            'gender' => $request->gender,
            'telp' => $request->telp,
        ]);

        if (!$update_user) {
            return back()
                ->withInput()
                ->withErrors($validator->errors())
                ->with('error', 'Gagal memperbarui Profile!');
        }

        if ($request->detail_foto_diri) {
            $detail_foto_diri_waktu = Carbon::now()->format('ymdhis');
            $detail_foto_diri_random = rand(10, 99);
            $detail_foto_diri = 'foto_diri/' . $detail_foto_diri_waktu . $detail_foto_diri_random . '.' . $request->detail_foto_diri->getClientOriginalExtension();
            $request->detail_foto_diri->storeAs('public/uploads/', $detail_foto_diri);
        } else {
            $detail_foto_diri = $user_detail_exists ? UserDetail::where('user_id', auth()->user()->id)->value('foto_diri') : null;
        }

        if ($request->detail_file_ktp) {
            $detail_file_ktp_waktu = Carbon::now()->format('ymdhis');
            $detail_file_ktp_random = rand(10, 99);
            $detail_file_ktp = 'file_ktp/' . $detail_file_ktp_waktu . $detail_file_ktp_random . '.' . $request->detail_file_ktp->getClientOriginalExtension();
            $request->detail_file_ktp->storeAs('public/uploads/', $detail_file_ktp);
        } else {
            $detail_file_ktp = $user_detail_exists ? UserDetail::where('user_id', auth()->user()->id)->value('file_ktp') : null;
        }

        if ($request->detail_file_kk) {
            $detail_file_kk_waktu = Carbon::now()->format('ymdhis');
            $detail_file_kk_random = rand(10, 99);
            $detail_file_kk = 'file_kk/' . $detail_file_kk_waktu . $detail_file_kk_random . '.' . $request->detail_file_kk->getClientOriginalExtension();
            $request->detail_file_kk->storeAs('public/uploads/', $detail_file_kk);
        } else {
            $detail_file_kk = $user_detail_exists ? UserDetail::where('user_id', auth()->user()->id)->value('file_kk') : null;
        }

        $tanggal_lahir = sprintf(
            '%04d-%02d-%02d',
            $request->detail_tahun_lahir,
            $request->detail_bulan_lahir,
            $request->detail_tanggal_lahir
        );

        if ($user_detail_exists) {
            $update_user_detail = UserDetail::where('user_id', auth()->user()->id)->update([
                'no_ktp' => $request->detail_no_ktp,
                'masa_berlaku_ktp' => $request->detail_masa_berlaku_ktp,
                'foto_diri' => $detail_foto_diri,
                'file_ktp' => $detail_file_ktp,
                'file_kk' => $detail_file_kk,
                'tempat_lahir' => $request->detail_tempat_lahir,
                'tanggal_lahir' => $tanggal_lahir,
                'alamat' => $request->detail_alamat,
                'kode_pos' => $request->detail_kode_pos,
                'pekerjaan' => $request->detail_pekerjaan,
                'no_npwp' => $request->detail_no_npwp,
                'nama_ibu' => $request->detail_nama_ibu,
                'tinggal_bersama' => $request->detail_tinggal_bersama,
                'nama_pasangan' => $request->detail_nama_pasangan,
                'pekerjaan_pasangan' => $request->detail_pekerjaan_pasangan,
                'bank_nama' => $request->detail_bank_nama,
                'bank_rekening' => $request->detail_bank_rekening,
            ]);

            if (!$update_user_detail) {
                return back()
                    ->withInput()
                    ->withErrors($validator->errors())
                    ->with('error', 'Gagal memperbarui Profile!');
            }
        } else {
            $create_user_detail = UserDetail::create([
                'user_id' => auth()->user()->id,
                'no_ktp' => $request->detail_no_ktp,
                'masa_berlaku_ktp' => $request->detail_masa_berlaku_ktp,
                'foto_diri' => $detail_foto_diri,
                'file_ktp' => $detail_file_ktp,
                'file_kk' => $detail_file_kk,
                'tempat_lahir' => $request->detail_tempat_lahir,
                'tanggal_lahir' => $tanggal_lahir,
                'alamat' => $request->detail_alamat,
                'kode_pos' => $request->detail_kode_pos,
                'pekerjaan' => $request->detail_pekerjaan,
                'no_npwp' => $request->detail_no_npwp,
                'nama_ibu' => $request->detail_nama_ibu,
                'tinggal_bersama' => $request->detail_tinggal_bersama,
                'nama_pasangan' => $request->detail_nama_pasangan,
                'pekerjaan_pasangan' => $request->detail_pekerjaan_pasangan,
                'bank_nama' => $request->detail_bank_nama,
                'bank_rekening' => $request->detail_bank_rekening,
            ]);

            if (!$create_user_detail) {
                return back()
                    ->withInput()
                    ->withErrors($validator->errors())
                    ->with('error', 'Gagal memperbarui Profile!');
            }
        }

        return redirect('anggota/profile')->with('success', 'Berhasil memperbarui Profile');
    }

    public function password()
    {
        return view('anggota.password');
    }

    public function password_proses(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|confirmed',
        ], [
            'password.required' => 'Password Baru harus diisi!',
            'password.confirmed' => 'Konfirmasi Password tidak sesuai!',
        ]);

        if ($validator->fails()) {
            return back()
                ->withInput()
                ->withErrors($validator->errors())
                ->with('error', 'Gagal memperbarui Password!');
        }

        $update = User::where('id', auth()->user()->id)->update([
            'password' => bcrypt($request->password),
        ]);

        if (!$update) {
            return back()
                ->withInput()
                ->withErrors($validator->errors())
                ->with('error', 'Gagal memperbarui Password!');
        }

        return redirect('anggota')->with('success', 'Berhasil memperbarui Password');;
    }
}
