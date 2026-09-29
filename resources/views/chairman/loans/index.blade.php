@extends('layout.app')

@section('content')
    <div class="container-fluid">

        <div class="mb-3">
            <h4>Persetujuan Pinjaman</h4>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-hover align-middle">

                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Anggota</th>
                                <th>Pengajuan</th>
                                <th>Rekomendasi Analyst</th>
                                <th>Rekomendasi Treasurer</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($loans as $loan)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        <strong>{{ $loan->code }}</strong>
                                    </td>

                                    <td>
                                        {{ $loan->user->name }}

                                        <br>

                                        <small class="text-muted">
                                            {{ $loan->user->memberProfile?->member_number ?? '-' }}
                                        </small>
                                    </td>

                                    <td>
                                        Rp{{ number_format($loan->requested_amount, 0, ',', '.') }}
                                    </td>

                                    <td>
                                        Rp{{ number_format($loan->analysis?->recommended_amount ?? 0, 0, ',', '.') }}
                                    </td>

                                    <td>
                                        Rp{{ number_format($loan->treasurerReview?->recommended_amount ?? 0, 0, ',', '.') }}
                                    </td>

                                    <td>
                                        <span class="badge bg-warning">
                                            Menunggu Persetujuan
                                        </span>
                                    </td>

                                    <td>
                                        <a href="{{ route('chairman.loans.show', $loan) }}" class="btn btn-sm btn-primary">
                                            Tinjau
                                        </a>
                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        Tidak ada pengajuan yang menunggu persetujuan.
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
