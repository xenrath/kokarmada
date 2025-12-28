@extends('layout.app')

@section('title', 'Data Pengguna')

@section('content')
    <!-- Start Content-->
    <div class="container-fluid">
        <!-- start page title -->
        <div class="page-title-box">
            <h4 class="page-title">Data Pengguna</h4>
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
                                        <option value="manajer" {{ request()->get('spesial') == 'manajer' ? 'selected' : '' }}>
                                            Manajer Analis
                                        </option>
                                        <option value="petugas" {{ request()->get('spesial') == 'petugas' ? 'selected' : '' }}>
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
                            <a href="{{ url('admin/anggota/create') }}" class="btn btn-primary rounded-0 mb-2 me-2">
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
                                <th>Email</th>
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
                                    <td>{{ $user->email }}</td>
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

    @foreach ($users as $user)
        @if ($user->status == 'aktif')
            <div id="modal-nonaktif-{{ $user->id }}" class="modal fade" tabindex="-1" role="dialog"
                aria-labelledby="modal-nonaktif-label-{{ $user->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content rounded-0">
                        <div class="modal-header">
                            <h4 class="modal-title" id="modal-nonaktif-label-{{ $user->id }}">Status Pengguna</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                        </div>
                        <div class="modal-body">
                            <p>Yakin nonaktifkan pengguna dengan nama <strong>{{ $user->nama }}</strong>?</p>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-light rounded-0" data-bs-dismiss="modal">Batal</button>
                            <form action="{{ url('admin/anggota/' . $user->id) }}" method="POST"
                                id="form-hapus-{{ $user->id }}">
                                @csrf
                                @method('delete')
                                <button type="button" class="btn btn-danger rounded-0" id="btn-hapus-{{ $user->id }}"
                                    onclick="form_hapus({{ $user->id }})">
                                    <span id="btn-hapus-text-{{ $user->id }}">Nonaktifkan</span>
                                    <span id="btn-hapus-load-{{ $user->id }}" style="display: none;">
                                        <i class="mdi mdi-spin mdi-loading"></i>
                                        Memproses
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->
        @else
            <div id="modal-aktif-{{ $user->id }}" class="modal fade" tabindex="-1" role="dialog"
                aria-labelledby="modal-aktif-label-{{ $user->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content rounded-0">
                        <div class="modal-header">
                            <h4 class="modal-title" id="modal-aktif-label-{{ $user->id }}">Status Pengguna</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-hidden="true"></button>
                        </div>
                        <div class="modal-body">
                            <p>Yakin aktifkan pengguna dengan nama <strong>{{ $user->nama }}</strong>?</p>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-light rounded-0" data-bs-dismiss="modal">Batal</button>
                            <form action="{{ url('admin/anggota/' . $user->id) }}" method="POST"
                                id="form-hapus-{{ $user->id }}">
                                @csrf
                                @method('delete')
                                <button type="button" class="btn btn-primary rounded-0"
                                    id="btn-hapus-{{ $user->id }}" onclick="form_hapus({{ $user->id }})">
                                    <span id="btn-hapus-text-{{ $user->id }}">Aktifkan</span>
                                    <span id="btn-hapus-load-{{ $user->id }}" style="display: none;">
                                        <i class="mdi mdi-spin mdi-loading"></i>
                                        Memproses
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->
        @endif
    @endforeach
@endsection

@section('script')
    <script>
        $('#keyword').on('search', function() {
            $('#form-filter').submit();
        });

        function form_hapus(id) {
            $('#btn-hapus-' + id).prop('disabled', true);
            $('#btn-hapus-text-' + id).hide();
            $('#btn-hapus-load-' + id).show();
            $('#form-hapus-' + id).submit();
        }
    </script>
@endsection
