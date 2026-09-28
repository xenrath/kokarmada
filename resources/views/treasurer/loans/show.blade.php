@extends('layout.app')

@section('content')

    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4>Review Pengajuan</h4>
                <div class="text-muted">{{ $loan->code }}</div>
            </div>

            <a href="{{ route('treasurer.loans.index') }}" class="btn btn-light">
                Kembali
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">

            {{-- DATA ANGGOTA --}}
            <div class="col-lg-6">

                <div class="card mb-3">
                    <div class="card-body">

                        <h5 class="mb-3">
                            Data Anggota
                        </h5>

                        <table class="table table-sm">

                            <tr>
                                <th width="40%">Nama</th>
                                <td>{{ $loan->user->name }}</td>
                            </tr>

                            <tr>
                                <th>No. Anggota</th>
                                <td>
                                    {{ $loan->user->memberProfile?->member_number ?? '-' }}
                                </td>
                            </tr>

                            <tr>
                                <th>No. Telepon</th>
                                <td>{{ $loan->user->phone }}</td>
                            </tr>

                            <tr>
                                <th>Unit Kerja</th>
                                <td>{{ $loan->work_unit }}</td>
                            </tr>

                            <tr>
                                <th>Jabatan</th>
                                <td>{{ $loan->position }}</td>
                            </tr>

                            <tr>
                                <th>Lama Bekerja</th>
                                <td>
                                    {{ $loan->employment_duration_years }} tahun
                                </td>
                            </tr>

                        </table>

                    </div>
                </div>

            </div>

            {{-- DATA PINJAMAN --}}
            <div class="col-lg-6">

                <div class="card mb-3">
                    <div class="card-body">

                        <h5 class="mb-3">
                            Data Pinjaman
                        </h5>

                        <table class="table table-sm">

                            <tr>
                                <th width="40%">Pengajuan</th>
                                <td>
                                    <strong>
                                        Rp{{ number_format($loan->requested_amount, 0, ',', '.') }}
                                    </strong>
                                </td>
                            </tr>

                            <tr>
                                <th>Jenis</th>
                                <td>
                                    {{ $loan->repayment_type === 'monthly' ? 'Bulanan' : 'Lump Sum' }}
                                </td>
                            </tr>

                            <tr>
                                <th>Jangka Waktu</th>
                                <td>
                                    {{ $loan->term_months }} bulan
                                </td>
                            </tr>

                            <tr>
                                <th>Tujuan</th>
                                <td>
                                    {{ $loan->purpose_category }}
                                </td>
                            </tr>

                            <tr>
                                <th>Penghasilan Bersih</th>
                                <td>
                                    Rp{{ number_format($loan->net_monthly_income ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>

                            <tr>
                                <th>Penghasilan Lain</th>
                                <td>
                                    Rp{{ number_format($loan->other_monthly_income ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>

                        </table>

                    </div>
                </div>

            </div>

        </div>

        {{-- HASIL ANALYST --}}
        <div class="card mb-3">

            <div class="card-body">

                <h5 class="mb-3">
                    Rekomendasi Analyst Manager
                </h5>

                @if ($loan->analysis)
                    <table class="table table-sm mb-0">

                        <tr>
                            <th width="25%">
                                Nominal Rekomendasi
                            </th>

                            <td>
                                Rp{{ number_format($loan->analysis->recommended_amount, 0, ',', '.') }}
                            </td>
                        </tr>

                        <tr>
                            <th>Catatan</th>

                            <td>
                                {!! nl2br(e($loan->analysis->notes)) !!}
                            </td>
                        </tr>

                    </table>
                @else
                    <div class="alert alert-warning mb-0">
                        Data analisis Analyst Manager belum tersedia.
                    </div>
                @endif

            </div>

        </div>

        {{-- FORM TREASURER --}}
        <div class="card">

            <div class="card-body">

                <h5 class="mb-3">
                    Review Treasurer
                </h5>

                <form method="POST" action="{{ route('treasurer.loans.review', $loan) }}">

                    @csrf

                    <div class="mb-3">

                        <label class="form-label">
                            Nominal Rekomendasi Treasurer
                        </label>

                        <input type="number" name="recommended_amount" class="form-control" min="1"
                            max="{{ $loan->requested_amount }}"
                            value="{{ old('recommended_amount', $loan->treasurerReview?->recommended_amount) }}"
                            required>

                        <small class="text-muted">
                            Maksimal
                            Rp{{ number_format($loan->requested_amount, 0, ',', '.') }}
                        </small>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Catatan Review
                        </label>

                        <textarea name="notes" rows="5" class="form-control" required>{{ old('notes', $loan->treasurerReview?->notes) }}</textarea>

                    </div>

                    <button type="submit" class="btn btn-primary">
                        Simpan Review & Teruskan ke Chairman
                    </button>

                </form>

            </div>

        </div>

    </div>

@endsection
