@extends('layout.app')

@section('title', 'Pengaturan Pinjaman')

@section('content')
    <!-- Start Content-->
    <div class="container-fluid">
        <!-- start page title -->
        <div class="page-title-box">
            <h4 class="page-title">Pengaturan Pinjaman</h4>
        </div>
        <!-- end page title -->
        <div class="card mb-4 rounded-0">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <strong>Bunga Pinjaman</strong>
                            <br>
                            <span>
                                {{ $pengaturan->bunga_pinjaman }}
                                % per Tahun
                            </span>
                            <br>
                            <span>
                                {{ number_format($pengaturan->bunga_pinjaman / 12, 2) }}
                                % per Bulan
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2">
                            <strong>Jangka Waktu Pinjaman</strong>
                            <br>
                            {{ $pengaturan->jangka_waktu_pinjaman }}
                            Tahun /
                            {{ $pengaturan->jangka_waktu_pinjaman * 12 }}
                            Bulan
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-warning rounded-0" data-bs-toggle="modal"
                    data-bs-target="#modal-edit">
                    <i class="mdi mdi-cog"></i>
                    Edit Pengaturan
                </button>
            </div>
        </div>
    </div>
    <!-- container -->

    <div id="modal-edit" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-edit-label"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-0">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-edit-label">Edit Pengaturan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <form action="{{ url('admin/pengaturan/' . $pengaturan->id) }}" method="post" id="form-edit">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group mb-2">
                            <label for="bunga_pinjaman" class="form-label">Bunga Pinjaman *</label>
                            <div class="input-group">
                                <input type="text" id="bunga_pinjaman" name="bunga_pinjaman"
                                    class="form-control rounded-0 @error('bunga_pinjaman') is-invalid @enderror"
                                    value="{{ old('bunga_pinjaman', $pengaturan->bunga_pinjaman) }}">
                                <span class="input-group-text rounded-0">% / Tahun</span>
                            </div>
                            @error('bunga_pinjaman')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-group mb-2">
                            <label for="jangka_waktu_pinjaman" class="form-label">Jangka Waktu Pinjaman *</label>
                            <div class="input-group">
                                <input type="text" id="jangka_waktu_pinjaman" name="jangka_waktu_pinjaman"
                                    class="form-control rounded-0 @error('jangka_waktu_pinjaman') is-invalid @enderror"
                                    value="{{ old('jangka_waktu_pinjaman', $pengaturan->jangka_waktu_pinjaman) }}">
                                <span class="input-group-text rounded-0">Tahun</span>
                            </div>
                            @error('jangka_waktu_pinjaman')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-light rounded-0" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary rounded-0" id="btn-edit" onclick="form_edit()">
                            <span id="btn-edit-text">
                                Simpan
                            </span>
                            <span id="btn-edit-load" style="display: none;">
                                <i class="mdi mdi-spin mdi-loading"></i>
                                Memproses...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
@endsection

@section('script')
    <script>
        function form_edit() {
            $('#btn-edit').prop('disabled', true);
            $('#btn-edit-text').hide();
            $('#btn-edit-load').show();
            $('#form-edit').submit();
        }
    </script>
@endsection
