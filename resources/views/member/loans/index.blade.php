@extends('layout.app')

@section('title', 'Data Pinjaman')

@section('content')
    <div class="container-fluid">

        {{-- Page title --}}
        <div class="d-flex align-items-center gap-2 mb-0">
            <div class="page-title-box">
                <h4 class="page-title mb-0">Data Pinjaman</h4>
            </div>
        </div>

        {{-- Summary --}}
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-muted mb-2">Total Pengajuan</h5>
                        <h3 class="mb-0">{{ $loans->count() }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-muted mb-2">Pengajuan Aktif</h5>
                        <h3 class="mb-0">
                            {{ $loans->whereIn('status', [
                                    'submitted',
                                    'analyst_reviewed',
                                    'treasurer_reviewed',
                                    'chairman_approved',
                                    'awaiting_signature',
                                    'awaiting_document_upload',
                                    'awaiting_secretary_verification',
                                    'ready_for_disbursement',
                                    'disbursed',
                                    'active',
                                ])->count() }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-muted mb-2">Pinjaman Selesai</h5>
                        <h3 class="mb-0">
                            {{ $loans->where('status', 'completed')->count() }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Loan list --}}
        <div class="card">

            <div class="card-body d-flex justify-content-between align-items-center">
                <h4 class="header-title mb-0">Riwayat Pinjaman</h4>

                <a href="{{ route('member.loans.create') }}" class="btn btn-primary rounded-0">
                    <i class="mdi mdi-plus me-1"></i>
                    Ajukan Pinjaman
                </a>
            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-centered mb-0">

                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 60px;">
                                    No
                                </th>

                                <th>
                                    Kode
                                </th>

                                <th>
                                    Tanggal Pengajuan
                                </th>

                                <th>
                                    Nominal Pengajuan
                                </th>

                                <th>
                                    Nominal Disetujui
                                </th>

                                <th>
                                    Jenis
                                </th>

                                <th>
                                    Jangka Waktu
                                </th>

                                <th>
                                    Status
                                </th>

                                <th class="text-center" style="width: 80px;">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($loans as $loan)
                                <tr>

                                    <td class="text-center">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td>
                                        <strong>
                                            {{ $loan->code }}
                                        </strong>
                                    </td>

                                    <td>
                                        {{ $loan->submitted_at ? \Carbon\Carbon::parse($loan->submitted_at)->translatedFormat('d F Y H:i') : '-' }}
                                    </td>

                                    <td>
                                        @rupiah($loan->requested_amount)
                                    </td>

                                    <td>
                                        @if ($loan->approved_amount !== null)
                                            @rupiah($loan->approved_amount)
                                        @else
                                            <span class="text-muted">
                                                Belum disetujui
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($loan->repayment_type === 'monthly')
                                            Bulanan
                                        @else
                                            Sekaligus
                                        @endif
                                    </td>

                                    <td>
                                        {{ $loan->term_months }} bulan
                                    </td>

                                    <td>

                                        @switch($loan->status)
                                            @case('submitted')
                                                <span class="badge bg-secondary rounded-0">
                                                    Diajukan
                                                </span>
                                            @break

                                            @case('analyst_reviewed')
                                                <span class="badge bg-info rounded-0">
                                                    Dianalisis
                                                </span>
                                            @break

                                            @case('treasurer_reviewed')
                                                <span class="badge bg-info rounded-0">
                                                    Direview Bendahara
                                                </span>
                                            @break

                                            @case('chairman_approved')
                                                <span class="badge bg-primary rounded-0">
                                                    Disetujui Ketua
                                                </span>
                                            @break

                                            @case('awaiting_signature')
                                                <span class="badge bg-warning text-dark rounded-0">
                                                    Menunggu Tanda Tangan
                                                </span>
                                            @break

                                            @case('awaiting_document_upload')
                                                <span class="badge bg-warning text-dark rounded-0">
                                                    Menunggu Upload Dokumen
                                                </span>
                                            @break

                                            @case('awaiting_secretary_verification')
                                                <span class="badge bg-warning text-dark rounded-0">
                                                    Menunggu Verifikasi
                                                </span>
                                            @break

                                            @case('ready_for_disbursement')
                                                <span class="badge bg-success rounded-0">
                                                    Siap Dicairkan
                                                </span>
                                            @break

                                            @case('disbursed')
                                                <span class="badge bg-success rounded-0">
                                                    Sudah Dicairkan
                                                </span>
                                            @break

                                            @case('active')
                                                <span class="badge bg-success rounded-0">
                                                    Berjalan
                                                </span>
                                            @break

                                            @case('completed')
                                                <span class="badge bg-dark rounded-0">
                                                    Selesai
                                                </span>
                                            @break

                                            @case('rejected')
                                                <span class="badge bg-danger rounded-0">
                                                    Ditolak
                                                </span>
                                            @break

                                            @default
                                                <span class="badge bg-secondary rounded-0">
                                                    {{ ucfirst(str_replace('_', ' ', $loan->status)) }}
                                                </span>
                                        @endswitch

                                    </td>

                                    <td class="text-center">

                                        <a href="{{ route('member.loans.show', $loan) }}"
                                            class="btn btn-sm btn-primary rounded-0">
                                            <i class="mdi mdi-eye"></i>
                                            Detail
                                        </a>

                                    </td>

                                </tr>

                                @empty

                                    <tr>

                                        <td colspan="9" class="text-center py-4">

                                            <div class="text-muted">

                                                <i class="mdi mdi-file-document-outline" style="font-size: 32px;"></i>

                                                <div class="mt-2">
                                                    Belum ada pengajuan pinjaman.
                                                </div>

                                                <div class="mt-3">

                                                    <a href="{{ route('member.loans.create') }}"
                                                        class="btn btn-primary rounded-0">
                                                        <i class="mdi mdi-plus me-1"></i>
                                                        Ajukan Pinjaman
                                                    </a>

                                                </div>

                                            </div>

                                        </td>

                                    </tr>
                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>
    @endsection
