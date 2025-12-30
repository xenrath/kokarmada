@extends('layout.app')

@section('title', 'Profile Saya')

@section('content')
    <!-- Start Content-->
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Profile Saya</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="card mb-2 rounded-0">
            <form action="{{ url('anggota/profile') }}" method="POST" autocomplete="off" id="form-submit"
                enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="nama" class="form-label">Nama Lengkap *</label>
                            <input type="text" id="nama" name="nama"
                                class="form-control rounded-0 @error('nama') is-invalid @enderror"
                                value="{{ old('nama', $user->nama) }}">
                            @error('nama')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="panggilan" class="form-label">Nama Panggilan *</label>
                            <input type="text" id="panggilan" name="panggilan"
                                class="form-control rounded-0 @error('panggilan') is-invalid @enderror"
                                value="{{ old('panggilan', $user->panggilan) }}">
                            @error('panggilan')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="gender" class="form-label">Jenis Kelamin *</label>
                            <select class="form-select rounded-0 @error('gender') is-invalid @enderror" id="gender"
                                name="gender">
                                <option value="">- Pilih -</option>
                                <option value="L" {{ old('gender', $user->gender) == 'L' ? 'selected' : '' }}>
                                    Laki-laki
                                </option>
                                <option value="P" {{ old('gender', $user->gender) == 'P' ? 'selected' : '' }}>
                                    Perempuan
                                </option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="detail_telp"
                                class="form-label d-flex flex-column flex-md-row align-items-md-center">
                                <span>No. HP / WhatsApp *</span>
                                <small class="text-muted ms-md-2">
                                    (08xxxxxxxxxx)
                                </small>
                            </label>
                            <input type="tel" id="telp" name="telp"
                                class="form-control rounded-0 @error('telp') is-invalid @enderror"
                                value="{{ old('telp', $user->telp) }}">
                            @error('telp')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="detail_file_ktp"
                                class="form-label d-flex flex-column flex-md-row align-items-md-center">
                                File KTP
                                @if (!($user_detail->file_ktp ?? null))
                                    *
                                @endif
                                <small class="text-muted ms-md-2">
                                    (format file: pdf, jpg, png. maksimal 2 mb)
                                </small>
                            </label>
                            <input type="file" id="detail_file_ktp" name="detail_file_ktp"
                                class="form-control rounded-0 @error('detail_file_ktp') is-invalid @enderror"
                                accept=".pdf,image/*">
                            @error('detail_file_ktp')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            @if ($user_detail->file_ktp ?? null)
                                <a href="{{ asset('storage/uploads/' . $user_detail->file_ktp) }}"
                                    class="btn btn-sm btn-outline-secondary float-end rounded-0 mt-2" target="_blank"
                                    rel="noopener noreferrer">
                                    Lihat File KTP
                                </a>
                            @endif
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="detail_file_kk"
                                class="form-label d-flex flex-column flex-md-row align-items-md-center">
                                File KK
                                @if (!($user_detail->file_kk ?? null))
                                    *
                                @endif
                                <small class="text-muted ms-md-2">
                                    (format file: pdf, jpg, png. maksimal 2 mb)
                                </small>
                            </label>
                            <input type="file" id="detail_file_kk" name="detail_file_kk"
                                class="form-control rounded-0 @error('detail_file_kk') is-invalid @enderror">
                            @error('detail_file_kk')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            @if ($user_detail->file_kk ?? null)
                                <a href="{{ asset('storage/uploads/' . $user_detail->file_kk) }}"
                                    class="btn btn-sm btn-outline-secondary float-end rounded-0 mt-2" target="_blank"
                                    rel="noopener noreferrer">
                                    Lihat File KK
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="detail_tempat_lahir" class="form-label">Tempat Lahir *</label>
                            <input type="text" id="detail_tempat_lahir" name="detail_tempat_lahir"
                                class="form-control rounded-0 @error('detail_tempat_lahir') is-invalid @enderror"
                                value="{{ old('detail_tempat_lahir', $user_detail->tempat_lahir ?? null) }}">
                            @error('detail_tempat_lahir')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-0">
                            <label for="detail_tanggal_lahir" class="form-label">Tanggal Lahir *</label>
                            <div class="row g-1">
                                <div class="col-4 mb-2">
                                    <select
                                        class="form-select rounded-0 @error('detail_tanggal_lahir') is-invalid @enderror"
                                        id="detail_tanggal_lahir" name="detail_tanggal_lahir">
                                        <option value="">Tanggal</option>
                                        @for ($i = 1; $i <= 31; $i++)
                                            <option value="{{ $i }}"
                                                {{ old(
                                                    'detail_tanggal_lahir',
                                                    $user_detail && $user_detail->tanggal_lahir ? \Carbon\Carbon::parse($user_detail->tanggal_lahir)->day : null,
                                                ) == $i
                                                    ? 'selected'
                                                    : '' }}>
                                                {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('detail_tanggal_lahir')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-4 mb-2">
                                    <select class="form-select rounded-0 @error('detail_bulan_lahir') is-invalid @enderror"
                                        id="detail_bulan_lahir" name="detail_bulan_lahir">
                                        <option value="">Bulan</option>
                                        <option value="1"
                                            {{ old(
                                                'detail_bulan_lahir',
                                                $user_detail && $user_detail->tanggal_lahir ? \Carbon\Carbon::parse($user_detail->tanggal_lahir)->month : null,
                                            ) == '1'
                                                ? 'selected'
                                                : '' }}>
                                            Januari
                                        </option>
                                        <option value="2"
                                            {{ old(
                                                'detail_bulan_lahir',
                                                $user_detail && $user_detail->tanggal_lahir ? \Carbon\Carbon::parse($user_detail->tanggal_lahir)->month : null,
                                            ) == '2'
                                                ? 'selected'
                                                : '' }}>
                                            Februari
                                        </option>
                                        <option value="3"
                                            {{ old(
                                                'detail_bulan_lahir',
                                                $user_detail && $user_detail->tanggal_lahir ? \Carbon\Carbon::parse($user_detail->tanggal_lahir)->month : null,
                                            ) == '3'
                                                ? 'selected'
                                                : '' }}>
                                            Maret
                                        </option>
                                        <option value="4"
                                            {{ old(
                                                'detail_bulan_lahir',
                                                $user_detail && $user_detail->tanggal_lahir ? \Carbon\Carbon::parse($user_detail->tanggal_lahir)->month : null,
                                            ) == '4'
                                                ? 'selected'
                                                : '' }}>
                                            April
                                        </option>
                                        <option value="5"
                                            {{ old(
                                                'detail_bulan_lahir',
                                                $user_detail && $user_detail->tanggal_lahir ? \Carbon\Carbon::parse($user_detail->tanggal_lahir)->month : null,
                                            ) == '5'
                                                ? 'selected'
                                                : '' }}>
                                            Mei
                                        </option>
                                        <option value="6"
                                            {{ old(
                                                'detail_bulan_lahir',
                                                $user_detail && $user_detail->tanggal_lahir ? \Carbon\Carbon::parse($user_detail->tanggal_lahir)->month : null,
                                            ) == '6'
                                                ? 'selected'
                                                : '' }}>
                                            Juni
                                        </option>
                                        <option value="7"
                                            {{ old(
                                                'detail_bulan_lahir',
                                                $user_detail && $user_detail->tanggal_lahir ? \Carbon\Carbon::parse($user_detail->tanggal_lahir)->month : null,
                                            ) == '7'
                                                ? 'selected'
                                                : '' }}>
                                            Juli
                                        </option>
                                        <option value="8"
                                            {{ old(
                                                'detail_bulan_lahir',
                                                $user_detail && $user_detail->tanggal_lahir ? \Carbon\Carbon::parse($user_detail->tanggal_lahir)->month : null,
                                            ) == '8'
                                                ? 'selected'
                                                : '' }}>
                                            Agustus
                                        </option>
                                        <option value="9"
                                            {{ old(
                                                'detail_bulan_lahir',
                                                $user_detail && $user_detail->tanggal_lahir ? \Carbon\Carbon::parse($user_detail->tanggal_lahir)->month : null,
                                            ) == '9'
                                                ? 'selected'
                                                : '' }}>
                                            September
                                        </option>
                                        <option value="10"
                                            {{ old(
                                                'detail_bulan_lahir',
                                                $user_detail && $user_detail->tanggal_lahir ? \Carbon\Carbon::parse($user_detail->tanggal_lahir)->month : null,
                                            ) == '10'
                                                ? 'selected'
                                                : '' }}>
                                            Oktober
                                        </option>
                                        <option value="11"
                                            {{ old(
                                                'detail_bulan_lahir',
                                                $user_detail && $user_detail->tanggal_lahir ? \Carbon\Carbon::parse($user_detail->tanggal_lahir)->month : null,
                                            ) == '11'
                                                ? 'selected'
                                                : '' }}>
                                            November
                                        </option>
                                        <option value="12"
                                            {{ old(
                                                'detail_bulan_lahir',
                                                $user_detail && $user_detail->tanggal_lahir ? \Carbon\Carbon::parse($user_detail->tanggal_lahir)->month : null,
                                            ) == '12'
                                                ? 'selected'
                                                : '' }}>
                                            Desember
                                        </option>
                                    </select>
                                    @error('detail_bulan_lahir')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-4 mb-2">
                                    <input type="number" id="detail_tahun_lahir" name="detail_tahun_lahir"
                                        class="form-control rounded-0 @error('detail_tahun_lahir') is-invalid @enderror"
                                        value="{{ old(
                                            'detail_tahun_lahir',
                                            $user_detail && $user_detail->tanggal_lahir ? \Carbon\Carbon::parse($user_detail->tanggal_lahir)->year : null,
                                        ) }}"
                                        placeholder="Tahun">
                                    @error('detail_tahun_lahir')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label for="detail_alamat" class="form-label">Alamat Lengkap *</label>
                        <textarea class="form-control rounded-0 @error('detail_alamat') is-invalid @enderror" id="detail_alamat"
                            name="detail_alamat" rows="3">{{ old('detail_alamat', $user_detail->alamat ?? null) }}</textarea>
                        @error('detail_alamat')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="detail_kode_pos" class="form-label">Kode POS *</label>
                            <input type="text" id="detail_kode_pos" name="detail_kode_pos"
                                class="form-control rounded-0 @error('detail_kode_pos') is-invalid @enderror"
                                value="{{ old('detail_kode_pos', $user_detail->kode_pos ?? null) }}">
                            @error('detail_kode_pos')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="detail_pekerjaan" class="form-label">Pekerjaan *</label>
                            <input type="text" id="detail_pekerjaan" name="detail_pekerjaan"
                                class="form-control rounded-0 @error('detail_pekerjaan') is-invalid @enderror"
                                value="{{ old('detail_pekerjaan', $user_detail->pekerjaan ?? null) }}">
                            @error('detail_pekerjaan')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="detail_no_npwp" class="form-label">No. NPWP *</label>
                            <input type="text" id="detail_no_npwp" name="detail_no_npwp"
                                class="form-control rounded-0 @error('detail_no_npwp') is-invalid @enderror"
                                value="{{ old('detail_no_npwp', $user_detail->no_npwp ?? null) }}">
                            @error('detail_no_npwp')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="detail_nama_ibu" class="form-label">Nama Gadis Ibu Kandung *</label>
                            <input type="text" id="detail_nama_ibu" name="detail_nama_ibu"
                                class="form-control rounded-0 @error('detail_nama_ibu') is-invalid @enderror"
                                value="{{ old('detail_nama_ibu', $user_detail->nama_ibu ?? null) }}">
                            @error('detail_nama_ibu')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="detail_tinggal_bersama" class="form-label">Tinggal Bersama *</label>
                            <input type="text" id="detail_tinggal_bersama" name="detail_tinggal_bersama"
                                class="form-control rounded-0 @error('detail_tinggal_bersama') is-invalid @enderror"
                                value="{{ old('detail_tinggal_bersama', $user_detail->tinggal_bersama ?? null) }}">
                            @error('detail_tinggal_bersama')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-body border-top">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="detail_nama_pasangan"
                                class="form-label d-flex flex-column flex-md-row align-items-md-center">
                                <span>
                                    Nama
                                    @if ($user->gender == 'L')
                                        Istri
                                    @else
                                        Suami
                                    @endif
                                </span>
                                <small class="text-muted ms-md-2">
                                    (kosongkan saja jika masih lajang)
                                </small>
                            </label>
                            <input type="text" id="detail_nama_pasangan" name="detail_nama_pasangan"
                                class="form-control rounded-0 @error('detail_nama_pasangan') is-invalid @enderror"
                                value="{{ old('detail_nama_pasangan', $user_detail->nama_pasangan ?? null) }}">
                            @error('detail_nama_pasangan')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="detail_pekerjaan_pasangan"
                                class="form-label d-flex flex-column flex-md-row align-items-md-center">
                                <span>
                                    Pekerjaan
                                    @if ($user->gender == 'L')
                                        Istri
                                    @else
                                        Suami
                                    @endif
                                </span>
                                <small class="text-muted ms-md-2">
                                    (kosongkan saja jika masih lajang)
                                </small>
                            </label>
                            <input type="text" id="detail_pekerjaan_pasangan" name="detail_pekerjaan_pasangan"
                                class="form-control rounded-0 @error('detail_pekerjaan_pasangan') is-invalid @enderror"
                                value="{{ old('detail_pekerjaan_pasangan', $user_detail->pekerjaan_pasangan ?? null) }}">
                            @error('detail_pekerjaan_pasangan')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary rounded-0" id="btn-submit" onclick="form_submit()">
                        <span id="btn-submit-text">
                            <i class="mdi mdi-content-save"></i>
                            Simpan
                        </span>
                        <span id="btn-submit-load" style="display: none;">
                            <i class="mdi mdi-spin mdi-loading"></i>
                            Memproses...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- container -->
@endsection

@section('script')
    <script>
        function form_submit() {
            $('#btn-submit').prop('disabled', true);
            $('#btn-submit-text').hide();
            $('#btn-submit-load').show();
            $('#form-submit').submit();
        }
    </script>
@endsection
