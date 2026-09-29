@extends('layout.app')

@section('title', 'Dokumen Kredit')

@section('content')
    <div class="container-fluid">

        <div class="page-title-box d-flex align-items-center">
            <a href="{{ route('analyst-manager.loans.show', $loan) }}" class="btn btn-light rounded-0 me-3">
                <i class="mdi mdi-arrow-left"></i>
            </a>

            <h4 class="page-title mb-0">
                Dokumen Kredit
            </h4>
        </div>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="alert alert-success rounded-0">
                <i class="mdi mdi-check-circle-outline me-1"></i>
                {{ session('success') }}
            </div>
        @endif

        {{-- Error Message --}}
        @if (session('error'))
            <div class="alert alert-danger rounded-0">
                <i class="mdi mdi-alert-circle-outline me-1"></i>
                {{ session('error') }}
            </div>
        @endif


        {{-- INFORMASI PINJAMAN --}}
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


        {{-- DOKUMEN KREDIT --}}
        <div class="card rounded-0">

            <div class="card-body">
                <h4 class="header-title">
                    Dokumen Kredit
                </h4>
            </div>

            <div class="card-body border-top">

                @php
                    $approvalLetter = $loan->documents->firstWhere('document_type', 'approval_letter');

                    $creditAgreement = $loan->documents->firstWhere('document_type', 'credit_agreement');

                    $documentsGenerated =
                        $approvalLetter &&
                        $approvalLetter->generated_file_path &&
                        $creditAgreement &&
                        $creditAgreement->generated_file_path;

                    $hasSignedDocument =
                        ($approvalLetter && $approvalLetter->file_path) ||
                        ($creditAgreement && $creditAgreement->file_path);
                @endphp


                {{-- GENERATE DOKUMEN --}}
                @if (!$documentsGenerated && !$hasSignedDocument)
                    <div class="alert alert-info rounded-0">
                        <div class="d-flex align-items-start">

                            <i class="mdi mdi-information-outline me-2 mt-1"></i>

                            <div>
                                <strong>Dokumen Kredit Belum Dibuat</strong>

                                <p class="mb-0 mt-1">
                                    Klik tombol di bawah untuk membuat Surat
                                    Persetujuan Kredit dan Surat Perjanjian Kredit.
                                </p>
                            </div>

                        </div>
                    </div>

                    <form action="{{ route('analyst-manager.loans.documents.generate', $loan) }}" method="POST">
                        @csrf

                        <button type="submit" class="btn btn-primary rounded-0">
                            <i class="mdi mdi-file-document-multiple-outline me-1"></i>
                            Generate Dokumen Kredit
                        </button>
                    </form>
                @endif


                {{-- SURAT PERSETUJUAN --}}
                <div class="border-bottom pb-4 mb-4">

                    <div class="d-flex align-items-start justify-content-between">

                        <div>
                            <h5 class="mb-1">
                                Surat Persetujuan Kredit
                            </h5>

                            <p class="text-muted mb-0">
                                Surat persetujuan berdasarkan keputusan
                                Chairman sebelum dokumen ditandatangani oleh anggota.
                            </p>
                        </div>

                        @if ($approvalLetter && $approvalLetter->generated_file_path)
                            <span class="badge bg-success rounded-0">
                                Sudah Dibuat
                            </span>
                        @else
                            <span class="badge bg-secondary rounded-0">
                                Belum Dibuat
                            </span>
                        @endif

                    </div>


                    @if ($approvalLetter && $approvalLetter->generated_file_path)
                        <div class="d-flex flex-wrap gap-2 mt-3">

                            <a href="{{ route('analyst-manager.loans.documents.view', [$loan, $approvalLetter]) }}"
                                target="_blank" class="btn btn-light rounded-0">
                                <i class="mdi mdi-file-pdf-box me-1"></i>
                                Lihat PDF
                            </a>

                            <a href="{{ route('analyst-manager.loans.documents.download', [$loan, $approvalLetter]) }}"
                                class="btn btn-primary rounded-0">
                                <i class="mdi mdi-download me-1"></i>
                                Download
                            </a>

                        </div>
                    @else
                        <p class="text-muted mb-0 mt-3">
                            Dokumen belum dibuat.
                        </p>
                    @endif

                </div>


                {{-- SURAT PERJANJIAN --}}
                <div>

                    <div class="d-flex align-items-start justify-content-between">

                        <div>
                            <h5 class="mb-1">
                                Surat Perjanjian Kredit
                            </h5>

                            <p class="text-muted mb-0">
                                Perjanjian kredit antara KOPKARMADA
                                dan anggota sebagai peminjam.
                            </p>
                        </div>

                        @if ($creditAgreement && $creditAgreement->generated_file_path)
                            <span class="badge bg-success rounded-0">
                                Sudah Dibuat
                            </span>
                        @else
                            <span class="badge bg-secondary rounded-0">
                                Belum Dibuat
                            </span>
                        @endif

                    </div>


                    @if ($creditAgreement && $creditAgreement->generated_file_path)
                        <div class="d-flex flex-wrap gap-2 mt-3">

                            <a href="{{ route('analyst-manager.loans.documents.view', [$loan, $creditAgreement]) }}"
                                target="_blank" class="btn btn-light rounded-0">
                                <i class="mdi mdi-file-pdf-box me-1"></i>
                                Lihat PDF
                            </a>

                            <a href="{{ route('analyst-manager.loans.documents.download', [$loan, $creditAgreement]) }}"
                                class="btn btn-primary rounded-0">
                                <i class="mdi mdi-download me-1"></i>
                                Download
                            </a>

                        </div>
                    @else
                        <p class="text-muted mb-0 mt-3">
                            Dokumen belum dibuat.
                        </p>
                    @endif

                </div>


            </div>


            {{-- UPLOAD DOKUMEN BERTANDA TANGAN --}}
            @if ($documentsGenerated)

                <div class="card-body border-top">

                    <h5 class="mb-3">
                        Upload Dokumen Bertanda Tangan
                    </h5>

                    <p class="text-muted mb-3">
                        Setelah dokumen dicetak dan ditandatangani oleh anggota,
                        unggah hasil scan kedua dokumen untuk dilanjutkan ke tahap
                        validasi Secretary.
                    </p>


                    <form action="{{ route('analyst-manager.loans.documents.upload', $loan) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf


                        {{-- SURAT PERSETUJUAN --}}
                        <div class="mb-4">

                            <label class="form-label">
                                Surat Persetujuan Kredit
                            </label>

                            @if ($approvalLetter->file_path)
                                <div class="mb-2">
                                    <span class="badge bg-info rounded-0">
                                        Sudah Diunggah
                                    </span>
                                </div>
                            @endif

                            <input type="file" name="documents[{{ $approvalLetter->id }}]"
                                class="form-control rounded-0" accept=".pdf,.jpg,.jpeg,.png">

                        </div>


                        {{-- SURAT PERJANJIAN --}}
                        <div class="mb-4">

                            <label class="form-label">
                                Surat Perjanjian Kredit
                            </label>

                            @if ($creditAgreement->file_path)
                                <div class="mb-2">
                                    <span class="badge bg-info rounded-0">
                                        Sudah Diunggah
                                    </span>
                                </div>
                            @endif

                            <input type="file" name="documents[{{ $creditAgreement->id }}]"
                                class="form-control rounded-0" accept=".pdf,.jpg,.jpeg,.png">

                        </div>


                        <button type="submit" class="btn btn-primary rounded-0">
                            <i class="mdi mdi-upload me-1"></i>
                            Upload Dokumen
                        </button>

                    </form>

                </div>

            @endif

        </div>

    </div>
@endsection
