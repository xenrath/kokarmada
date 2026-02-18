@extends('layout.app')

@section('title', 'Data Rekening')

@section('content')
    <!-- Start Content-->
    <div class="container-fluid">
        <!-- start page title -->
        <div class="page-title-box">
            <h4 class="page-title">Data Rekening</h4>
        </div>
        <!-- end page title -->
        <div class="card mb-2 rounded-0">
            <div class="card-body text-end">
                <a href="{{ url('anggota/bendahara/keuangan-rekening/create') }}" class="btn btn-primary rounded-0">
                    <i class="mdi mdi-plus-circle"></i>
                    Tambah Rekening
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-centered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 10px">No</th>
                                <th>Nama Rekening</th>
                                <th>Nomor</th>
                                <th>Status</th>
                                <th style="width: 40px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rekenings as $rekening)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>
                                        @if ($rekening->jenis == 'bank')
                                            Bank {{ $rekening->bank }}
                                        @else
                                            {{ $rekening->nama }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($rekening->jenis == 'bank')
                                            {{ $rekening->nomor }}
                                            <br>
                                            <small>{{ $rekening->nama }}</small>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($rekening->is_aktif)
                                            <span class="badge bg-primary rounded-0">Aktif</span>
                                        @else
                                            <span class="badge bg-danger rounded-0">Non-Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($rekening->is_aktif)
                                            <a href="#" class="action-icon" data-bs-toggle="modal"
                                                data-bs-target="#modal-nonaktif-{{ $rekening->id }}">
                                                <i class="mdi mdi-cancel"></i>
                                            </a>
                                        @else
                                            <a href="#" class="action-icon" data-bs-toggle="modal"
                                                data-bs-target="#modal-aktif-{{ $rekening->id }}">
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

    @foreach ($rekenings as $rekening)
        @if ($rekening->is_aktif)
            <div id="modal-nonaktif-{{ $rekening->id }}" class="modal fade" tabindex="-1" role="dialog"
                aria-labelledby="modal-nonaktif-label-{{ $rekening->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content rounded-0">
                        <div class="modal-header">
                            <h4 class="modal-title" id="modal-nonaktif-label-{{ $rekening->id }}">Status Anggota</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                        </div>
                        <div class="modal-body">
                            <p>
                                Yakin nonaktifkan rekening
                                @if ($rekening->jenis == 'bank')
                                    dari <strong>Bank {{ $rekening->bank }}</strong>
                                    atas nama <strong>{{ $rekening->nama }}</strong>?
                                @else
                                    dengan nama rekening <strong>{{ $rekening->nama }}</strong>?
                                @endif
                            </p>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-light rounded-0" data-bs-dismiss="modal">Batal</button>
                            <form action="{{ url('anggota/bendahara/keuangan-rekening/' . $rekening->id) }}" method="POST"
                                id="form-hapus-{{ $rekening->id }}">
                                @csrf
                                @method('delete')
                                <button type="button" class="btn btn-danger rounded-0" id="btn-hapus-{{ $rekening->id }}"
                                    onclick="form_hapus({{ $rekening->id }})">
                                    <span id="btn-hapus-text-{{ $rekening->id }}">Nonaktifkan</span>
                                    <span id="btn-hapus-load-{{ $rekening->id }}" style="display: none;">
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
            <div id="modal-aktif-{{ $rekening->id }}" class="modal fade" tabindex="-1" role="dialog"
                aria-labelledby="modal-aktif-label-{{ $rekening->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content rounded-0">
                        <div class="modal-header">
                            <h4 class="modal-title" id="modal-aktif-label-{{ $rekening->id }}">Status Anggota</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                        </div>
                        <div class="modal-body">
                            <p>
                                Yakin aktifkan rekening
                                @if ($rekening->jenis == 'bank')
                                    dari  <strong>Bank {{ $rekening->bank }}</strong>
                                    atas nama <strong>{{ $rekening->nama }}</strong>?
                                @else
                                    dengan nama rekening <strong>{{ $rekening->nama }}</strong>?
                                @endif
                            </p>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-light rounded-0" data-bs-dismiss="modal">Batal</button>
                            <form action="{{ url('anggota/bendahara/keuangan-rekening/' . $rekening->id) }}" method="POST"
                                id="form-hapus-{{ $rekening->id }}">
                                @csrf
                                @method('delete')
                                <button type="button" class="btn btn-primary rounded-0" id="btn-hapus-{{ $rekening->id }}"
                                    onclick="form_hapus({{ $rekening->id }})">
                                    <span id="btn-hapus-text-{{ $rekening->id }}">Aktifkan</span>
                                    <span id="btn-hapus-load-{{ $rekening->id }}" style="display: none;">
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
        function form_hapus(id) {
            $('#btn-hapus-' + id).prop('disabled', true);
            $('#btn-hapus-text-' + id).hide();
            $('#btn-hapus-load-' + id).show();
            $('#form-hapus-' + id).submit();
        }
    </script>
@endsection
