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
            <form action="{{ url('admin/anggota/' . $user->id) }}" method="POST" autocomplete="off" id="form-submit">
                @csrf
                @method('PUT')
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
                            <label for="detail_nama" class="form-label">Nama Panggilan *</label>
                            <input type="text" id="detail_nama" name="detail_nama"
                                class="form-control rounded-0 @error('detail_nama') is-invalid @enderror"
                                value="{{ old('detail_nama') }}">
                            @error('detail_nama')
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
                            <label for="email" class="form-label">Email *</label>
                            <input type="text" id="email" name="email"
                                class="form-control rounded-0 @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email) }}">
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="detail_telp"
                                class="form-label d-flex flex-column flex-md-row align-items-md-center">
                                <span>No. HP / WhatsApp *</span>
                                <small class="text-muted ms-md-2">
                                    (08xxxxxxxxxx)
                                </small>
                            </label>
                            <input type="tel" id="detail_telp" name="detail_telp"
                                class="form-control rounded-0 @error('detail_telp') is-invalid @enderror"
                                value="{{ old('detail_telp') }}">
                            @error('detail_telp')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="detail_pekerjaan" class="form-label">Pekerjaan *</label>
                            <input type="text" id="detail_pekerjaan" name="detail_pekerjaan"
                                class="form-control rounded-0 @error('detail_pekerjaan') is-invalid @enderror"
                                value="{{ old('detail_pekerjaan') }}">
                            @error('detail_pekerjaan')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="detail_tempat" class="form-label">Tempat Lahir *</label>
                            <input type="text" id="detail_tempat" name="detail_tempat"
                                class="form-control rounded-0 @error('detail_tempat') is-invalid @enderror"
                                value="{{ old('detail_tempat') }}">
                            @error('detail_tempat')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-0">
                            <label for="detail_tanggal" class="form-label">Tanggal Lahir *</label>
                            <div class="row g-1">
                                <div class="col-4 mb-2">
                                    <select class="form-select rounded-0 @error('detail_tanggal') is-invalid @enderror"
                                        id="detail_tanggal" name="detail_tanggal">
                                        <option value="">Tanggal</option>
                                        @for ($i = 1; $i <= 31; $i++)
                                            <option value="{{ $i }}"
                                                {{ old('detail_tanggal') == $i ? 'selected' : '' }}>
                                                {{ $i }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('detail_tanggal')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-4 mb-2">
                                    <select class="form-select rounded-0 @error('detail_bulan') is-invalid @enderror"
                                        id="detail_bulan" name="detail_bulan">
                                        <option value="">Bulan</option>
                                        <option value="1" {{ old('detail_bulan') == '1' ? 'selected' : '' }}>
                                            Januari
                                        </option>
                                        <option value="2" {{ old('detail_bulan') == '2' ? 'selected' : '' }}>
                                            Februari
                                        </option>
                                        <option value="3" {{ old('detail_bulan') == '3' ? 'selected' : '' }}>
                                            Maret
                                        </option>
                                        <option value="4" {{ old('detail_bulan') == '4' ? 'selected' : '' }}>
                                            April
                                        </option>
                                        <option value="5" {{ old('detail_bulan') == '5' ? 'selected' : '' }}>
                                            Mei
                                        </option>
                                        <option value="6" {{ old('detail_bulan') == '6' ? 'selected' : '' }}>
                                            Juni
                                        </option>
                                        <option value="7" {{ old('detail_bulan') == '7' ? 'selected' : '' }}>
                                            Juli
                                        </option>
                                        <option value="8" {{ old('detail_bulan') == '8' ? 'selected' : '' }}>
                                            Agustus
                                        </option>
                                        <option value="9" {{ old('detail_bulan') == '9' ? 'selected' : '' }}>
                                            September
                                        </option>
                                        <option value="10" {{ old('detail_bulan') == '10' ? 'selected' : '' }}>
                                            Oktober
                                        </option>
                                        <option value="11" {{ old('detail_bulan') == '11' ? 'selected' : '' }}>
                                            November
                                        </option>
                                        <option value="12" {{ old('detail_bulan') == '12' ? 'selected' : '' }}>
                                            Desember
                                        </option>
                                    </select>
                                </div>
                                <div class="col-4 mb-2">
                                    <input type="number" id="detail_tahun" name="detail_tahun"
                                        class="form-control rounded-0 @error('detail_tahun') is-invalid @enderror"
                                        value="{{ old('detail_tahun') }}" placeholder="Tahun">
                                    @error('detail_tahun')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control rounded-0" id="alamat" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="detail_kode_pos" class="form-label">Kode POS *</label>
                            <input type="text" id="detail_kode_pos" name="detail_kode_pos"
                                class="form-control rounded-0 @error('detail_kode_pos') is-invalid @enderror"
                                value="{{ old('detail_kode_pos') }}">
                            @error('detail_kode_pos')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="detail_no_npwp" class="form-label">No. NPWP *</label>
                            <input type="text" id="detail_no_npwp" name="detail_no_npwp"
                                class="form-control rounded-0 @error('detail_no_npwp') is-invalid @enderror"
                                value="{{ old('detail_no_npwp') }}">
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
                                value="{{ old('detail_nama_ibu') }}">
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
                                value="{{ old('detail_tinggal_bersama') }}">
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
                            <label for="detail_pasangan_nama"
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
                            <input type="text" id="detail_pasangan" name="detail_pasangan"
                                class="form-control rounded-0 @error('detail_pasangan') is-invalid @enderror"
                                value="{{ old('detail_pasangan') }}">
                            @error('detail_pasangan')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="detail_pasangan_pekerjaan"
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
                            <input type="text" id="detail_pasangan_pekerjaan" name="detail_pasangan_pekerjaan"
                                class="form-control rounded-0 @error('detail_pasangan_pekerjaan') is-invalid @enderror"
                                value="{{ old('detail_pasangan_pekerjaan') }}">
                            @error('detail_pasangan_pekerjaan')
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
