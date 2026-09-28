@extends('layout.app')

@section('title', 'Validasi Dokumen')

@section('content')

    <div class="container-fluid">

        {{-- Page Title --}}
        <div class="page-title-box d-flex align-items-center">

            <a href="{{ route('secretary.home') }}" class="btn btn-light rounded-0 me-3">
                <i class="mdi mdi-arrow-left"></i>
            </a>

            <h4 class="page-title mb-0">
                Validasi Dokumen
            </h4>

        </div>


        {{-- Informasi --}}
        <div class="card rounded-0 mb-3">

            <div class="card-body">

                <h4 class="header-title mb-1">
                    Dokumen Menunggu Validasi
                </h4>

                <p class="text-muted mb-0">
                    Berikut pengajuan pinjaman yang dokumen bertanda
                    tangannya telah diunggah oleh Manajer Analis.
                </p>

            </div>

        </div>


        {{-- Daftar Pengajuan --}}
        <div class="card rounded-0">

            <div class="card-body">

                <h4 class="header-title mb-3">
                    Daftar Pengajuan
                </h4>

                <div class="table-responsive">

                    <table class="table table-centered mb-0">

                        <thead>
                            <tr>
                                <th>Kode Pinjaman</th>
                                <th>Anggota</th>
                                <th>Nominal Disetujui</th>
                                <th>Dokumen</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($loans as $loan)
                                @php
                                    $documents = $loan->documents->whereIn('document_type', [
                                        'approval_letter',
                                        'credit_agreement',
                                    ]);

                                    $pendingCount = $documents
                                        ->where('status', 'pending')
                                        ->whereNotNull('file_path')
                                        ->count();
                                @endphp

                                <tr>

                                    <td class="align-middle">
                                        <strong>
                                            {{ $loan->code }}
                                        </strong>
                                    </td>


                                    <td class="align-middle">
                                        {{ $loan->user->name }}
                                    </td>


                                    <td class="align-middle">
                                        Rp{{ number_format($loan->approved_amount, 0, ',', '.') }}
                                    </td>


                                    <td class="align-middle">

                                        <span class="badge bg-warning rounded-0">
                                            {{ $pendingCount }}
                                            dokumen
                                        </span>

                                    </td>


                                    <td class="align-middle">

                                        <span class="badge bg-secondary rounded-0">
                                            Menunggu Validasi
                                        </span>

                                    </td>


                                    <td class="align-middle text-end">

                                        <a href="{{ route('secretary.loans.show', $loan) }}"
                                            class="btn btn-primary btn-sm rounded-0">
                                            <i class="mdi mdi-eye me-1"></i>
                                            Periksa
                                        </a>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="6" class="text-center py-5">

                                        <i class="mdi mdi-file-check-outline font-36 text-muted"></i>

                                        <h5 class="mt-3">
                                            Tidak Ada Dokumen
                                        </h5>

                                        <p class="text-muted mb-0">
                                            Belum ada dokumen kredit yang
                                            menunggu validasi.
                                        </p>

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
