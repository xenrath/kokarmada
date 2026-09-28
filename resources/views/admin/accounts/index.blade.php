@extends('layout.app')

@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">

                <div class="page-title-box d-flex align-items-center">

                    <h4 class="page-title mb-0">
                        Rekening
                    </h4>

                </div>

            </div>
        </div>

        <div class="card rounded-0">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <h4 class="header-title mb-0">
                        Daftar Rekening
                    </h4>

                    <a href="{{ route('admin.accounts.create') }}" class="btn btn-primary rounded-0">
                        <i class="mdi mdi-plus me-1"></i>
                        Tambah Rekening
                    </a>

                </div>

            </div>

            <div class="card-body border-top">

                <div class="table-responsive">

                    <table class="table table-centered table-nowrap mb-0">

                        <thead>
                            <tr>
                                <th>Nama Rekening</th>
                                <th>Bank</th>
                                <th>Nomor Rekening</th>
                                <th>Atas Nama</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($accounts as $account)
                                <tr>

                                    <td>
                                        <strong>
                                            {{ $account->name }}
                                        </strong>
                                    </td>

                                    <td>
                                        {{ $account->bank_name ?: '-' }}
                                    </td>

                                    <td>
                                        {{ $account->account_number ?: '-' }}
                                    </td>

                                    <td>
                                        {{ $account->account_name ?: '-' }}
                                    </td>

                                    <td>

                                        @if ($account->is_active)
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

                                        <a href="{{ route('admin.accounts.edit', $account) }}"
                                            class="btn btn-light btn-sm rounded-0">
                                            <i class="mdi mdi-pencil-outline"></i>
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('admin.accounts.toggle-status', $account) }}"
                                            class="d-inline">

                                            @csrf
                                            @method('PATCH')

                                            @if ($account->is_active)
                                                <button type="submit" class="btn btn-light btn-sm rounded-0">
                                                    <i class="mdi mdi-toggle-switch-off-outline"></i>
                                                    Nonaktifkan
                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-light btn-sm rounded-0">
                                                    <i class="mdi mdi-toggle-switch-outline"></i>
                                                    Aktifkan
                                                </button>
                                            @endif

                                        </form>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Belum ada rekening.
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
