@extends('layout.app')

@section('title', 'Validasi Dokumen')

@section('content')

    <div class="container-fluid">

        {{-- Page Title --}}
        <div class="page-title-box d-flex align-items-center">

            <a href="{{ route('secretary.loans.index') }}" class="btn btn-light rounded-0 me-3">
                <i class="mdi mdi-arrow-left"></i>
            </a>

            <h4 class="page-title mb-0">
                Validasi Dokumen
            </h4>

        </div>


        {{-- Success --}}
        @if (session('success'))
            <div class="alert alert-success rounded-0">
                <i class="mdi mdi-check-circle-outline me-1"></i>
                {{ session('success') }}
            </div>
        @endif


        {{-- Error --}}
        @if (session('error'))
            <div class="alert alert-danger rounded-0">
                <i class="mdi mdi-alert-circle-outline me-1"></i>
                {{ session('error') }}
            </div>
        @endif


        {{-- Informasi Pinjaman --}}
        <div class="card rounded-0 mb-3">

            <div class="card-body">

                <h4 class="header-title">
                    Informasi Pinjaman
                </h4>

            </div>

            <div class="card-body border-top">

                <div class="row">

                    <div class="col-md-3 mb-3 mb-md-0">

                        <p class="text-muted mb-1">
                            Kode Pinjaman
                        </p>

                        <h5 class="mb-0">
                            {{ $loan->code }}
                        </h5>

                    </div>


                    <div class="col-md-3 mb-3 mb-md-0">

                        <p class="text-muted mb-1">
                            Anggota
                        </p>

                        <h5 class="mb-0">
                            {{ $loan->user->name }}
                        </h5>

                    </div>


                    <div class="col-md-3 mb-3 mb-md-0">

                        <p class="text-muted mb-1">
                            Nominal Disetujui
                        </p>

                        <h5 class="mb-0">
                            Rp{{ number_format($loan->approved_amount, 0, ',', '.') }}
                        </h5>

                    </div>


                    <div class="col-md-3">

                        <p class="text-muted mb-1">
                            Cicilan
                        </p>

                        <h5 class="mb-0">

                            @if ($loan->repayment_type === 'monthly')
                                Rp{{ number_format($loan->monthly_installment, 0, ',', '.') }}
                                / bulan
                            @else
                                Lump Sum
                            @endif

                        </h5>

                    </div>

                </div>

            </div>

        </div>


        {{-- Dokumen --}}
        <div class="card rounded-0">

            <div class="card-body">

                <h4 class="header-title">
                    Pemeriksaan Dokumen
                </h4>

                <p class="text-muted mb-0">
                    Periksa dokumen hasil generate dan scan bertanda
                    tangan sebelum melakukan validasi.
                </p>

            </div>


            <div class="card-body border-top">

                @php
                    $approvalLetter = $loan->documents->firstWhere('document_type', 'approval_letter');

                    $creditAgreement = $loan->documents->firstWhere('document_type', 'credit_agreement');

                    $documents = [
                        [
                            'document' => $approvalLetter,
                            'title' => 'Surat Persetujuan Kredit',
                            'description' => 'Surat persetujuan kredit berdasarkan keputusan Ketua.',
                        ],
                        [
                            'document' => $creditAgreement,
                            'title' => 'Surat Perjanjian Kredit',
                            'description' => 'Perjanjian kredit antara koperasi dan anggota.',
                        ],
                    ];
                @endphp


                @foreach ($documents as $item)
                    @php
                        $document = $item['document'];
                    @endphp

                    @if ($document)
                        <div class="mb-4">

                            <div class="d-flex justify-content-between align-items-start mb-3">

                                <div>

                                    <h5 class="mb-1">
                                        {{ $item['title'] }}
                                    </h5>

                                    <p class="text-muted mb-0">
                                        {{ $item['description'] }}
                                    </p>

                                </div>


                                @if ($document->status === 'verified')
                                    <span class="badge bg-success rounded-0">
                                        Terverifikasi
                                    </span>
                                @elseif ($document->status === 'rejected')
                                    <span class="badge bg-danger rounded-0">
                                        Perlu Diperbaiki
                                    </span>
                                @else
                                    <span class="badge bg-warning rounded-0">
                                        Menunggu Validasi
                                    </span>
                                @endif

                            </div>


                            <div class="row">

                                {{-- Generated --}}
                                <div class="col-md-6 mb-3">

                                    <div class="border p-3 rounded-0 h-100">

                                        <div class="d-flex align-items-center mb-3">

                                            <i class="mdi mdi-file-pdf-box font-24 text-primary me-2"></i>

                                            <div>

                                                <h5 class="mb-0">
                                                    Dokumen Generated
                                                </h5>

                                                <small class="text-muted">
                                                    Dokumen asli dari sistem
                                                </small>

                                            </div>

                                        </div>


                                        @if ($document->generated_file_path)
                                            <a href="{{ route('secretary.loans.documents.generated', [$loan, $document]) }}"
                                                target="_blank" class="btn btn-light rounded-0">
                                                <i class="mdi mdi-eye me-1"></i>
                                                Lihat PDF
                                            </a>
                                        @else
                                            <span class="text-muted">
                                                Dokumen generated belum tersedia.
                                            </span>
                                        @endif

                                    </div>

                                </div>


                                {{-- Signed --}}
                                <div class="col-md-6 mb-3">

                                    <div class="border p-3 rounded-0 h-100">

                                        <div class="d-flex align-items-center mb-3">

                                            <i class="mdi mdi-file-document-check-outline font-24 text-success me-2"></i>

                                            <div>

                                                <h5 class="mb-0">
                                                    Dokumen Bertanda Tangan
                                                </h5>

                                                <small class="text-muted">
                                                    Hasil scan dokumen fisik
                                                </small>

                                            </div>

                                        </div>


                                        @if ($document->file_path)
                                            <a href="{{ route('secretary.loans.documents.signed', [$loan, $document]) }}"
                                                target="_blank" class="btn btn-light rounded-0">
                                                <i class="mdi mdi-eye me-1"></i>
                                                Lihat Scan
                                            </a>
                                        @else
                                            <span class="text-muted">
                                                Scan bertanda tangan belum tersedia.
                                            </span>
                                        @endif

                                    </div>

                                </div>

                            </div>


                            {{-- Aksi Validasi --}}
                            @if ($document->status === 'pending' && $document->file_path)
                                <div class="border-top pt-3">

                                    <div class="d-flex flex-wrap gap-2">

                                        <form
                                            action="{{ route('secretary.loans.documents.verify', [$loan, $document]) }}"
                                            method="POST">

                                            @csrf

                                            <button type="submit" class="btn btn-success rounded-0">
                                                <i class="mdi mdi-check me-1"></i>
                                                Validasi Dokumen
                                            </button>

                                        </form>


                                        <button type="button" class="btn btn-danger rounded-0" data-bs-toggle="modal"
                                            data-bs-target="#rejectModal{{ $document->id }}">
                                            <i class="mdi mdi-close me-1"></i>
                                            Perlu Diperbaiki
                                        </button>

                                    </div>

                                </div>
                            @elseif ($document->status === 'verified')
                                <div class="border-top pt-3">

                                    <p class="text-success mb-0">

                                        <i class="mdi mdi-check-circle-outline me-1"></i>

                                        Dokumen telah divalidasi oleh
                                        {{ $document->verifier->name ?? 'Sekretaris' }}.

                                    </p>

                                </div>
                            @elseif ($document->status === 'rejected')
                                <div class="border-top pt-3">

                                    <p class="text-danger mb-0">

                                        <i class="mdi mdi-alert-circle-outline me-1"></i>

                                        Dokumen perlu diperbaiki dan diunggah kembali.

                                    </p>

                                </div>
                            @endif

                        </div>


                        @if (!$loop->last)
                            <hr>
                        @endif


                        {{-- Modal Tolak --}}
                        <div class="modal fade" id="rejectModal{{ $document->id }}" tabindex="-1" aria-hidden="true">

                            <div class="modal-dialog">

                                <div class="modal-content rounded-0">

                                    <div class="modal-header">

                                        <h5 class="modal-title">
                                            Dokumen Perlu Diperbaiki
                                        </h5>

                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>

                                    </div>


                                    <form
                                        action="{{ route('secretary.loans.documents.reject', [$loan, $document]) }}"
                                        method="POST">

                                        @csrf

                                        <div class="modal-body">

                                            <p class="text-muted">
                                                Berikan catatan yang jelas agar
                                                Analyst Manager dapat memperbaiki
                                                dokumen yang bersangkutan.
                                            </p>

                                            <div>

                                                <label class="form-label">
                                                    Catatan Perbaikan
                                                </label>

                                                <textarea name="notes" rows="4" class="form-control rounded-0" required></textarea>

                                            </div>

                                        </div>


                                        <div class="modal-footer">

                                            <button type="button" class="btn btn-light rounded-0"
                                                data-bs-dismiss="modal">
                                                Batal
                                            </button>

                                            <button type="submit" class="btn btn-danger rounded-0">
                                                Tandai Perlu Diperbaiki
                                            </button>

                                        </div>

                                    </form>

                                </div>

                            </div>

                        </div>
                    @endif
                @endforeach

            </div>

        </div>

    </div>

@endsection
