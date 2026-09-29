@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col-12">

            <div class="page-title-box">
                <h4 class="page-title">
                    <i class="mdi mdi-cash-multiple me-1"></i>
                    Dashboard Treasurer
                </h4>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card rounded-0">
                <div class="card-body">
                    <h4 class="header-title">
                        Selamat Datang
                    </h4>

                    <p class="text-muted mb-0">
                        Kelola review keuangan, persetujuan pencairan, dan transaksi pinjaman KOPKARMADA.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-md-6 col-xl-3">
            <div class="card rounded-0">
                <div class="card-body">
                    <div class="row align-items-center">

                        <div class="col-8">
                            <div class="text-muted text-uppercase font-12">
                                Menunggu Review
                            </div>

                            <h3 class="mb-0 mt-2">
                                {{ $waitingReview }}
                            </h3>
                        </div>

                        <div class="col-4 text-end">
                            <i class="mdi mdi-file-document-edit-outline display-5 text-muted"></i>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card rounded-0">
                <div class="card-body">
                    <div class="row align-items-center">

                        <div class="col-8">
                            <div class="text-muted text-uppercase font-12">
                                Menunggu Chairman
                            </div>

                            <h3 class="mb-0 mt-2">
                                {{ $waitingChairman }}
                            </h3>
                        </div>

                        <div class="col-4 text-end">
                            <i class="mdi mdi-account-check-outline display-5 text-muted"></i>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card rounded-0">
                <div class="card-body">
                    <div class="row align-items-center">

                        <div class="col-8">
                            <div class="text-muted text-uppercase font-12">
                                Disetujui
                            </div>

                            <h3 class="mb-0 mt-2">
                                {{ $approved }}
                            </h3>
                        </div>

                        <div class="col-4 text-end">
                            <i class="mdi mdi-check-circle-outline display-5 text-muted"></i>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card rounded-0">
                <div class="card-body">
                    <div class="row align-items-center">

                        <div class="col-8">
                            <div class="text-muted text-uppercase font-12">
                                Sudah Dicairkan
                            </div>

                            <h3 class="mb-0 mt-2">
                                {{ $disbursed }}
                            </h3>
                        </div>

                        <div class="col-4 text-end">
                            <i class="mdi mdi-cash-check display-5 text-muted"></i>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">

        <div class="col-xl-6">
            <div class="card rounded-0 h-100">

                <div class="card-body">
                    <h4 class="header-title">
                        Ringkasan
                    </h4>
                </div>

                <div class="card-body border-top">

                    <div class="row">
                        <div class="col-7 text-muted">
                            Pengajuan menunggu review
                        </div>

                        <div class="col-5 text-end">
                            <strong>{{ $waitingReview }}</strong>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-7 text-muted">
                            Menunggu persetujuan Chairman
                        </div>

                        <div class="col-5 text-end">
                            <strong>{{ $waitingChairman }}</strong>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-7 text-muted">
                            Pinjaman disetujui
                        </div>

                        <div class="col-5 text-end">
                            <strong>{{ $approved }}</strong>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-7 text-muted">
                            Pinjaman sudah dicairkan
                        </div>

                        <div class="col-5 text-end">
                            <strong>{{ $disbursed }}</strong>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <div class="col-xl-6">
            <div class="card rounded-0 h-100">

                <div class="card-body">
                    <h4 class="header-title">
                        Pekerjaan Anda
                    </h4>
                </div>

                <div class="card-body border-top">

                    <div class="row g-3">

                        <div class="col-md-6">
                            <a href="{{ route('treasurer.loans.index') }}"
                                class="d-block border p-3 rounded-0 text-decoration-none h-100">

                                <i class="mdi mdi-file-document-edit-outline font-24 text-primary"></i>

                                <h5 class="mt-2 mb-1">
                                    Review Pinjaman
                                </h5>

                                <p class="text-muted mb-0">
                                    Review pengajuan yang menunggu pemeriksaan Treasurer.
                                </p>

                            </a>
                        </div>

                        <div class="col-md-6">
                            <a href="{{ route('treasurer.disbursements.index') }}"
                                class="d-block border p-3 rounded-0 text-decoration-none h-100">

                                <i class="mdi mdi-cash-check font-24 text-primary"></i>

                                <h5 class="mt-2 mb-1">
                                    Pencairan Pinjaman
                                </h5>

                                <p class="text-muted mb-0">
                                    Lihat pinjaman yang telah siap untuk dicairkan.
                                </p>

                            </a>
                        </div>

                    </div>

                </div>

            </div>
        </div>

    </div>
@endsection
