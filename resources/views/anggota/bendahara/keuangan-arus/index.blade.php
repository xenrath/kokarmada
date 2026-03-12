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

        <div class="row mb-2">
            <div class="col-md-4">
                <div class="card border-primary border tilebox-one rounded-0 mb-2">
                    <div class="card-body">
                        <i class="uil uil-money-insert"></i>
                        <h6 class="text-uppercase mt-0">Pemasukan</h6>
                        <h2 class="my-2" id="active-users-count">Rp10.000.000</h2>
                        <div class="text-center">
                            <a href="{{ url('anggota/bendahara/keuangan-arus/pemasukan/create') }}"
                                class="btn btn-primary btn-sm rounded-0">Buat Pemasukan</a>
                        </div>
                    </div>
                    <!-- end card-body-->
                </div>
                <!--end card-->
            </div>
            <div class="col-md-4">
                <div class="card border-danger border tilebox-one rounded-0 mb-2">
                    <div class="card-body">
                        <i class='uil uil-money-withdraw float-end'></i>
                        <h6 class="text-uppercase mt-0">Pengeluaran</h6>
                        <h2 class="my-2" id="active-users-count">Rp1.000.000</h2>
                        <div class="text-center">
                            <a href="{{ url('anggota/bendahara/keuangan-arus/pengeluaran/create') }}"
                                class="btn btn-danger btn-sm rounded-0">Buat Pemasukan</a>
                        </div>
                    </div>
                    <!-- end card-body-->
                </div>
                <!--end card-->
            </div>
            <div class="col-md-4">
                <div class="card border-secondary border tilebox-one rounded-0 mb-2">
                    <div class="card-body">
                        <i class='uil uil-wallet float-end'></i>
                        <h6 class="text-uppercase mt-0">Saldo Akhir</h6>
                        <h2 class="my-2" id="active-users-count">Rp90.000.000.000</h2>
                    </div>
                    <!-- end card-body-->
                </div>
                <!--end card-->
            </div>
        </div>

        <div class="card mb-2 rounded-0">
            <div class="card-body">
                <h4 class="header-title">Arus Kas</h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-centered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 10px">No</th>
                                <th>Rekening</th>
                                <th>Jenis</th>
                                <th>Pengadaan</th>
                                <th>Jumlah</th>
                                <th style="width: 40px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($aruses as $arus)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>
                                        -
                                    </td>
                                    <td>
                                        -
                                    </td>
                                    <td>
                                        -
                                    </td>
                                    <td>
                                        -
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">
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

    {{-- @foreach ($aruss as $arus)
        @if ($arus->is_aktif)
            <div id="modal-nonaktif-{{ $arus->id }}" class="modal fade" tabindex="-1" role="dialog"
                aria-labelledby="modal-nonaktif-label-{{ $arus->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content rounded-0">
                        <div class="modal-header">
                            <h4 class="modal-title" id="modal-nonaktif-label-{{ $arus->id }}">Status Anggota</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                        </div>
                        <div class="modal-body">
                            <p>
                                Yakin nonaktifkan arus
                                @if ($arus->jenis == 'bank')
                                    dari <strong>Bank {{ $arus->bank }}</strong>
                                    atas nama <strong>{{ $arus->nama }}</strong>?
                                @else
                                    dengan nama arus <strong>{{ $arus->nama }}</strong>?
                                @endif
                            </p>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-light rounded-0" data-bs-dismiss="modal">Batal</button>
                            <form action="{{ url('anggota/bendahara/keuangan-arus/' . $arus->id) }}" method="POST"
                                id="form-hapus-{{ $arus->id }}">
                                @csrf
                                @method('delete')
                                <button type="button" class="btn btn-danger rounded-0" id="btn-hapus-{{ $arus->id }}"
                                    onclick="form_hapus({{ $arus->id }})">
                                    <span id="btn-hapus-text-{{ $arus->id }}">Nonaktifkan</span>
                                    <span id="btn-hapus-load-{{ $arus->id }}" style="display: none;">
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
            <div id="modal-aktif-{{ $arus->id }}" class="modal fade" tabindex="-1" role="dialog"
                aria-labelledby="modal-aktif-label-{{ $arus->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content rounded-0">
                        <div class="modal-header">
                            <h4 class="modal-title" id="modal-aktif-label-{{ $arus->id }}">Status Anggota</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                        </div>
                        <div class="modal-body">
                            <p>
                                Yakin aktifkan arus
                                @if ($arus->jenis == 'bank')
                                    dari  <strong>Bank {{ $arus->bank }}</strong>
                                    atas nama <strong>{{ $arus->nama }}</strong>?
                                @else
                                    dengan nama arus <strong>{{ $arus->nama }}</strong>?
                                @endif
                            </p>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-light rounded-0" data-bs-dismiss="modal">Batal</button>
                            <form action="{{ url('anggota/bendahara/keuangan-arus/' . $arus->id) }}" method="POST"
                                id="form-hapus-{{ $arus->id }}">
                                @csrf
                                @method('delete')
                                <button type="button" class="btn btn-primary rounded-0" id="btn-hapus-{{ $arus->id }}"
                                    onclick="form_hapus({{ $arus->id }})">
                                    <span id="btn-hapus-text-{{ $arus->id }}">Aktifkan</span>
                                    <span id="btn-hapus-load-{{ $arus->id }}" style="display: none;">
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
    @endforeach --}}
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
