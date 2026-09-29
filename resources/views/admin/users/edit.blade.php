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
                        Edit Pengguna
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

                <form method="POST" action="{{ route('admin.users.update', $user) }}">

                    @csrf
                    @method('PUT')

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label for="name" class="form-label">
                                Nama Lengkap
                            </label>

                            <input type="text" id="name" name="name"
                                class="form-control rounded-0 @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}">

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
                                value="{{ old('phone', $user->phone) }}">

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

                        <div class="col-md-6 mb-3">

                            <label for="role" class="form-label">
                                Role
                            </label>

                            <select id="role" name="role"
                                class="form-select rounded-0 @error('role') is-invalid @enderror">

                                <option value="member" {{ old('role', $user->role) == 'member' ? 'selected' : '' }}>
                                    Member
                                </option>

                                <option value="analyst_manager"
                                    {{ old('role', $user->role) == 'analyst_manager' ? 'selected' : '' }}>
                                    Analyst Manager
                                </option>

                                <option value="treasurer" {{ old('role', $user->role) == 'treasurer' ? 'selected' : '' }}>
                                    Treasurer
                                </option>

                                <option value="chairman" {{ old('role', $user->role) == 'chairman' ? 'selected' : '' }}>
                                    Chairman
                                </option>

                                <option value="secretary" {{ old('role', $user->role) == 'secretary' ? 'selected' : '' }}>
                                    Secretary
                                </option>

                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                                    Admin
                                </option>

                                <option value="supervisor"
                                    {{ old('role', $user->role) == 'supervisor' ? 'selected' : '' }}>
                                    Supervisor
                                </option>

                            </select>

                            @error('role')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>

                    <div class="border-top pt-3 mt-2">

                        <button type="submit" class="btn btn-primary rounded-0">
                            <i class="mdi mdi-content-save me-1"></i>
                            Simpan Perubahan
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
