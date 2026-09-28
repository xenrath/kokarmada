@extends('layout.app')

@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">

                <div class="page-title-box d-flex align-items-center">

                    <a href="{{ route('admin.users.index') }}" class="btn btn-light rounded-0 me-3">
                        <i class="mdi mdi-arrow-left"></i>
                    </a>

                    <h4 class="page-title mb-0">
                        Tambah Pengguna
                    </h4>

                </div>

            </div>
        </div>

        <div class="card rounded-0">

            <div class="card-body">
                <h4 class="header-title">
                    Data Pengguna
                </h4>
            </div>

            <div class="card-body border-top">

                <form method="POST" action="{{ route('admin.users.store') }}">

                    @csrf

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label for="name" class="form-label">
                                Nama Lengkap
                            </label>

                            <input type="text" id="name" name="name"
                                class="form-control rounded-0 @error('name') is-invalid @enderror"
                                value="{{ old('name') }}">

                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        <div class="col-md-6 mb-3">

                            <label for="phone" class="form-label">
                                Nomor HP
                            </label>

                            <input type="text" id="phone" name="phone"
                                class="form-control rounded-0 @error('phone') is-invalid @enderror"
                                value="{{ old('phone') }}">

                            @error('phone')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        <div class="col-md-6 mb-3">

                            <label for="gender" class="form-label">
                                Jenis Kelamin
                            </label>

                            <select id="gender" name="gender"
                                class="form-select rounded-0 @error('gender') is-invalid @enderror">

                                <option value="">
                                    Pilih Jenis Kelamin
                                </option>

                                <option value="L" @selected(old('gender') === 'L')}>
                                    Laki-laki
                                </option>

                                <option value="P" @selected(old('gender') === 'P')}>
                                    Perempuan
                                </option>

                            </select>

                            @error('gender')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        <div class="col-md-6 mb-3">

                            <label for="role" class="form-label">
                                Role
                            </label>

                            <select id="role" name="role"
                                class="form-select rounded-0 @error('role') is-invalid @enderror">

                                <option value="">
                                    Pilih Role
                                </option>

                                <option value="member" @selected(old('role') === 'member')}>
                                    Member
                                </option>

                                <option value="analyst_manager" @selected(old('role') === 'analyst_manager')}>
                                    Analyst Manager
                                </option>

                                <option value="treasurer" @selected(old('role') === 'treasurer')}>
                                    Treasurer
                                </option>

                                <option value="chairman" @selected(old('role') === 'chairman')}>
                                    Chairman
                                </option>

                                <option value="secretary" @selected(old('role') === 'secretary')}>
                                    Secretary
                                </option>

                                <option value="admin" @selected(old('role') === 'admin')}>
                                    Admin
                                </option>

                                <option value="supervisor" @selected(old('role') === 'supervisor')}>
                                    Supervisor
                                </option>

                            </select>

                            @error('role')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        <div class="col-md-6 mb-3">

                            <label for="password" class="form-label">
                                Password
                            </label>

                            <input type="password" id="password" name="password"
                                class="form-control rounded-0 @error('password') is-invalid @enderror">

                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        <div class="col-md-6 mb-3">

                            <label for="password_confirmation" class="form-label">
                                Konfirmasi Password
                            </label>

                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-control rounded-0">

                        </div>

                    </div>

                    <div class="border-top pt-3 mt-2">

                        <button type="submit" class="btn btn-primary rounded-0">
                            <i class="mdi mdi-content-save me-1"></i>
                            Simpan
                        </button>

                        <a href="{{ route('admin.users.index') }}" class="btn btn-light rounded-0 ms-1">
                            Batal
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>
@endsection
