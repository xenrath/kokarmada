@extends('layout.app')

@section('title', 'Data Pinjaman')

@section('content')
    <!-- Start Content-->
    <div class="container-fluid">
        <!-- start page title -->
        <div class="page-title-box">
            <h4 class="page-title">Data Pinjaman</h4>
        </div>
        <!-- end page title -->
        <div class="card mb-2 rounded-0">
            <div class="card-body pb-0">
                <div class="row mb-2">
                    <div class="col-xl-8">
                        <form action="{{ url('anggota/pinjaman') }}" method="get" autocomplete="off" id="form-filter"
                            class="row gy-2 gx-2 align-items-center justify-content-xl-start justify-content-between">
                            <div class="col-auto">
                                <div class="d-flex align-items-center">
                                    <select class="form-select rounded-0" name="status"
                                        onchange="$('#form-filter').submit();">
                                        <option value="">- Pilih Status -</option>
                                        <option value="menunggu"
                                            {{ request()->get('status') == 'menunggu' ? 'selected' : '' }}>
                                            Menunggu
                                        </option>
                                        <option value="proses" {{ request()->get('status') == 'proses' ? 'selected' : '' }}>
                                            Proses
                                        </option>
                                        <option value="selesai"
                                            {{ request()->get('status') == 'selesai' ? 'selected' : '' }}>
                                            Selesai
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
                            <button type="button" class="btn btn-primary rounded-0 mb-2" data-bs-toggle="modal"
                                data-bs-target="#modal-profile">
                                <i class="mdi mdi-file-plus-outline me-1"></i>
                                Buat Pinjaman
                            </button>
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
                                <th>Nama Anggota</th>
                                <th>Nominal</th>
                                <th>Jangka Waktu</th>
                                <th>Status</th>
                                <th style="width: 120px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pinjamans as $pinjaman)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $pinjaman->user->nama }}</td>
                                    <td>@rupiah($pinjaman->nominal)</td>
                                    <td>
                                        @if ($pinjaman->tipe_angsuran == 'bulanan')
                                            {{ $pinjaman->jangka_waktu * 12 }} Bulan
                                        @else
                                            {{ $pinjaman->jangka_waktu }} Tahun
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info-lighten rounded-0">
                                            {{ ucfirst($pinjaman->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ url('anggota/pinjaman/' . $pinjaman->id) }}" class="action-icon">
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

    <div id="modal-profile" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-profile-label"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-0">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-profile-label">Buat Pinjaman</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                @if ($user_detail_exists)
                    <div class="modal-body">
                        <div class="alert alert-info text-center rounded-0" role="alert">
                            <div>
                                Cek kembali data diri Anda.
                                <br>
                                Edit jika terdapat kesalahan data.
                            </div>
                            <div class="mt-2">
                                <a href="{{ url('anggota/profile') }}" class="btn btn-info btn-sm rounded-0">
                                    Perbarui Data Diri
                                    <i class="mdi mdi-account-edit"></i>
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Nama Lengkap</strong>
                                <br>
                                {{ $user->nama }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Panggilan</strong>
                                <br>
                                {{ $user->panggilan }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Jenis Kelamin</strong>
                                <br>
                                {{ $user->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>No. HP / WhatsApp</strong>
                                <br>
                                {{ $user->telp }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Tempat Lahir</strong>
                                <br>
                                {{ $user_detail->tempat_lahir ?? '-' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Tanggal Lahir</strong>
                                <br>
                                {{ $user_detail && $user_detail->tanggal_lahir ? \Carbon\Carbon::parse($user_detail->tanggal_lahir)->translatedFormat('d F Y') : null }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>File KTP</strong>
                                <br>
                                @if ($user_detail->file_ktp ?? null)
                                    <a href="{{ asset('storage/uploads/' . $user_detail->file_ktp) }}"
                                        class="btn btn-sm btn-outline-secondary rounded-0 mt-1" target="_blank">
                                        Lihat File KTP
                                    </a>
                                @else
                                    -
                                @endif
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>File KK</strong>
                                <br>
                                @if ($user_detail->file_kk ?? null)
                                    <a href="{{ asset('storage/uploads/' . $user_detail->file_kk) }}"
                                        class="btn btn-sm btn-outline-secondary rounded-0 mt-1" target="_blank">
                                        Lihat File KK
                                    </a>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Alamat Lengkap</strong>
                                <br>
                                {{ $user_detail->alamat ?? '-' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Kode POS</strong>
                                <br>
                                {{ $user_detail->kode_pos ?? '-' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Pekerjaan</strong>
                                <br>
                                {{ $user_detail->pekerjaan ?? '-' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>No. NPWP</strong>
                                <br>
                                {{ $user_detail->no_npwp ?? '-' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Nama Gadis Ibu Kandung</strong>
                                <br>
                                {{ $user_detail->nama_ibu ?? '-' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Tinggal Bersama</strong>
                                <br>
                                {{ $user_detail->tinggal_bersama ?? '-' }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>
                                    Nama
                                    @if ($user->gender == 'L')
                                        Istri
                                    @else
                                        Suami
                                    @endif
                                </strong>
                                <br>
                                {{ $user_detail->nama_pasangan ?? '-' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>
                                    Pekerjaan
                                    @if ($user->gender == 'L')
                                        Istri
                                    @else
                                        Suami
                                    @endif
                                </strong>
                                <br>
                                {{ $user_detail->pekerjaan_pasangan ?? '-' }}
                            </div>
                        </div>
                    </div>
                    <div class="modal-body border-top">
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input rounded-0" id="validasi">
                            <label class="form-check-label" for="validasi">
                                Centang jika semua data diatas sudah benar.</label>
                        </div>
                    </div>
                @else
                    <div class="modal-body">
                        <div class="alert alert-warning text-center rounded-0" role="alert">
                            <div>
                                Anda belum melengkapi data diri.
                                <br>
                                Silahkan lengkapi data diri terlebih dahulu sebelum membuat Pinjaman.
                            </div>
                            <div class="mt-2">
                                <a href="{{ url('anggota/profile') }}" class="btn btn-info btn-sm rounded-0">
                                    Lengkapi Data Diri
                                    <i class="mdi mdi-account-edit"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-light rounded-0" data-bs-dismiss="modal">Batal</button>
                    @if ($user_detail_exists)
                        <button type="button" class="btn btn-primary rounded-0" id="btn-lanjutkan"
                            onclick="form_lanjutkan()" disabled>
                            <span id="btn-lanjutkan-text">Lanjutkan</span>
                            <span id="btn-lanjutkan-load" style="display:none;">
                                <i class="mdi mdi-spin mdi-loading"></i>
                            </span>
                        </button>
                    @endif
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#validasi').on('change', function() {
                $('#btn-lanjutkan').prop('disabled', !this.checked);
            });
        });

        function form_lanjutkan() {
            $('#btn-lanjutkan').prop('disabled', true);
            $('#btn-lanjutkan-text').hide();
            $('#btn-lanjutkan-load').show();
            window.location.href = "{{ url('anggota/pinjaman/create') }}";
        }
    </script>
@endsection
