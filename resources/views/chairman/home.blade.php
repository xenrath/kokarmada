@extends('layout.app')

@section('content')
    <div class="container-fluid">

        <div class="page-title-box d-flex align-items-center">
            <h4 class="page-title mb-0">Dashboard Chairman</h4>
        </div>

        <div class="row">

            <div class="col-md-6 col-xl-4">
                <div class="card rounded-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">

                            <div>
                                <h5 class="text-muted fw-normal mt-0">
                                    Menunggu Persetujuan
                                </h5>

                                <h3 class="mt-2 mb-0">
                                    {{ $waitingApproval }}
                                </h3>
                            </div>

                            <div class="avatar-sm bg-warning-subtle rounded-0">
                                <span class="avatar-title rounded-0">
                                    <i class="uil-clock text-warning font-24"></i>
                                </span>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4">
                <div class="card rounded-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">

                            <div>
                                <h5 class="text-muted fw-normal mt-0">
                                    Pinjaman Disetujui
                                </h5>

                                <h3 class="mt-2 mb-0">
                                    {{ $approved }}
                                </h3>
                            </div>

                            <div class="avatar-sm bg-success-subtle rounded-0">
                                <span class="avatar-title rounded-0">
                                    <i class="uil-check-circle text-success font-24"></i>
                                </span>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4">
                <div class="card rounded-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">

                            <div>
                                <h5 class="text-muted fw-normal mt-0">
                                    Pinjaman Dicairkan
                                </h5>

                                <h3 class="mt-2 mb-0">
                                    {{ $disbursed }}
                                </h3>
                            </div>

                            <div class="avatar-sm bg-info-subtle rounded-0">
                                <span class="avatar-title rounded-0">
                                    <i class="uil-money-withdraw text-info font-24"></i>
                                </span>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-12">

                <div class="card rounded-0">
                    <div class="card-body">

                        <h4 class="header-title mb-0">
                            Persetujuan Pinjaman
                        </h4>

                    </div>

                    <div class="card-body border-top">

                        @if ($waitingApproval > 0)
                            <div class="alert alert-warning rounded-0 mb-0">
                                <div class="d-flex align-items-center">

                                    <i class="uil-clock me-2"></i>

                                    <div>
                                        Terdapat
                                        <strong>{{ $waitingApproval }}</strong>
                                        pengajuan pinjaman yang menunggu
                                        persetujuan Anda.
                                    </div>

                                </div>
                            </div>

                            <div class="mt-3">
                                <a href="{{ route('chairman.loans.index') }}" class="btn btn-primary rounded-0">
                                    <i class="mdi mdi-file-document-outline me-1"></i>
                                    Lihat Pengajuan
                                </a>
                            </div>
                        @else
                            <div class="text-muted">
                                Tidak ada pengajuan pinjaman yang menunggu
                                persetujuan.
                            </div>
                        @endif

                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection
