@extends('layout.app')

@section('title', 'Pengajuan Pinjaman')

@section('content')
    <div class="container-fluid">

        {{-- Page Title --}}
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">KOPKARMADA</li>
                            <li class="breadcrumb-item active">
                                Pengajuan Pinjaman
                            </li>
                        </ol>
                    </div>

                    <h4 class="page-title">
                        Pengajuan Pinjaman
                    </h4>
                </div>
            </div>
        </div>

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

        <div class="card rounded-0 mb-3">

            <div class="card-body border-bottom">

                <h4 class="header-title mb-1">
                    Daftar Pengajuan
                </h4>

                <p class="text-muted mb-0">
                    Daftar pengajuan pinjaman yang sedang dalam proses.
                </p>

            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-centered mb-0 align-middle">

                        <thead class="table-light">
                            <tr>
                                <th width="50">No</th>
                                <th>Kode</th>
                                <th>Anggota</th>
                                <th>Tanggal</th>
                                <th>Nominal</th>
                                <th>Status</th>
                                <th width="150">Tindakan</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($loans as $loan)
                                <tr>

                                    <td class="align-top">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="align-top">
                                        <strong>
                                            {{ $loan->code }}
                                        </strong>
                                    </td>

                                    <td class="align-top">

                                        {{ $loan->user->name }}

                                        <br>

                                        <small class="text-muted">
                                            {{ $loan->user->memberProfile?->member_number ?? '-' }}
                                        </small>

                                    </td>

                                    <td class="align-top">
                                        {{ $loan->submitted_at ? \Carbon\Carbon::parse($loan->submitted_at)->translatedFormat('d F Y H:i') : '-' }}
                                    </td>

                                    <td class="align-top">
                                        <strong>
                                            Rp{{ number_format($loan->requested_amount, 0, ',', '.') }}
                                        </strong>
                                    </td>

                                    <td class="align-top">

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

                                    </td>

                                    <td class="align-top">
                                        @if (in_array($loan->status, ['submitted', 'under_analysis']))
                                            <a href="{{ route('analyst-manager.loans.show', $loan) }}"
                                                class="btn btn-sm btn-primary rounded-0">
                                                Analisis
                                            </a>
                                        @elseif ($loan->status === 'approved')
                                            <a href="{{ route('analyst-manager.loans.documents', $loan) }}"
                                                class="btn btn-sm btn-primary rounded-0">
                                                Dokumen
                                            </a>
                                        @else
                                            <a href="{{ route('analyst-manager.loans.show', $loan) }}"
                                                class="btn btn-sm btn-light rounded-0">
                                                Lihat
                                            </a>
                                        @endif
                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="7" class="text-center text-muted py-5">

                                        Belum ada pengajuan pinjaman.

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
