@extends('layout.app')

@section('title', 'Detail Pinjaman')

@section('content')
    <div class="container-fluid">

        <div class="page-title-box d-flex align-items-center">
            <a href="{{ route('member.loans.index') }}" class="btn btn-light rounded-0 me-3">
                <i class="mdi mdi-arrow-left"></i>
            </a>

            <h4 class="page-title mb-0">
                Detail Pinjaman
            </h4>
        </div>

        {{-- INFORMASI PINJAMAN --}}
        <div class="card rounded-0 mb-3">

            <div class="card-body">
                <h4 class="header-title">
                    Informasi Pinjaman
                </h4>
            </div>

            <div class="card-body border-top">

                <div class="row g-3">

                    <div class="col-md-4">
                        <div class="text-muted mb-1">
                            Kode Pinjaman
                        </div>

                        <div class="fw-semibold">
                            {{ $loan->code }}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="text-muted mb-1">
                            Status
                        </div>

                        @if ($loan->status === 'submitted')
                            <span class="badge bg-secondary rounded-0">
                                Diajukan
                            </span>
                        @elseif ($loan->status === 'waiting_treasurer_review')
                            <span class="badge bg-warning rounded-0">
                                Review Treasurer
                            </span>
                        @elseif ($loan->status === 'waiting_chairman_approval')
                            <span class="badge bg-warning rounded-0">
                                Menunggu Persetujuan Chairman
                            </span>
                        @elseif ($loan->status === 'approved')
                            <span class="badge bg-success rounded-0">
                                Disetujui
                            </span>
                        @elseif ($loan->status === 'disbursed')
                            <span class="badge bg-primary rounded-0">
                                Sudah Dicairkan
                            </span>
                        @else
                            <span class="badge bg-secondary rounded-0">
                                {{ $loan->status }}
                            </span>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <div class="text-muted mb-1">
                            Tanggal Pengajuan
                        </div>

                        <div class="fw-semibold">
                            {{ \Carbon\Carbon::parse($loan->submitted_at)->format('d/m/Y H:i') }}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="text-muted mb-1">
                            Nominal Pengajuan
                        </div>

                        <div class="fw-semibold">
                            Rp{{ number_format($loan->requested_amount, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="text-muted mb-1">
                            Nominal Disetujui
                        </div>

                        <div class="fw-semibold">
                            @if ($loan->approved_amount)
                                Rp{{ number_format($loan->approved_amount, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="text-muted mb-1">
                            Suku Bunga
                        </div>

                        <div class="fw-semibold">
                            {{ number_format($loan->interest_rate, 2, ',', '.') }}% / tahun
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="text-muted mb-1">
                            Tenor
                        </div>

                        <div class="fw-semibold">
                            {{ $loan->term_months }} bulan
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="text-muted mb-1">
                            Jenis Angsuran
                        </div>

                        <div class="fw-semibold">
                            @if ($loan->repayment_type === 'monthly')
                                Bulanan
                            @elseif ($loan->repayment_type === 'lump_sum')
                                Sekaligus
                            @else
                                -
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="text-muted mb-1">
                            Angsuran
                        </div>

                        <div class="fw-semibold">
                            @if ($loan->monthly_installment)
                                Rp{{ number_format($loan->monthly_installment, 0, ',', '.') }}
                                / bulan
                            @elseif ($loan->repayment_type === 'lump_sum')
                                Lump Sum
                            @else
                                -
                            @endif
                        </div>
                    </div>

                </div>

            </div>
        </div>


        {{-- KELAYAKAN TOP UP --}}
        @if ($topUpEligibility)
            <div class="card rounded-0 mb-3">

                <div class="card-body">
                    <h4 class="header-title mb-1">
                        Status Kelayakan Top Up
                    </h4>

                    <p class="text-muted mb-0">
                        Status berikut dihitung berdasarkan pinjaman aktif terbaru,
                        riwayat pembayaran pokok, tunggakan, dan pengaturan koperasi.
                    </p>
                </div>

                <div class="card-body border-top">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <div class="text-muted mb-1">
                                Hasil Kelayakan
                            </div>

                            @if ($topUpEligibility['eligible'])
                                <span class="badge bg-success rounded-0">
                                    Memenuhi Syarat
                                </span>
                            @else
                                <span class="badge bg-warning rounded-0">
                                    Belum Memenuhi Syarat
                                </span>
                            @endif
                        </div>

                        <div class="text-end">
                            <div class="text-muted mb-1">
                                Persentase Pokok Terbayar
                            </div>

                            <div class="fw-semibold">
                                {{ number_format($topUpEligibility['principal_repayment_percent'], 2, ',', '.') }}%
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">

                        <div class="col-md-4">
                            <div class="text-muted mb-1">
                                Pokok Sudah Dibayar
                            </div>

                            <div class="fw-semibold">
                                Rp{{ number_format($topUpEligibility['principal_paid'], 0, ',', '.') }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted mb-1">
                                Sisa Pokok
                            </div>

                            <div class="fw-semibold">
                                Rp{{ number_format($topUpEligibility['outstanding_principal'], 0, ',', '.') }}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted mb-1">
                                Angsuran Lama
                            </div>

                            <div class="fw-semibold">
                                Rp{{ number_format($topUpEligibility['old_monthly_installment'], 0, ',', '.') }}
                                / bulan
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted mb-1">
                                Minimum Tambahan Top Up
                            </div>

                            <div class="fw-semibold">
                                @if ($topUpEligibility['minimum_additional_amount'] !== null)
                                    Rp{{ number_format($topUpEligibility['minimum_additional_amount'], 0, ',', '.') }}
                                @else
                                    Belum dikonfigurasi
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted mb-1">
                                Maksimum Tambahan Top Up
                            </div>

                            <div class="fw-semibold">
                                @if ($topUpEligibility['maximum_additional_amount'] !== null)
                                    Rp{{ number_format($topUpEligibility['maximum_additional_amount'], 0, ',', '.') }}
                                @else
                                    Belum tersedia
                                @endif
                            </div>
                        </div>

                    </div>

                    @if ($topUpEligibility['financial'])
                        <div class="border-top mt-4 pt-3">

                            <h5 class="mb-3">
                                Informasi Kapasitas Keuangan
                            </h5>

                            <div class="row g-3">

                                <div class="col-md-3">
                                    <div class="text-muted mb-1">
                                        Pendapatan Bersih
                                    </div>

                                    <div class="fw-semibold">
                                        Rp{{ number_format($topUpEligibility['financial']['net_monthly_income'], 0, ',', '.') }}
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="text-muted mb-1">
                                        Pendapatan Lain
                                    </div>

                                    <div class="fw-semibold">
                                        Rp{{ number_format($topUpEligibility['financial']['other_monthly_income'], 0, ',', '.') }}
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="text-muted mb-1">
                                        Kewajiban Eksternal
                                    </div>

                                    <div class="fw-semibold">
                                        Rp{{ number_format($topUpEligibility['financial']['external_monthly_obligations'], 0, ',', '.') }}
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="text-muted mb-1">
                                        Sisa Pendapatan Setelah Angsuran Lama
                                    </div>

                                    <div class="fw-semibold">
                                        Rp{{ number_format($topUpEligibility['financial']['available_income'], 0, ',', '.') }}
                                    </div>
                                </div>

                            </div>

                            @if ($topUpEligibility['financial']['capacity_threshold_percent'] !== null)
                                <div class="alert alert-info rounded-0 mt-3 mb-0">
                                    Ambang kapasitas indikator:
                                    <strong>
                                        {{ number_format($topUpEligibility['financial']['capacity_threshold_percent'], 2, ',', '.') }}%
                                    </strong>
                                    dengan nilai indikator
                                    <strong>
                                        Rp{{ number_format($topUpEligibility['financial']['capacity_limit_amount'], 0, ',', '.') }}
                                    </strong>.
                                    Nilai ini digunakan sebagai bahan analisis dan bukan keputusan otomatis.
                                </div>
                            @endif

                        </div>
                    @endif

                    <div class="border-top mt-4 pt-3">
                        <p class="{{ $topUpEligibility['eligible'] ? 'text-success' : 'text-muted' }} mb-0">
                            <i class="mdi mdi-information-outline me-1"></i>
                            {{ $topUpEligibility['reason'] }}
                        </p>
                    </div>

                </div>
            </div>
        @endif


        {{-- JADWAL ANGSURAN --}}
        @if ($loan->status === 'disbursed' && $loan->installments->isNotEmpty())

            <div class="card rounded-0">

                <div class="card-body">
                    <h4 class="header-title">
                        Jadwal Angsuran
                    </h4>
                </div>

                <div class="card-body border-top p-0">

                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">

                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Pokok</th>
                                    <th>Bunga</th>
                                    <th>Denda</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach ($loan->installments as $installment)
                                    <tr>

                                        <td>
                                            {{ $installment->installment_number }}
                                        </td>

                                        <td>
                                            {{ \Carbon\Carbon::parse($installment->due_date)->format('d/m/Y') }}
                                        </td>

                                        <td>
                                            Rp{{ number_format($installment->principal_amount, 0, ',', '.') }}
                                        </td>

                                        <td>
                                            Rp{{ number_format($installment->interest_amount, 0, ',', '.') }}
                                        </td>

                                        <td>
                                            Rp{{ number_format($installment->penalty_amount, 0, ',', '.') }}
                                        </td>

                                        <td class="fw-semibold">
                                            Rp{{ number_format($installment->total_amount, 0, ',', '.') }}
                                        </td>

                                        <td>
                                            @if ($installment->status === 'pending')
                                                <span class="badge bg-warning rounded-0">
                                                    Belum Bayar
                                                </span>
                                            @elseif ($installment->status === 'paid')
                                                <span class="badge bg-success rounded-0">
                                                    Lunas
                                                </span>
                                            @else
                                                <span class="badge bg-secondary rounded-0">
                                                    {{ $installment->status }}
                                                </span>
                                            @endif
                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>
                    </div>

                </div>

            </div>

        @endif

    </div>
@endsection
