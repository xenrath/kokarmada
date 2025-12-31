@extends('layout.app')

@section('title', 'Data Simpanan')

@section('content')
    <!-- Start Content-->
    <div class="container-fluid">
        <!-- start page title -->
        <div class="page-title-box">
            <h4 class="page-title">Data Simpanan</h4>
        </div>
        <!-- end page title -->
        <div class="card mb-2 rounded-0">
            <div class="card-body pb-0">
                <div class="row mb-2">
                    <div class="col-xl-8">
                        <form action="{{ url('admin/anggota') }}" method="get" autocomplete="off" id="form-filter"
                            class="row gy-2 gx-2 align-items-center justify-content-xl-start justify-content-between">
                            <div class="col-auto">
                                <div class="d-flex align-items-center">
                                    <select class="form-select rounded-0" name="spesial" id="spesial"
                                        onchange="$('#form-filter').submit();">
                                        <option value="">- Pilih Role -</option>
                                        <option value="ketua" {{ request()->get('spesial') == 'ketua' ? 'selected' : '' }}>
                                            Ketua
                                        </option>
                                        <option value="sekretaris"
                                            {{ request()->get('spesial') == 'sekretaris' ? 'selected' : '' }}>
                                            Sekretaris
                                        </option>
                                        <option value="bendahara"
                                            {{ request()->get('spesial') == 'bendahara' ? 'selected' : '' }}>
                                            Bendahara
                                        </option>
                                        <option value="manajer"
                                            {{ request()->get('spesial') == 'manajer' ? 'selected' : '' }}>
                                            Manajer Analis
                                        </option>
                                        <option value="petugas"
                                            {{ request()->get('spesial') == 'petugas' ? 'selected' : '' }}>
                                            Petugas
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-auto">
                                <label for="keyword" class="visually-hidden">Cari</label>
                                <div class="input-group">
                                    <input type="search" class="form-control rounded-0" id="keyword" name="keyword"
                                        placeholder="Cari..." value="{{ request()->get('keyword') }}">
                                    <button class="btn btn-primary rounded-0" type="submit">
                                        <i class="mdi mdi-magnify"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-xl-4">
                        <div class="text-xl-end mt-xl-0 mt-2">
                            <a href="{{ url('admin/anggota/create') }}" class="btn btn-primary rounded-0 mb-2">
                                <i class="mdi mdi-account-plus me-1"></i>
                                Tambah Anggota
                            </a>
                            {{-- <button type="button" class="btn btn-light rounded-0 mb-2">Export</button> --}}
                        </div>
                    </div><!-- end col-->
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-centered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 10px">No</th>
                                <th>Nama</th>
                                <th>No. HP / WhatsApp</th>
                                <th>Sebagai</th>
                                <th style="width: 120px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $user->nama }}
                                        @if ($user->status == 'nonaktif')
                                            <span class="badge badge-danger-lighten rounded-pill">
                                                <i class="mdi mdi-cancel"></i>
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $user->telp }}</td>
                                    <td>
                                        <h5>
                                            @if ($user->spesial != 'normal')
                                                <span class="badge badge-secondary-lighten rounded-0">
                                                    {{ ucfirst($user->spesial) }}
                                                </span>
                                            @else
                                                <span class="badge badge-info-lighten rounded-0">
                                                    Anggota
                                                </span>
                                            @endif
                                        </h5>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ url('admin/anggota/' . $user->id) }}" class="action-icon">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                        <a href="{{ url('admin/anggota/' . $user->id . '/edit') }}" class="action-icon">
                                            <i class="mdi mdi-square-edit-outline"></i>
                                        </a>
                                        @if ($user->status == 'aktif')
                                            <a href="#" class="action-icon" data-bs-toggle="modal"
                                                data-bs-target="#modal-nonaktif-{{ $user->id }}">
                                                <i class="mdi mdi-cancel"></i>
                                            </a>
                                        @else
                                            <a href="#" class="action-icon" data-bs-toggle="modal"
                                                data-bs-target="#modal-aktif-{{ $user->id }}">
                                                <i class="mdi mdi-check-circle"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">
                                        <span class="text-muted">- Data tidak ditemukan -</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- container -->
@endsection
