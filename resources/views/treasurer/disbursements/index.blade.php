@extends('layout.app')

@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">

                <div class="page-title-box">
                    <h4 class="page-title">
                        Pinjaman Siap Dicairkan
                    </h4>
                </div>

            </div>
        </div>

        <div class="card rounded-0">

            <div class="card-body">
                <h4 class="header-title">
                    Daftar Pinjaman
                </h4>
            </div>

            <div class="card-body border-top">

                @forelse($loans as $loan)
                    <div class="border p-3 rounded-0 mb-3">

                        <div class="row g-3 align-items-center">

                            <div class="col-md-4">
                                <small class="text-muted d-block">
                                    Kode Pinjaman
                                </small>

                                <strong>
                                    {{ $loan->code }}
                                </strong>
                            </div>

                            <div class="col-md-3">
                                <small class="text-muted d-block">
                                    Anggota
                                </small>

                                <strong>
                                    {{ $loan->user->name }}
                                </strong>
                            </div>

                            <div class="col-md-3">
                                <small class="text-muted d-block">
                                    Nominal Disetujui
                                </small>

                                <strong>
                                    Rp {{ number_format($loan->approved_amount, 0, ',', '.') }}
                                </strong>
                            </div>

                            <div class="col-md-2 text-md-end">

                                <a href="{{ route('treasurer.disbursements.show', $loan) }}"
                                    class="btn btn-light btn-sm rounded-0">
                                    <i class="mdi mdi-eye-outline me-1"></i>
                                    Detail
                                </a>

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="text-center py-4 text-muted">

                        <i class="mdi mdi-cash-remove font-24 d-block mb-2"></i>

                        Belum ada pinjaman yang siap dicairkan.

                    </div>
                @endforelse

            </div>

        </div>

    </div>
@endsection
