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
                                <div class="d-flex align-items-center">
                                    <input class="form-control rounded-0" id="example-month" type="month" name="month">
                                </div>
                            </div>
                        </form>
                    </div>
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
