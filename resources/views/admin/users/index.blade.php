@extends('layout.app')

@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">

                <div class="page-title-box d-flex align-items-center">

                    <h4 class="page-title mb-0">
                        Pengguna
                    </h4>

                </div>

            </div>
        </div>

        <div class="card rounded-0">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <h4 class="header-title mb-0">
                        Daftar Pengguna
                    </h4>

                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary rounded-0">
                        <i class="mdi mdi-plus me-1"></i>
                        Tambah Pengguna
                    </a>

                </div>

            </div>

            <div class="card-body border-top">

                <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4">

                    <div class="row g-2">

                        <div class="col-md-5">
                            <input type="text" name="keyword" class="form-control rounded-0" value="{{ $keyword }}"
                                placeholder="Cari nama atau nomor HP">
                        </div>

                        <div class="col-md-3">
                            <select name="role" class="form-select rounded-0">

                                <option value="">
                                    Semua Role
                                </option>

                                <option value="member" {{ $role === 'member' ? 'selected' : '' }}>
                                    Member
                                </option>

                                <option value="analyst_manager" {{ $role === 'analyst_manager' ? 'selected' : '' }}>
                                    Analyst Manager
                                </option>

                                <option value="treasurer" {{ $role === 'treasurer' ? 'selected' : '' }}>
                                    Treasurer
                                </option>

                                <option value="chairman" {{ $role === 'chairman' ? 'selected' : '' }}>
                                    Chairman
                                </option>

                                <option value="secretary" {{ $role === 'secretary' ? 'selected' : '' }}>
                                    Secretary
                                </option>

                                <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>
                                    Admin
                                </option>

                                <option value="supervisor" {{ $role === 'supervisor' ? 'selected' : '' }}>
                                    Supervisor
                                </option>

                            </select>
                        </div>

                        <div class="col-md-2">
                            <select name="status" class="form-select rounded-0">

                                <option value="">
                                    Semua Status
                                </option>

                                <option value="active" {{ $status === 'active' ? 'selected' : '' }}>
                                    Aktif
                                </option>

                                <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>
                                    Nonaktif
                                </option>

                            </select>
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-light rounded-0 w-100">
                                <i class="mdi mdi-magnify me-1"></i>
                                Cari
                            </button>
                        </div>

                    </div>

                </form>

                <div class="table-responsive">

                    <table class="table table-centered table-nowrap mb-0">

                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Nomor HP</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($users as $user)
                                <tr>

                                    <td>
                                        <strong>
                                            {{ $user->name }}
                                        </strong>
                                    </td>

                                    <td>
                                        {{ $user->phone }}
                                    </td>

                                    <td>
                                        @if ($user->role === 'member')
                                            Member
                                        @elseif($user->role === 'analyst_manager')
                                            Analyst Manager
                                        @elseif($user->role === 'treasurer')
                                            Treasurer
                                        @elseif($user->role === 'chairman')
                                            Chairman
                                        @elseif($user->role === 'secretary')
                                            Secretary
                                        @elseif($user->role === 'admin')
                                            Admin
                                        @elseif($user->role === 'supervisor')
                                            Supervisor
                                        @else
                                            {{ $user->role }}
                                        @endif
                                    </td>

                                    <td>

                                        @if ($user->status === 'active')
                                            <span class="badge bg-success rounded-0">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="badge bg-secondary rounded-0">
                                                Nonaktif
                                            </span>
                                        @endif

                                    </td>

                                    <td class="text-end">

                                        <a href="{{ route('admin.users.edit', $user) }}"
                                            class="btn btn-light btn-sm rounded-0">
                                            <i class="mdi mdi-pencil-outline"></i>
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}"
                                            class="d-inline">

                                            @csrf
                                            @method('PATCH')

                                            @if ($user->status === 'active')
                                                <button type="submit" class="btn btn-light btn-sm rounded-0">
                                                    <i class="mdi mdi-account-off-outline"></i>
                                                    Nonaktifkan
                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-light btn-sm rounded-0">
                                                    <i class="mdi mdi-account-check-outline"></i>
                                                    Aktifkan
                                                </button>
                                            @endif

                                        </form>

                                        <button type="button" class="btn btn-light btn-sm rounded-0" data-bs-toggle="modal"
                                            data-bs-target="#resetPasswordModal{{ $user->id }}">
                                            <i class="mdi mdi-lock-reset"></i>
                                            Reset Password
                                        </button>

                                    </td>

                                </tr>

                                {{-- RESET PASSWORD MODAL --}}
                                <div class="modal fade" id="resetPasswordModal{{ $user->id }}" tabindex="-1"
                                    aria-hidden="true">

                                    <div class="modal-dialog">

                                        <div class="modal-content rounded-0">

                                            <div class="modal-header rounded-0">

                                                <h5 class="modal-title">
                                                    Reset Password
                                                </h5>

                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close">
                                                </button>

                                            </div>

                                            <div class="modal-body">

                                                <p class="mb-0">
                                                    Apakah Anda yakin ingin mereset password
                                                    <strong>{{ $user->name }}</strong>?
                                                </p>

                                                <div class="alert alert-warning rounded-0 mt-3 mb-0">
                                                    Password setelah reset akan menjadi:
                                                    <strong>password123</strong>
                                                </div>

                                            </div>

                                            <div class="modal-footer">

                                                <button type="button" class="btn btn-light rounded-0"
                                                    data-bs-dismiss="modal">
                                                    Batal
                                                </button>

                                                <form method="POST"
                                                    action="{{ route('admin.users.reset-password', $user) }}">

                                                    @csrf
                                                    @method('PATCH')

                                                    <button type="submit" class="btn btn-primary rounded-0">
                                                        Reset Password
                                                    </button>

                                                </form>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            @empty

                                <tr>

                                    <td colspan="5" class="text-center text-muted py-4">
                                        Belum ada pengguna.
                                    </td>

                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>
@endsection
