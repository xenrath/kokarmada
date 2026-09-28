@extends('layout.app')

@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">

                <div class="page-title-box">
                    <h4 class="page-title">
                        Detail Pencairan Pinjaman
                    </h4>
                </div>

            </div>
        </div>

        {{-- INFORMASI PINJAMAN --}}
        <div class="card rounded-0">

            <div class="card-body">
                <h4 class="header-title">
                    Informasi Pinjaman
                </h4>
            </div>

            <div class="card-body border-top">

                <div class="row g-3">

                    <div class="col-md-6">
                        <small class="text-muted d-block">
                            Kode Pinjaman
                        </small>

                        <strong>
                            {{ $loan->code }}
                        </strong>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted d-block">
                            Nama Anggota
                        </small>

                        <strong>
                            {{ $loan->user->name }}
                        </strong>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted d-block">
                            Nominal Pengajuan
                        </small>

                        <strong>
                            Rp {{ number_format($loan->requested_amount, 0, ',', '.') }}
                        </strong>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted d-block">
                            Nominal Disetujui Chairman
                        </small>

                        <strong>
                            Rp {{ number_format($loan->approved_amount, 0, ',', '.') }}
                        </strong>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted d-block">
                            Tujuan Pinjaman
                        </small>

                        <strong>
                            {{ $loan->purpose_description ?: '-' }}
                        </strong>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted d-block">
                            Jenis Pembayaran
                        </small>

                        <strong>
                            @if ($loan->repayment_type === 'monthly')
                                Angsuran Bulanan
                            @elseif($loan->repayment_type === 'lump_sum')
                                Sekaligus (Lump Sum)
                            @else
                                -
                            @endif
                        </strong>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted d-block">
                            Jangka Waktu
                        </small>

                        <strong>
                            {{ $loan->term_months ? $loan->term_months . ' bulan' : '-' }}
                        </strong>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted d-block">
                            Angsuran Per Bulan
                        </small>

                        <strong>
                            @if ($loan->monthly_installment)
                                Rp {{ number_format($loan->monthly_installment, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </strong>
                    </div>

                </div>

            </div>

        </div>


        {{-- INFORMASI ANALISIS --}}
        <div class="card rounded-0">

            <div class="card-body">
                <h4 class="header-title">
                    Hasil Review
                </h4>
            </div>

            <div class="card-body border-top">

                <div class="row g-3">

                    <div class="col-md-6">
                        <small class="text-muted d-block">
                            Rekomendasi Analyst Manager
                        </small>

                        <strong>
                            Rp {{ number_format($loan->analysis->recommended_amount ?? 0, 0, ',', '.') }}
                        </strong>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted d-block">
                            Rekomendasi Treasurer
                        </small>

                        <strong>
                            Rp {{ number_format($loan->treasurerReview->recommended_amount ?? 0, 0, ',', '.') }}
                        </strong>
                    </div>

                    <div class="col-12">
                        <small class="text-muted d-block">
                            Catatan Analyst Manager
                        </small>

                        <div class="border p-3 rounded-0 mt-1">
                            {{ $loan->analysis->notes ?? '-' }}
                        </div>
                    </div>

                    <div class="col-12">
                        <small class="text-muted d-block">
                            Catatan Treasurer
                        </small>

                        <div class="border p-3 rounded-0 mt-1">
                            {{ $loan->treasurerReview->notes ?? '-' }}
                        </div>
                    </div>

                </div>

            </div>

        </div>


        {{-- STATUS DOKUMEN --}}
        <div class="card rounded-0">

            <div class="card-body">
                <h4 class="header-title">
                    Verifikasi Dokumen
                </h4>
            </div>

            <div class="card-body border-top">

                <div class="row g-3">

                    @foreach ($loan->documents->whereIn('document_type', ['approval_letter', 'credit_agreement']) as $document)
                        <div class="col-md-6">

                            <div class="border p-3 rounded-0 h-100">

                                <div class="d-flex justify-content-between align-items-start">

                                    <strong>
                                        @if ($document->document_type === 'approval_letter')
                                            Surat Persetujuan Kredit
                                        @elseif($document->document_type === 'credit_agreement')
                                            Surat Perjanjian Kredit
                                        @endif
                                    </strong>

                                    @if ($document->status === 'verified')
                                        <span class="badge bg-success rounded-0">
                                            Terverifikasi
                                        </span>
                                    @elseif($document->status === 'rejected')
                                        <span class="badge bg-danger rounded-0">
                                            Perlu Diperbaiki
                                        </span>
                                    @else
                                        <span class="badge bg-warning rounded-0">
                                            Menunggu Validasi
                                        </span>
                                    @endif

                                </div>

                                @if ($document->verified_at)
                                    <div class="mt-3 text-muted">
                                        <small>
                                            Diverifikasi pada
                                            {{ \Carbon\Carbon::parse($document->verified_at)->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                @endif

                            </div>

                        </div>
                    @endforeach

                </div>

            </div>

        </div>


        {{-- STATUS PENCAIRAN --}}
        <div class="card rounded-0">

            <div class="card-body">
                <h4 class="header-title">
                    Pencairan Pinjaman
                </h4>
            </div>

            <div class="card-body border-top">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Nominal Pencairan
                        </label>

                        <input type="text" class="form-control rounded-0"
                            value="Rp {{ number_format($loan->approved_amount, 0, ',', '.') }}" readonly>

                        <small class="text-muted">
                            Nominal mengikuti keputusan Chairman.
                        </small>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label for="account_id" class="form-label">
                            Rekening Sumber Dana
                        </label>

                        <select id="account_id" name="account_id" class="form-select rounded-0" form="disbursementForm">

                            <option value="">
                                Pilih Rekening
                            </option>

                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}">
                                    {{ $account->name }}
                                    @if ($account->bank_name)
                                        - {{ $account->bank_name }}
                                    @endif
                                </option>
                            @endforeach

                        </select>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label for="disbursed_at" class="form-label">
                            Tanggal Pencairan
                        </label>

                        <input type="date" id="disbursed_at" name="disbursed_at" class="form-control rounded-0"
                            value="{{ date('Y-m-d') }}" form="disbursementForm">

                    </div>

                    <div class="col-md-6 mb-3">

                        <label for="notes" class="form-label">
                            Catatan
                        </label>

                        <input type="text" id="notes" name="notes" class="form-control rounded-0"
                            placeholder="Catatan pencairan" form="disbursementForm">

                    </div>

                </div>

                <div class="border-top pt-3 mt-2">

                    <form id="disbursementForm" method="POST"
                        action="{{ route('treasurer.disbursements.store', $loan) }}">
                        @csrf

                        <button type="submit" class="btn btn-primary rounded-0">
                            <i class="mdi mdi-cash-check me-1"></i>
                            Cairkan Pinjaman
                        </button>
                    </form>

                </div>

            </div>

        </div>

    </div>
@endsection
