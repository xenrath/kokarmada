@extends('layout.app')

@section('content')
    <div class="container-fluid">

        <div class="page-title-box">
            <h4 class="page-title">Angsuran</h4>
        </div>

        <div class="card rounded-0">
            <div class="card-body">
                <h4 class="header-title">Daftar Pinjaman Dicairkan</h4>
            </div>

            <div class="card-body border-top">
                <div class="table-responsive">
                    <table class="table table-centered table-bordered table-nowrap mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Pinjaman</th>
                                <th>Anggota</th>
                                <th>Jumlah Pinjaman</th>
                                <th>Angsuran</th>
                                <th>Dibayar</th>
                                <th>Sisa</th>
                                <th>Status</th>
                                <th width="80">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($loans as $loan)
                                @php
                                    $totalInstallments = $loan->installments->count();

                                    $paidInstallments = $loan->installments->where('status', 'paid')->count();

                                    $remainingInstallments = $totalInstallments - $paidInstallments;
                                @endphp

                                <tr>
                                    <td>
                                        {{ $loans->firstItem() + $loop->index }}
                                    </td>

                                    <td>
                                        {{ $loan->code }}
                                    </td>

                                    <td>
                                        {{ $loan->user->memberProfile->member_number ?? '-' }}<br>
                                        <small class="text-muted">
                                            {{ $loan->user->name }}
                                        </small>
                                    </td>

                                    <td>
                                        Rp {{ number_format($loan->approved_amount, 0, ',', '.') }}
                                    </td>

                                    <td>
                                        {{ $totalInstallments }}
                                    </td>

                                    <td>
                                        {{ $paidInstallments }}
                                    </td>

                                    <td>
                                        {{ $remainingInstallments }}
                                    </td>

                                    <td>
                                        @if ($remainingInstallments === 0 && $totalInstallments > 0)
                                            <span class="badge bg-success">
                                                Lunas
                                            </span>
                                        @else
                                            <span class="badge bg-warning">
                                                Berjalan
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        <a href="{{ route('treasurer.installments.show', $loan) }}"
                                            class="btn btn-light btn-sm rounded-0" title="Lihat Angsuran">
                                            <i class="mdi mdi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        Belum ada pinjaman yang memiliki pencairan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $loans->links() }}
                </div>
            </div>
        </div>

    </div>
@endsection
