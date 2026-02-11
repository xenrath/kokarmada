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
                        <form action="{{ url('anggota/simpanan') }}" method="get" autocomplete="off" id="form-filter"
                            class="row gy-2 gx-2 align-items-center justify-content-xl-start justify-content-between">
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
                                data-bs-target="#modal-generate">
                                <i class="mdi mdi-sync me-1"></i>
                                Generate Simpanan
                            </button>
                            {{-- <button type="button" class="btn btn-light rounded-0 mb-2">Export</button> --}}
                        </div>
                    </div>
                    <!-- end col-->
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-centered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 10px">No</th>
                                <th>Nama Anggota</th>
                                <th>Jumlah</th>
                                <th>Waktu</th>
                                <th>Status</th>
                                <th style="width: 120px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="text-center">
                                    <span class="text-muted">- Data tidak ditemukan -</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- container -->

    <div id="modal-generate" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-generate-label"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-0">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-generate-label">Generate Simpanan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning text-center rounded-0" role="alert">
                        <div>
                            Pastikan gaji bulan {{ Carbon\Carbon::now()->translatedFormat('F') }} telah dibayarkan terlebih
                            dahulu sebelum melakukan proses generate simpanan.
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-light rounded-0" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary rounded-0" id="btn-lanjutkan">
                        <span id="btn-lanjutkan-text">Lanjutkan</span>
                        <span id="btn-lanjutkan-load" style="display:none;">
                            <i class="mdi mdi-spin mdi-loading"></i>
                        </span>
                    </button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
@endsection
