@extends('layout.app')

@section('title', 'Dashboard Sekretaris')

@section('content')

    <div class="container-fluid">

        {{-- Page Title --}}
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">
                        Dashboard Sekretaris
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
                            Berikut ringkasan dokumen kredit yang perlu Anda
                            tindak lanjuti.
                        </p>

                    </div>

                </div>

            </div>
        </div>


        {{-- Statistics --}}
        <div class="row">

            {{-- Menunggu Validasi --}}
            <div class="col-lg-6 col-xl-3">

                <div class="card rounded-0">

                    <div class="card-body">

                        <div class="row align-items-center">

                            <div class="col-8">

                                <h5 class="text-muted fw-normal mt-0 text-truncate">
                                    Menunggu Validasi
                                </h5>

                                <h3 class="my-2 py-1">
                                    {{ $loansWaitingVerification }}
                                </h3>

                                <p class="mb-0">

                                    @if ($loansWaitingVerification > 0)
                                        <a href="#" class="text-primary">
                                            Lihat pengajuan
                                            <i class="mdi mdi-arrow-right ms-1"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">
                                            Tidak ada pengajuan
                                        </span>
                                    @endif

                                </p>

                            </div>

                            <div class="col-4">

                                <div class="text-end">
                                    <i class="uil-file-check-alt display-5 text-muted"></i>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Dokumen Menunggu --}}
            <div class="col-lg-6 col-xl-3">

                <div class="card rounded-0">

                    <div class="card-body">

                        <div class="row align-items-center">

                            <div class="col-8">

                                <h5 class="text-muted fw-normal mt-0 text-truncate">
                                    Dokumen Menunggu
                                </h5>

                                <h3 class="my-2 py-1">
                                    {{ $pendingDocuments }}
                                </h3>

                                <p class="mb-0 text-muted">
                                    Perlu diperiksa
                                </p>

                            </div>

                            <div class="col-4">

                                <div class="text-end">
                                    <i class="uil-file-search-alt display-5 text-muted"></i>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Sudah Divalidasi --}}
            <div class="col-lg-6 col-xl-3">

                <div class="card rounded-0">

                    <div class="card-body">

                        <div class="row align-items-center">

                            <div class="col-8">

                                <h5 class="text-muted fw-normal mt-0 text-truncate">
                                    Sudah Divalidasi
                                </h5>

                                <h3 class="my-2 py-1">
                                    {{ $verifiedDocuments }}
                                </h3>

                                <p class="mb-0 text-muted">
                                    Dokumen terverifikasi
                                </p>

                            </div>

                            <div class="col-4">

                                <div class="text-end">
                                    <i class="uil-file-check-alt display-5 text-muted"></i>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Perlu Diperbaiki --}}
            <div class="col-lg-6 col-xl-3">

                <div class="card rounded-0">

                    <div class="card-body">

                        <div class="row align-items-center">

                            <div class="col-8">

                                <h5 class="text-muted fw-normal mt-0 text-truncate">
                                    Perlu Diperbaiki
                                </h5>

                                <h3 class="my-2 py-1">
                                    {{ $rejectedDocuments }}
                                </h3>

                                <p class="mb-0 text-muted">
                                    Dokumen ditolak
                                </p>

                            </div>

                            <div class="col-4">

                                <div class="text-end">
                                    <i class="uil-file-times display-5 text-muted"></i>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- Summary & Action --}}
        <div class="row">

            {{-- Ringkasan --}}
            <div class="col-xl-4">

                <div class="card rounded-0">

                    <div class="card-body">

                        <h4 class="header-title mb-3">
                            Ringkasan Dokumen
                        </h4>


                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">

                            <span class="text-muted">
                                Menunggu Validasi
                            </span>

                            <strong>
                                {{ $pendingDocuments }}
                            </strong>

                        </div>


                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">

                            <span class="text-muted">
                                Sudah Divalidasi
                            </span>

                            <strong>
                                {{ $verifiedDocuments }}
                            </strong>

                        </div>


                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">

                            <span class="text-muted">
                                Perlu Diperbaiki
                            </span>

                            <strong>
                                {{ $rejectedDocuments }}
                            </strong>

                        </div>


                        <div class="d-flex justify-content-between align-items-center pt-2">

                            <span class="text-muted">
                                Pengajuan Menunggu
                            </span>

                            <strong>
                                {{ $loansWaitingVerification }}
                            </strong>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Pekerjaan Anda --}}
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

                            {{-- Validasi Dokumen --}}
                            <div class="col-md-6 mb-3">

                                <a href="#" class="text-decoration-none">

                                    <div class="border p-3 rounded-0 h-100">

                                        <div class="d-flex align-items-center">

                                            <div class="me-3">

                                                <i class="uil-file-check-alt font-24 text-primary"></i>

                                            </div>


                                            <div>

                                                <h5 class="mb-1">
                                                    Validasi Dokumen
                                                </h5>

                                                <p class="text-muted mb-0">
                                                    Periksa dan validasi dokumen
                                                    kredit anggota.
                                                </p>

                                            </div>

                                        </div>

                                    </div>

                                </a>

                            </div>


                            {{-- Dokumen Perlu Diperbaiki --}}
                            <div class="col-md-6 mb-3">

                                <a href="#" class="text-decoration-none">

                                    <div class="border p-3 rounded-0 h-100">

                                        <div class="d-flex align-items-center">

                                            <div class="me-3">

                                                <i class="uil-file-times font-24 text-primary"></i>

                                            </div>


                                            <div>

                                                <h5 class="mb-1">
                                                    Dokumen Perlu Diperbaiki
                                                </h5>

                                                <p class="text-muted mb-0">
                                                    Lihat dokumen yang membutuhkan
                                                    perbaikan.
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
