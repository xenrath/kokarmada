@extends('layout.app')

@section('title', 'Dashboard Analyst Manager')

@section('content')
    <div class="container-fluid">

        {{-- Page Title --}}
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">KOPKARMADA</li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>

                    <h4 class="page-title">
                        Dashboard Analyst Manager
                    </h4>
                </div>
            </div>
        </div>

        {{-- Welcome --}}
        <div class="row">
            <div class="col-12">
                <div class="card rounded-0">
                    <div class="card-body">
                        <h4 class="mt-0 mb-1">
                            Selamat datang,
                            {{ auth()->user()->nickname ?: auth()->user()->name }}
                        </h4>

                        <p class="text-muted mb-0">
                            Berikut ringkasan pengajuan pinjaman yang perlu Anda tindak lanjuti.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistics --}}
        <div class="row">

            {{-- Pengajuan Baru --}}
            <div class="col-lg-6 col-xl-3">
                <div class="card rounded-0">
                    <div class="card-body">
                        <div class="row align-items-center">

                            <div class="col-8">
                                <h5 class="text-muted fw-normal mt-0 text-truncate">
                                    Pengajuan Baru
                                </h5>

                                <h3 class="my-2 py-1">
                                    {{ $submitted }}
                                </h3>

                                <p class="mb-0">
                                    <a href="{{ route('analyst-manager.loans.index') }}" class="text-primary">
                                        Lihat pengajuan
                                        <i class="mdi mdi-arrow-right ms-1"></i>
                                    </a>
                                </p>
                            </div>

                            <div class="col-4">
                                <div class="text-end">
                                    <i class="uil-file-plus-alt display-5 text-muted"></i>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- Dalam Analisis --}}
            <div class="col-lg-6 col-xl-3">
                <div class="card rounded-0">
                    <div class="card-body">
                        <div class="row align-items-center">

                            <div class="col-8">
                                <h5 class="text-muted fw-normal mt-0 text-truncate">
                                    Dalam Analisis
                                </h5>

                                <h3 class="my-2 py-1">
                                    {{ $underAnalysis }}
                                </h3>

                                <p class="mb-0 text-muted">
                                    Sedang diproses
                                </p>
                            </div>

                            <div class="col-4">
                                <div class="text-end">
                                    <i class="uil-search-alt display-5 text-muted"></i>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- Menunggu Treasurer --}}
            <div class="col-lg-6 col-xl-3">
                <div class="card rounded-0">
                    <div class="card-body">
                        <div class="row align-items-center">

                            <div class="col-8">
                                <h5 class="text-muted fw-normal mt-0 text-truncate">
                                    Menunggu Treasurer
                                </h5>

                                <h3 class="my-2 py-1">
                                    {{ $waitingTreasurer }}
                                </h3>

                                <p class="mb-0 text-muted">
                                    Menunggu review
                                </p>
                            </div>

                            <div class="col-4">
                                <div class="text-end">
                                    <i class="uil-money-withdraw display-5 text-muted"></i>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- Menunggu Chairman --}}
            <div class="col-lg-6 col-xl-3">
                <div class="card rounded-0">
                    <div class="card-body">
                        <div class="row align-items-center">

                            <div class="col-8">
                                <h5 class="text-muted fw-normal mt-0 text-truncate">
                                    Menunggu Chairman
                                </h5>

                                <h3 class="my-2 py-1">
                                    {{ $waitingChairman }}
                                </h3>

                                <p class="mb-0 text-muted">
                                    Menunggu persetujuan
                                </p>
                            </div>

                            <div class="col-4">
                                <div class="text-end">
                                    <i class="uil-check-circle display-5 text-muted"></i>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Approval Summary --}}
        <div class="row">

            <div class="col-xl-4">
                <div class="card rounded-0">
                    <div class="card-body">

                        <h4 class="header-title mb-3">
                            Ringkasan Pengajuan
                        </h4>

                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <span class="text-muted">
                                Pengajuan Baru
                            </span>

                            <strong>
                                {{ $submitted }}
                            </strong>
                        </div>

                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <span class="text-muted">
                                Dalam Analisis
                            </span>

                            <strong>
                                {{ $underAnalysis }}
                            </strong>
                        </div>

                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <span class="text-muted">
                                Menunggu Treasurer
                            </span>

                            <strong>
                                {{ $waitingTreasurer }}
                            </strong>
                        </div>

                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <span class="text-muted">
                                Menunggu Chairman
                            </span>

                            <strong>
                                {{ $waitingChairman }}
                            </strong>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2">
                            <span class="text-muted">
                                Sudah Disetujui
                            </span>

                            <strong>
                                {{ $approved }}
                            </strong>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Action --}}
            <div class="col-xl-8">
                <div class="card rounded-0">
                    <div class="card-body">

                        <h4 class="header-title mb-1">
                            Pekerjaan Anda
                        </h4>

                        <p class="text-muted mb-4">
                            Pilih pekerjaan yang ingin Anda lakukan.
                        </p>

                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <a href="{{ route('analyst-manager.loans.index') }}" class="text-decoration-none">

                                    <div class="border p-3 rounded-0 h-100">

                                        <div class="d-flex align-items-center">

                                            <div class="me-3">
                                                <i class="uil-file-plus-alt font-24 text-primary"></i>
                                            </div>

                                            <div>
                                                <h5 class="mb-1">
                                                    Pengajuan Pinjaman
                                                </h5>

                                                <p class="text-muted mb-0">
                                                    Lihat dan proses pengajuan pinjaman.
                                                </p>
                                            </div>

                                        </div>

                                    </div>

                                </a>
                            </div>

                            <div class="col-md-6 mb-3">
                                <a href="{{ route('analyst-manager.loans.index') }}" class="text-decoration-none">

                                    <div class="border p-3 rounded-0 h-100">

                                        <div class="d-flex align-items-center">

                                            <div class="me-3">
                                                <i class="uil-clipboard-alt font-24 text-primary"></i>
                                            </div>

                                            <div>
                                                <h5 class="mb-1">
                                                    Kelola Pengajuan
                                                </h5>

                                                <p class="text-muted mb-0">
                                                    Tinjau pengajuan dan hasil analisis.
                                                </p>
                                            </div>

                                        </div>

                                    </div>

                                </a>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection
