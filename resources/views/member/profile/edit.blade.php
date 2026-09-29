@extends('layout.app')

@section('content')
    <div class="container-fluid">

        <div class="page-title-box d-flex align-items-center">
            <a href="{{ route('member.profile.index') }}" class="btn btn-light rounded-0 me-3">
                <i class="mdi mdi-arrow-left"></i>
            </a>

            <h4 class="page-title mb-0">Edit Profil</h4>
        </div>

        <form action="{{ route('member.profile.update') }}" method="POST" enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <div class="row">

                <div class="col-lg-6">
                    <div class="card rounded-0">

                        <div class="card-body">
                            <h4 class="header-title">
                                Data Pribadi
                            </h4>
                        </div>

                        <div class="card-body border-top">

                            <div class="mb-3">
                                <label class="form-label">
                                    Nomor Anggota
                                </label>

                                <input type="text" class="form-control rounded-0"
                                    value="{{ $user->memberProfile?->member_number ?? 'Akan dibuat otomatis' }}" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>

                                <input type="text" name="name"
                                    class="form-control rounded-0 @error('name') is-invalid @enderror"
                                    value="{{ old('name', $user->name) }}">

                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Nomor HP / WhatsApp <span class="text-danger">*</span>
                                </label>

                                <input type="text" name="phone"
                                    class="form-control rounded-0 @error('phone') is-invalid @enderror"
                                    value="{{ old('phone', $user->phone) }}">

                                @error('phone')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Jenis Kelamin <span class="text-danger">*</span>
                                </label>

                                <select name="gender" class="form-select rounded-0 @error('gender') is-invalid @enderror">

                                    <option value="">
                                        Pilih jenis kelamin
                                    </option>

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

                            <div class="mb-3">
                                <label class="form-label">
                                    NIK
                                </label>

                                <input type="text" name="national_id"
                                    class="form-control rounded-0 @error('national_id') is-invalid @enderror"
                                    value="{{ old('national_id', $user->memberProfile?->national_id) }}">

                                @error('national_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Masa Berlaku KTP
                                </label>

                                <input type="date" name="national_id_expiry"
                                    class="form-control rounded-0 @error('national_id_expiry') is-invalid @enderror"
                                    value="{{ old('national_id_expiry', optional($user->memberProfile?->national_id_expiry)->format('Y-m-d')) }}">

                                @error('national_id_expiry')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Tempat Lahir
                                </label>

                                <input type="text" name="birth_place" class="form-control rounded-0"
                                    value="{{ old('birth_place', $user->memberProfile?->birth_place) }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Tanggal Lahir
                                </label>

                                <input type="date" name="birth_date" class="form-control rounded-0"
                                    value="{{ old('birth_date', optional($user->memberProfile?->birth_date)->format('Y-m-d')) }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Alamat
                                </label>

                                <textarea name="address" rows="4" class="form-control rounded-0">{{ old('address', $user->memberProfile?->address) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Kode Pos
                                </label>

                                <input type="text" name="postal_code" class="form-control rounded-0"
                                    value="{{ old('postal_code', $user->memberProfile?->postal_code) }}">
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-lg-6">

                    <div class="card rounded-0">
                        <div class="card-body">
                            <h4 class="header-title">
                                Data Pekerjaan & Keluarga
                            </h4>
                        </div>

                        <div class="card-body border-top">

                            <div class="mb-3">
                                <label class="form-label">
                                    Pekerjaan
                                </label>

                                <input type="text" name="occupation" class="form-control rounded-0"
                                    value="{{ old('occupation', $user->memberProfile?->occupation) }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    NPWP
                                </label>

                                <input type="text" name="tax_id" class="form-control rounded-0"
                                    value="{{ old('tax_id', $user->memberProfile?->tax_id) }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Nama Ibu Kandung
                                </label>

                                <input type="text" name="mother_name" class="form-control rounded-0"
                                    value="{{ old('mother_name', $user->memberProfile?->mother_name) }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Status Pernikahan
                                </label>

                                <select name="marital_status" class="form-select rounded-0">

                                    <option value="">
                                        Pilih status
                                    </option>

                                    <option value="belum_menikah"
                                        {{ old('marital_status', $user->memberProfile?->marital_status) == 'belum_menikah' ? 'selected' : '' }}>
                                        Belum Menikah
                                    </option>

                                    <option value="menikah"
                                        {{ old('marital_status', $user->memberProfile?->marital_status) == 'menikah' ? 'selected' : '' }}>
                                        Menikah
                                    </option>

                                    <option value="cerai_hidup"
                                        {{ old('marital_status', $user->memberProfile?->marital_status) == 'cerai_hidup' ? 'selected' : '' }}>
                                        Cerai Hidup
                                    </option>

                                    <option value="cerai_mati"
                                        {{ old('marital_status', $user->memberProfile?->marital_status) == 'cerai_mati' ? 'selected' : '' }}>
                                        Cerai Mati
                                    </option>

                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Nama Pasangan
                                </label>

                                <input type="text" name="spouse_name" class="form-control rounded-0"
                                    value="{{ old('spouse_name', $user->memberProfile?->spouse_name) }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Pekerjaan Pasangan
                                </label>

                                <input type="text" name="spouse_occupation" class="form-control rounded-0"
                                    value="{{ old('spouse_occupation', $user->memberProfile?->spouse_occupation) }}">
                            </div>

                        </div>
                    </div>

                    <div class="card rounded-0">
                        <div class="card-body">
                            <h4 class="header-title">
                                Data Rekening
                            </h4>
                        </div>

                        <div class="card-body border-top">

                            <div class="mb-3">
                                <label class="form-label">
                                    Nama Bank
                                </label>

                                <input type="text" name="bank_name" class="form-control rounded-0"
                                    value="{{ old('bank_name', $user->memberProfile?->bank_name) }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Nomor Rekening
                                </label>

                                <input type="text" name="bank_account_number" class="form-control rounded-0"
                                    value="{{ old('bank_account_number', $user->memberProfile?->bank_account_number) }}">
                            </div>

                        </div>
                    </div>

                    <div class="card rounded-0">
                        <div class="card-body">
                            <h4 class="header-title">
                                Dokumen Profil
                            </h4>
                        </div>

                        <div class="card-body border-top">

                            <div class="mb-3">
                                <label class="form-label">
                                    Foto Profil
                                </label>

                                <input type="file" name="photo_file"
                                    class="form-control rounded-0 @error('photo_file') is-invalid @enderror"
                                    accept=".jpg,.jpeg,.png">

                                @error('photo_file')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    File KTP
                                </label>

                                <input type="file" name="national_id_file"
                                    class="form-control rounded-0 @error('national_id_file') is-invalid @enderror"
                                    accept=".pdf,.jpg,.jpeg,.png">

                                @error('national_id_file')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    File KK
                                </label>

                                <input type="file" name="family_card_file"
                                    class="form-control rounded-0 @error('family_card_file') is-invalid @enderror"
                                    accept=".pdf,.jpg,.jpeg,.png">

                                @error('family_card_file')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                        </div>
                    </div>

                </div>
            </div>

            <div class="card rounded-0">
                <div class="card-body text-end">
                    <a href="{{ route('member.profile.index') }}" class="btn btn-light rounded-0 me-2">
                        Batal
                    </a>

                    <button type="submit" class="btn btn-primary rounded-0">
                        <i class="mdi mdi-content-save me-1"></i>
                        Simpan Profil
                    </button>
                </div>
            </div>

        </form>

    </div>
@endsection
