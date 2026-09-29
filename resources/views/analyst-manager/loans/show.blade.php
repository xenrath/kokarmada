@extends('layout.app')

@section('title', 'Detail Pengajuan Pinjaman')

@section('content')
    <div class="container-fluid">

        {{-- Page Title --}}
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center">

                    <a href="{{ route('analyst-manager.loans.index') }}" class="btn btn-light rounded-0 me-3">
                        <i class="mdi mdi-arrow-left"></i>
                    </a>

                    <h4 class="page-title mb-0">
                        Detail Pengajuan Pinjaman
                    </h4>

                </div>
            </div>
        </div>


        {{-- Alert --}}
        @if (session('success'))
            <div class="alert alert-success rounded-0">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger rounded-0">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger rounded-0">

                <strong>Pengajuan belum dapat diproses.</strong>

                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>
        @endif


        {{-- Header Pinjaman --}}
        <div class="card rounded-0">

            <div class="card-body">

                <div class="row align-items-center">

                    <div class="col-md-8">

                        <h3 class="mt-0 mb-1">
                            {{ $loan->code }}
                        </h3>

                        <p class="text-muted mb-2">
                            {{ $loan->user->name }}

                            @if ($loan->user->memberProfile?->member_number)
                                · {{ $loan->user->memberProfile->member_number }}
                            @endif
                        </p>

                        @if ($loan->status === 'submitted')
                            <span class="badge bg-warning rounded-0">
                                Menunggu Analisis
                            </span>
                        @elseif ($loan->status === 'under_analysis')
                            <span class="badge bg-info rounded-0">
                                Dalam Analisis
                            </span>
                        @elseif ($loan->status === 'waiting_treasurer_review')
                            <span class="badge bg-secondary rounded-0">
                                Menunggu Treasurer
                            </span>
                        @elseif ($loan->status === 'waiting_chairman_approval')
                            <span class="badge bg-secondary rounded-0">
                                Menunggu Chairman
                            </span>
                        @elseif ($loan->status === 'approved')
                            <span class="badge bg-success rounded-0">
                                Disetujui
                            </span>
                        @else
                            <span class="badge bg-secondary rounded-0">
                                {{ $loan->status }}
                            </span>
                        @endif

                    </div>

                    <div class="col-md-4 text-md-end mt-3 mt-md-0">

                        @if ($loan->status === 'approved')
                            <a href="{{ route('analyst-manager.loans.documents', $loan) }}"
                                class="btn btn-primary rounded-0">
                                <i class="mdi mdi-file-document-outline me-1"></i>
                                Dokumen
                            </a>
                        @endif

                    </div>

                </div>

            </div>

        </div>


        <div class="row">

            {{-- =====================================================
                 KOLOM UTAMA
            ====================================================== --}}
            <div class="col-xl-8">

                {{-- Data Anggota --}}
                <div class="card rounded-0">

                    <div class="card-body">
                        <h4 class="header-title">
                            Data Anggota
                        </h4>
                    </div>

                    <div class="card-body border-top">

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <small class="text-muted d-block">
                                    Nama
                                </small>

                                <div>
                                    {{ $loan->user->name }}
                                </div>

                            </div>

                            <div class="col-md-6 mb-3">

                                <small class="text-muted d-block">
                                    No. Anggota
                                </small>

                                <div>
                                    {{ $loan->user->memberProfile?->member_number ?? '-' }}
                                </div>

                            </div>

                            <div class="col-md-6 mb-3">

                                <small class="text-muted d-block">
                                    No. Telepon
                                </small>

                                <div>
                                    {{ $loan->user->phone }}
                                </div>

                            </div>

                            <div class="col-md-6 mb-3">

                                <small class="text-muted d-block">
                                    Unit Kerja
                                </small>

                                <div>
                                    {{ $loan->work_unit }}
                                </div>

                            </div>

                            <div class="col-md-6">

                                <small class="text-muted d-block">
                                    Jabatan
                                </small>

                                <div>
                                    {{ $loan->position }}
                                </div>

                            </div>

                            <div class="col-md-6">

                                <small class="text-muted d-block">
                                    Lama Bekerja
                                </small>

                                <div>
                                    {{ $loan->employment_duration_years }} tahun
                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Data Pinjaman --}}
                <div class="card rounded-0">

                    <div class="card-body">

                        <h4 class="header-title">
                            Data Pinjaman
                        </h4>

                    </div>

                    <div class="card-body border-top">

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <small class="text-muted d-block">
                                    Nominal Pengajuan
                                </small>

                                <strong>
                                    Rp{{ number_format($loan->requested_amount, 0, ',', '.') }}
                                </strong>

                            </div>

                            <div class="col-md-6 mb-3">

                                <small class="text-muted d-block">
                                    Jenis Pembayaran
                                </small>

                                <div>
                                    @if ($loan->repayment_type === 'monthly')
                                        Bulanan
                                    @else
                                        Lump Sum
                                    @endif
                                </div>

                            </div>

                            <div class="col-md-6 mb-3">

                                <small class="text-muted d-block">
                                    Jangka Waktu
                                </small>

                                <div>
                                    {{ $loan->term_months }} bulan
                                </div>

                            </div>

                            <div class="col-md-6 mb-3">

                                <small class="text-muted d-block">
                                    Suku Bunga
                                </small>

                                <div>
                                    {{ number_format($loan->interest_rate, 2, ',', '.') }}%
                                    per tahun
                                </div>

                            </div>

                            <div class="col-md-6 mb-3">

                                <small class="text-muted d-block">
                                    Cicilan
                                </small>

                                <div>
                                    @if ($loan->monthly_installment)
                                        Rp{{ number_format($loan->monthly_installment, 0, ',', '.') }}
                                        / bulan
                                    @else
                                        -
                                    @endif
                                </div>

                            </div>

                            <div class="col-md-6 mb-3">

                                <small class="text-muted d-block">
                                    Tujuan
                                </small>

                                <div>
                                    @if ($loan->purpose_category === 'business')
                                        Usaha
                                    @elseif ($loan->purpose_category === 'consumer')
                                        Konsumtif
                                    @else
                                        {{ $loan->purpose_category }}
                                    @endif
                                </div>

                            </div>

                            <div class="col-12">

                                <small class="text-muted d-block">
                                    Keterangan Tujuan
                                </small>

                                <div>
                                    {{ $loan->purpose_description }}
                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Pekerjaan & Penghasilan --}}
                <div class="card rounded-0">

                    <div class="card-body">

                        <h4 class="header-title">
                            Pekerjaan & Penghasilan
                        </h4>

                    </div>

                    <div class="card-body border-top">

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <small class="text-muted d-block">
                                    Penghasilan Bersih
                                </small>

                                <h4 class="mt-1 mb-0">
                                    Rp{{ number_format($loan->net_monthly_income ?? 0, 0, ',', '.') }}
                                </h4>

                            </div>

                            <div class="col-md-6 mb-3">

                                <small class="text-muted d-block">
                                    Penghasilan Lain
                                </small>

                                <h4 class="mt-1 mb-0">
                                    Rp{{ number_format($loan->other_monthly_income ?? 0, 0, ',', '.') }}
                                </h4>

                            </div>

                        </div>

                        <div class="border-top pt-3">

                            <small class="text-muted d-block">
                                Slip Gaji
                            </small>

                            @if ($loan->salary_slip)
                                <span class="text-success">
                                    Tersedia
                                </span>
                            @else
                                <span class="text-danger">
                                    Tidak tersedia
                                </span>
                            @endif

                        </div>

                    </div>

                </div>


                {{-- Agunan --}}
                @if ($loan->collaterals->count())

                    <div class="card rounded-0">

                        <div class="card-body">

                            <h4 class="header-title">
                                Agunan
                            </h4>

                        </div>

                        <div class="card-body border-top">

                            @foreach ($loan->collaterals as $collateral)
                                <div class="border p-3 rounded-0 mb-3">

                                    <div class="row">

                                        <div class="col-md-4 mb-3">

                                            <small class="text-muted d-block">
                                                Jenis
                                            </small>

                                            <div>
                                                {{ $collateral->type }}
                                            </div>

                                        </div>

                                        <div class="col-md-4 mb-3">

                                            <small class="text-muted d-block">
                                                Status Kepemilikan
                                            </small>

                                            <div>
                                                {{ $collateral->ownership_status }}
                                            </div>

                                        </div>

                                        <div class="col-md-4 mb-3">

                                            <small class="text-muted d-block">
                                                Bukti Kepemilikan
                                            </small>

                                            <div>
                                                {{ $collateral->ownership_proof ?: '-' }}
                                            </div>

                                        </div>

                                    </div>

                                    @if ($collateral->description)
                                        <div class="border-top pt-3">

                                            <small class="text-muted d-block">
                                                Keterangan
                                            </small>

                                            <div>
                                                {{ $collateral->description }}
                                            </div>

                                        </div>
                                    @endif

                                </div>
                            @endforeach

                        </div>

                    </div>

                @endif


                {{-- Analisis Kemampuan --}}
                <div class="card rounded-0">

                    <div class="card-body">

                        <h4 class="header-title">
                            Analisis Kemampuan
                        </h4>

                        @if ($loan->analysis)
                            <span class="text-muted">
                                Rekomendasi Analyst Manager
                            </span>
                        @elseif ($loan->status === 'submitted')
                            <span class="text-muted">
                                Analisis belum dimulai
                            </span>
                        @elseif ($loan->status === 'under_analysis')
                            <span class="text-muted">
                                Analisis sedang dikerjakan
                            </span>
                        @else
                            <span class="text-muted">
                                Belum ada analisis
                            </span>
                        @endif

                    </div>

                    <div class="card-body border-top">

                        {{-- =================================================
                             STATUS: SUBMITTED
                        ================================================== --}}
                        @if ($loan->status === 'submitted')

                            <div class="mb-3">

                                <p class="text-muted mb-3">
                                    Pengajuan ini belum masuk tahap analisis.
                                    Mulai analisis terlebih dahulu untuk dapat
                                    memberikan rekomendasi.
                                </p>

                                <form action="{{ route('analyst-manager.loans.analysis.start', $loan) }}" method="POST">

                                    @csrf

                                    <button type="submit" class="btn btn-primary rounded-0">
                                        <i class="mdi mdi-play-circle-outline me-1"></i>
                                        Mulai Analisis
                                    </button>

                                </form>

                            </div>


                            {{-- =================================================
                             STATUS: UNDER ANALYSIS
                        ================================================== --}}
                        @elseif ($loan->status === 'under_analysis')
                            <form action="{{ route('analyst-manager.loans.analysis', $loan) }}" method="POST">

                                @csrf

                                <div class="mb-3">

                                    <label class="form-label">
                                        Nominal Rekomendasi
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text rounded-0">
                                            Rp
                                        </span>

                                        <input type="number" name="recommended_amount" class="form-control rounded-0"
                                            min="1" max="{{ $loan->requested_amount }}"
                                            value="{{ old('recommended_amount', $loan->analysis?->recommended_amount) }}"
                                            required>

                                    </div>

                                    <small class="text-muted">
                                        Maksimal:
                                        Rp{{ number_format($loan->requested_amount, 0, ',', '.') }}
                                    </small>

                                </div>

                                <div class="mb-3">

                                    <label class="form-label">
                                        Catatan Analisis
                                    </label>

                                    <textarea name="notes" rows="5" class="form-control rounded-0" required>{{ old('notes', $loan->analysis?->notes) }}</textarea>

                                </div>

                                <button type="submit" class="btn btn-primary rounded-0">
                                    <i class="mdi mdi-check-circle-outline me-1"></i>
                                    Selesaikan Analisis
                                </button>

                            </form>


                            {{-- =================================================
                             STATUS LAIN
                        ================================================== --}}
                        @else
                            @if ($loan->analysis)

                                <div class="row">

                                    <div class="col-md-5 mb-3">

                                        <small class="text-muted d-block">
                                            Nominal Rekomendasi
                                        </small>

                                        <h3 class="mt-1 mb-0">
                                            Rp{{ number_format($loan->analysis->recommended_amount, 0, ',', '.') }}
                                        </h3>

                                    </div>

                                    <div class="col-md-7 mb-3">

                                        <small class="text-muted d-block">
                                            Dianalisis Oleh
                                        </small>

                                        <div>
                                            {{ $loan->analysis->analyst->name }}
                                        </div>

                                        @if ($loan->analysis->reviewed_at)
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($loan->analysis->reviewed_at)->translatedFormat('d F Y H:i') }}
                                            </small>
                                        @endif

                                    </div>

                                </div>

                                <div class="border-top pt-3">

                                    <small class="text-muted d-block">
                                        Catatan Analisis
                                    </small>

                                    <div class="mt-1">
                                        {!! nl2br(e($loan->analysis->notes)) !!}
                                    </div>

                                </div>
                            @else
                                <div class="text-muted">
                                    Analisis belum tersedia.
                                </div>

                            @endif

                        @endif

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 SIDEBAR
            ====================================================== --}}
            <div class="col-xl-4">

                {{-- Ringkasan Nominal --}}
                <div class="card rounded-0">

                    <div class="card-body">

                        <h4 class="header-title">
                            Ringkasan Nominal
                        </h4>

                    </div>

                    <div class="card-body border-top">

                        <div class="mb-3">

                            <small class="text-muted d-block">
                                Pengajuan Member
                            </small>

                            <h3 class="mt-1 mb-0">
                                Rp{{ number_format($loan->requested_amount, 0, ',', '.') }}
                            </h3>

                        </div>


                        <div class="border-top pt-3 mb-3">

                            <small class="text-muted d-block">
                                Rekomendasi Analyst
                            </small>

                            <h4 class="mt-1 mb-0">

                                @if ($loan->analysis)
                                    Rp{{ number_format($loan->analysis->recommended_amount, 0, ',', '.') }}
                                @else
                                    -
                                @endif

                            </h4>

                        </div>


                        <div class="border-top pt-3">

                            <small class="text-muted d-block">
                                Nominal Disetujui
                            </small>

                            <h4 class="mt-1 mb-0">

                                @if ($loan->approved_amount)
                                    Rp{{ number_format($loan->approved_amount, 0, ',', '.') }}
                                @else
                                    <span class="text-muted">
                                        Belum disetujui
                                    </span>
                                @endif

                            </h4>

                        </div>

                    </div>

                </div>


                {{-- Riwayat Proses --}}
                <div class="card rounded-0">

                    <div class="card-body">

                        <h4 class="header-title">
                            Riwayat Proses
                        </h4>

                    </div>

                    <div class="card-body border-top">

                        <div class="d-flex justify-content-between align-items-center">

                            <div>

                                <h4 class="mt-0 mb-1">
                                    {{ $loan->processes->count() }}
                                </h4>

                                <span class="text-muted">
                                    proses telah tercatat
                                </span>

                            </div>

                            <button type="button" class="btn btn-primary rounded-0" data-bs-toggle="modal"
                                data-bs-target="#modal-riwayat-proses">
                                Lihat
                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =============================================================
         MODAL RIWAYAT PROSES
    ============================================================== --}}
    <div id="modal-riwayat-proses" class="modal fade" tabindex="-1" aria-labelledby="modal-riwayat-proses-label"
        aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-scrollable">

            <div class="modal-content rounded-0">

                <div class="modal-header rounded-0">

                    <h4 class="modal-title" id="modal-riwayat-proses-label">
                        Riwayat Proses Pinjaman
                    </h4>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                </div>

                <div class="modal-body">

                    @forelse ($loan->processes->sortBy('created_at') as $process)
                        <div class="border-bottom pb-3 mb-3">

                            <div class="row">

                                <div class="col-md-8">

                                    <h5 class="mb-1">

                                        @if ($process->action === 'submitted')
                                            Pengajuan Dibuat
                                        @elseif ($process->action === 'analysis_started')
                                            Analisis Dimulai
                                        @elseif ($process->action === 'analysis_completed')
                                            Analisis Selesai
                                        @elseif ($process->action === 'review_completed')
                                            Review Treasurer Selesai
                                        @elseif ($process->action === 'loan_approved')
                                            Pinjaman Disetujui
                                        @elseif ($process->action === 'documents_generated')
                                            Dokumen Dibuat
                                        @elseif ($process->action === 'documents_uploaded')
                                            Dokumen Diunggah
                                        @else
                                            {{ $process->action }}
                                        @endif

                                    </h5>

                                    <small class="text-muted">

                                        {{ $process->created_at ? \Carbon\Carbon::parse($process->created_at)->translatedFormat('d F Y H:i') : '-' }}

                                    </small>

                                </div>

                                <div class="col-md-4 text-md-end">

                                    <span class="badge bg-light text-dark rounded-0">
                                        {{ $process->role }}
                                    </span>

                                </div>

                            </div>

                            <div class="mt-2">

                                <strong>
                                    {{ $process->user?->name ?? 'Sistem' }}
                                </strong>

                            </div>

                            @if ($process->notes)
                                <div class="text-muted mt-2">
                                    {!! nl2br(e($process->notes)) !!}
                                </div>
                            @endif

                        </div>

                    @empty

                        <div class="text-center text-muted py-4">
                            Belum ada riwayat proses.
                        </div>
                    @endforelse

                </div>

                <div class="modal-footer rounded-0">

                    <button type="button" class="btn btn-light rounded-0" data-bs-dismiss="modal">
                        Tutup
                    </button>

                </div>

            </div>

        </div>

    </div>

@endsection
