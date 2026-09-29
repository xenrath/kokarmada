@extends('layout.app')

@section('title', 'Pengaturan Pinjaman')

@section('content')
    <div class="container-fluid">

        <div class="page-title-box d-flex align-items-center">
            <a href="{{ route('admin.home') }}" class="btn btn-light rounded-0 me-3">
                <i class="mdi mdi-arrow-left"></i>
            </a>

            <h4 class="page-title mb-0">
                Pengaturan Pinjaman
            </h4>
        </div>

        @if (session('success'))
            <div class="alert alert-success rounded-0">
                <i class="mdi mdi-check-circle-outline me-1"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="card rounded-0">
            <div class="card-body">
                <h4 class="header-title mb-1">
                    Batas Pinjaman & Top Up
                </h4>

                <p class="text-muted mb-0">
                    Atur parameter yang digunakan sistem dalam proses
                    pengajuan dan analisis Top Up.
                </p>
            </div>

            <div class="card-body border-top">
                <form action="{{ route('admin.loan-settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="top_up_minimum_amount" class="form-label">
                                Minimum Top Up
                            </label>

                            <input
                                type="number"
                                id="top_up_minimum_amount"
                                name="top_up_minimum_amount"
                                class="form-control rounded-0 @error('top_up_minimum_amount') is-invalid @enderror"
                                min="1"
                                step="1"
                                value="{{ old('top_up_minimum_amount', $settings['top_up_minimum_amount']) }}"
                                required
                            >

                            <small class="text-muted">
                                Nominal tambahan minimum yang dapat diminta anggota.
                            </small>

                            @error('top_up_minimum_amount')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="loan_maximum_amount" class="form-label">
                                Maksimum Total Pinjaman
                            </label>

                            <input
                                type="number"
                                id="loan_maximum_amount"
                                name="loan_maximum_amount"
                                class="form-control rounded-0 @error('loan_maximum_amount') is-invalid @enderror"
                                min="1"
                                step="1"
                                value="{{ old('loan_maximum_amount', $settings['loan_maximum_amount']) }}"
                                required
                            >

                            <small class="text-muted">
                                Batas maksimum total pokok kontrak baru, termasuk Top Up.
                            </small>

                            @error('loan_maximum_amount')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="loan_capacity_threshold_percent" class="form-label">
                                Ambang Kapasitas Keuangan
                            </label>

                            <div class="input-group">
                                <input
                                    type="number"
                                    id="loan_capacity_threshold_percent"
                                    name="loan_capacity_threshold_percent"
                                    class="form-control rounded-0 @error('loan_capacity_threshold_percent') is-invalid @enderror"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    value="{{ old('loan_capacity_threshold_percent', $settings['loan_capacity_threshold_percent']) }}"
                                    required
                                >
                                <span class="input-group-text rounded-0">
                                    %
                                </span>
                            </div>

                            <small class="text-muted">
                                Digunakan sebagai indikator kemampuan pembayaran; bukan hard-block eligibility.
                            </small>

                            @error('loan_capacity_threshold_percent')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="border-top pt-3 mt-2 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary rounded-0">
                            <i class="mdi mdi-content-save-outline me-1"></i>
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection
