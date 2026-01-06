<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnggotaController extends Controller
{
    public function index(Request $request)
    {
        $spesial = $request->spesial;
        $keyword = $request->keyword;

        $users = User::where('role', 'anggota')
            ->when($spesial, function ($query) use ($spesial) {
                $query->where('spesial', $spesial);
            })
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('nama', 'like', "%{$keyword}%");
                });
            })
            ->select(
                'id',
                'telp',
                'nama',
                'panggilan',
                'gender',
                'role',
                'spesial',
                'status',
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
        $validator_user = Validator::make($request->all(), [
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

        if ($request->boolean('lengkapi_data')) {
            $validator_user_detail = Validator::make($request->all(), [
                'detail_file_ktp' => 'required|mimes:pdf,jpg,jpeg,png|max:2048',
                'detail_file_kk' => 'required|mimes:pdf,jpg,jpeg,png|max:2048',
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
            ], [
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
            ]);
        } else {
            $validator_user_detail = null;
        }

        if ($validator_user->fails() || ($validator_user_detail && $validator_user_detail->fails())) {
            return back()
                ->withInput()
                ->withErrors(
                    $validator_user->errors()->merge(
                        $validator_user_detail?->errors() ?? []
                    )
                )
                ->with('error', 'Gagal menambahkan Anggota!');
        }

        $create_user = User::create([
            'nama' => $request->nama,
            'panggilan' => $request->panggilan,
            'telp' => $request->telp,
            'password' => bcrypt('bhamada'),
            'gender' => $request->gender,
            'role' => 'anggota',
            'spesial' => $request->spesial ?? 'normal',
        ]);

        if (!$create_user) {
            return back()->with('error', 'Gagal menambahkan Anggota!');
        }

        if ($request->boolean('lengkapi_data')) {
            $detail_file_ktp_waktu = Carbon::now()->format('ymdhis');
            $detail_file_ktp_random = rand(10, 99);
            $detail_file_ktp = 'anggota/' . $detail_file_ktp_waktu . $detail_file_ktp_random . '.' . $request->detail_file_ktp->getClientOriginalExtension();
            $request->detail_file_ktp->storeAs('public/uploads/', $detail_file_ktp);

            $detail_file_kk_waktu = Carbon::now()->format('ymdhis');
            $detail_file_kk_random = rand(10, 99);
            $detail_file_kk = 'anggota/' . $detail_file_kk_waktu . $detail_file_kk_random . '.' . $request->detail_file_kk->getClientOriginalExtension();
            $request->detail_file_kk->storeAs('public/uploads/', $detail_file_kk);

            $tanggal_lahir = sprintf(
                '%04d-%02d-%02d',
                $request->detail_tahun_lahir,
                $request->detail_bulan_lahir,
                $request->detail_tanggal_lahir
            );

            $create_user_detail = UserDetail::create([
                'user_id' => $create_user->id,
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
            ]);

            if (!$create_user_detail) {
                return back()
                    ->withInput()
                    ->withErrors(
                        $validator_user->errors()->merge(
                            $validator_user_detail?->errors() ?? []
                        )
                    )
                    ->with('error', 'Gagal menambahkan Anggota!');
            }
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
                'nama_pasangan',
                'pekerjaan_pasangan',
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

        $user_detail_exists = UserDetail::where('user_id', $id)->exists();

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
                'nama_pasangan',
                'pekerjaan_pasangan',
            )
            ->first();

        return view('admin.anggota.edit', compact('user', 'user_detail_exists', 'user_detail'));
    }

    public function update(Request $request, $id)
    {
        $validator_user = Validator::make($request->all(), [
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

        $user_detail_exists = UserDetail::where('user_id', $id)->exists();

        if ($user_detail_exists) {
            $lengkapi_data = true;
            $validator_detail_file_ktp = 'nullable';
            $validator_detail_file_kk = 'nullable';
        } else {
            $lengkapi_data = $request->boolean('lengkapi_data');
            $validator_detail_file_ktp = 'required';
            $validator_detail_file_kk = 'required';
        }

        if ($lengkapi_data) {
            $validator_user_detail = Validator::make($request->all(), [
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
            ], [
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
            ]);
        } else {
            $validator_user_detail = null;
        }

        if ($validator_user->fails() || ($validator_user_detail && $validator_user_detail->fails())) {
            return back()
                ->withInput()
                ->withErrors(
                    $validator_user->errors()->merge(
                        $validator_user_detail?->errors() ?? []
                    )
                )
                ->with('error', 'Gagal menambahkan Anggota!');
        }

        $update_user = User::where('id', $id)->update([
            'nama' => $request->nama,
            'panggilan' => $request->panggilan,
            'telp' => $request->telp,
            'gender' => $request->gender,
            'spesial' => $request->spesial ?? 'normal',
        ]);

        if (!$update_user) {
            return back()->with('error', 'Gagal memperbarui Pengguna!');
        }

        if ($lengkapi_data) {
            if ($request->detail_file_ktp) {
                $detail_file_ktp_waktu = Carbon::now()->format('ymdhis');
                $detail_file_ktp_random = rand(10, 99);
                $detail_file_ktp = 'anggota/' . $detail_file_ktp_waktu . $detail_file_ktp_random . '.' . $request->detail_file_ktp->getClientOriginalExtension();
                $request->detail_file_ktp->storeAs('public/uploads/', $detail_file_ktp);
            } else {
                $detail_file_ktp = $user_detail_exists ? UserDetail::where('user_id', $id)->value('file_ktp') : null;
            }

            if ($request->detail_file_kk) {
                $detail_file_kk_waktu = Carbon::now()->format('ymdhis');
                $detail_file_kk_random = rand(10, 99);
                $detail_file_kk = 'anggota/' . $detail_file_kk_waktu . $detail_file_kk_random . '.' . $request->detail_file_kk->getClientOriginalExtension();
                $request->detail_file_kk->storeAs('public/uploads/', $detail_file_kk);
            } else {
                $detail_file_kk = $user_detail_exists ? UserDetail::where('user_id', $id)->value('file_kk') : null;
            }

            $tanggal_lahir = sprintf(
                '%04d-%02d-%02d',
                $request->detail_tahun_lahir,
                $request->detail_bulan_lahir,
                $request->detail_tanggal_lahir
            );

            if ($user_detail_exists) {
                $update_user_detail = UserDetail::where('user_id', $id)->update([
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
                ]);

                if (!$update_user_detail) {
                    return back()
                        ->withInput()
                        ->withErrors(
                            $validator_user->errors()->merge(
                                $validator_user_detail?->errors() ?? []
                            )
                        )
                        ->with('error', 'Gagal memperbarui Anggota!');
                }
            } else {
                $create_user_detail = UserDetail::create([
                    'user_id' => $id,
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
                ]);

                if (!$create_user_detail) {
                    return back()
                        ->withInput()
                        ->withErrors(
                            $validator_user->errors()->merge(
                                $validator_user_detail?->errors() ?? []
                            )
                        )
                        ->with('error', 'Gagal menambahkan Anggota!');
                }
            }
        }

        return redirect('admin/anggota')->with('success', 'Berhasil memperbarui Anggota');
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
