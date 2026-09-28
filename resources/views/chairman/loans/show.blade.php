@extends('layout.app')

@section('content')

    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>
                <h4>Persetujuan Pinjaman</h4>

                <div class="text-muted">
                    {{ $loan->code }}
                </div>
            </div>

            <a href="{{ route('chairman.loans.index') }}" class="btn btn-light">
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


        {{-- PERBANDINGAN NOMINAL --}}
        <div class="card mb-3">

            <div class="card-body">

                <h5 class="mb-3">
                    Perbandingan Nominal
                </h5>

                <div class="row">

                    <div class="col-md-4">
                        <div class="border rounded p-3 text-center">

                            <div class="text-muted">
                                Pengajuan Member
                            </div>

                            <h4 class="mt-2">
                                Rp{{ number_format($loan->requested_amount, 0, ',', '.') }}
                            </h4>

                        </div>
                    </div>


                    <div class="col-md-4">

                        <div class="border rounded p-3 text-center">

                            <div class="text-muted">
                                Rekomendasi Analyst Manager
                            </div>

                            <h4 class="mt-2">
                                Rp{{ number_format($loan->analysis?->recommended_amount ?? 0, 0, ',', '.') }}
                            </h4>

                            @if ($loan->analysis)
                                <small class="text-muted">
                                    {{ $loan->analysis->analyst->name }}
                                </small>
                            @endif

                        </div>

                    </div>


                    <div class="col-md-4">

                        <div class="border rounded p-3 text-center">

                            <div class="text-muted">
                                Rekomendasi Treasurer
                            </div>

                            <h4 class="mt-2">
                                Rp{{ number_format($loan->treasurerReview?->recommended_amount ?? 0, 0, ',', '.') }}
                            </h4>

                            @if ($loan->treasurerReview)
                                <small class="text-muted">
                                    {{ $loan->treasurerReview->treasurer->name }}
                                </small>
                            @endif

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- CATATAN ANALYST --}}
        <div class="card mb-3">

            <div class="card-body">

                <h5>Catatan Analyst Manager</h5>

                @if ($loan->analysis)
                    <div class="border rounded p-3 mt-3">
                        {!! nl2br(e($loan->analysis->notes)) !!}
                    </div>
                @else
                    <div class="text-muted">
                        Tidak ada catatan.
                    </div>
                @endif

            </div>

        </div>


        {{-- CATATAN TREASURER --}}
        <div class="card mb-3">

            <div class="card-body">

                <h5>Catatan Treasurer</h5>

                @if ($loan->treasurerReview)
                    <div class="border rounded p-3 mt-3">
                        {!! nl2br(e($loan->treasurerReview->notes)) !!}
                    </div>
                @else
                    <div class="text-muted">
                        Tidak ada catatan.
                    </div>
                @endif

            </div>

        </div>


        {{-- FORM KEPUTUSAN CHAIRMAN --}}
        <div class="card">

            <div class="card-body">

                <h5 class="mb-3">
                    Keputusan Chairman
                </h5>

                <form method="POST" action="{{ route('chairman.loans.approve', $loan) }}">

                    @csrf

                    <div class="mb-3">

                        <label class="form-label">
                            Nominal Persetujuan Final
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                Rp
                            </span>

                            <input type="number" name="approved_amount" id="approved_amount" class="form-control"
                                min="1" max="{{ $loan->requested_amount }}"
                                value="{{ old('approved_amount', $loan->treasurerReview?->recommended_amount) }}"
                                required>

                        </div>

                        <small class="text-muted">
                            Maksimal sesuai nominal pengajuan member.
                        </small>

                    </div>


                    {{-- PILIH CEPAT --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Pilih dari Rekomendasi
                        </label>

                        <div class="d-flex flex-wrap gap-2">

                            <button type="button" class="btn btn-outline-secondary recommendation-btn"
                                data-amount="{{ $loan->requested_amount }}">

                                Pengajuan Member

                            </button>

                            @if ($loan->analysis)
                                <button type="button" class="btn btn-outline-secondary recommendation-btn"
                                    data-amount="{{ $loan->analysis->recommended_amount }}">

                                    Analyst Manager

                                </button>
                            @endif

                            @if ($loan->treasurerReview)
                                <button type="button" class="btn btn-outline-secondary recommendation-btn"
                                    data-amount="{{ $loan->treasurerReview->recommended_amount }}">

                                    Treasurer

                                </button>
                            @endif

                        </div>

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Catatan Chairman
                        </label>

                        <textarea name="notes" rows="4" class="form-control" placeholder="Catatan keputusan Chairman...">{{ old('notes') }}</textarea>

                    </div>


                    <button type="submit" class="btn btn-success">

                        Setujui Pinjaman

                    </button>

                </form>

            </div>

        </div>

    </div>


    <script>
        document.querySelectorAll('.recommendation-btn')
            .forEach(function(button) {

                button.addEventListener('click', function() {

                    document.getElementById('approved_amount').value =
                        this.dataset.amount;

                });

            });
    </script>

@endsection
