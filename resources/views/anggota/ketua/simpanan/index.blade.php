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
                            <a href="{{ url('anggota/pinjaman/create') }}" class="btn btn-primary rounded-0 mb-2">
                                <i class="mdi mdi-file-plus-outline me-1"></i>
                                Buat Pinjaman
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
@endsection
