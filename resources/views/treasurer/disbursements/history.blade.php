@extends('layout.app')

@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">

                <div class="page-title-box d-flex align-items-center">

                    <h4 class="page-title mb-0">
                        Riwayat Pencairan
                    </h4>

                </div>

            </div>
        </div>

        <div class="card rounded-0">

            <div class="card-body">
                <h4 class="header-title">
                    Daftar Pencairan
                </h4>
            </div>

            <div class="card-body border-top">

                <div class="table-responsive">

                    <table class="table table-centered table-nowrap mb-0">

                        <thead>
                            <tr>
                                <th>Kode Pinjaman</th>
                                <th>Anggota</th>
                                <th>Nominal</th>
                                <th>Rekening Sumber</th>
                                <th>Tanggal Pencairan</th>
                                <th>Treasurer</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($loans as $loan)
                                <tr>

                                    <td>
                                        <strong>
                                            {{ $loan->code }}
                                        </strong>
                                    </td>

                                    <td>
                                        {{ $loan->user->name }}
                                    </td>

                                    <td>
                                        Rp {{ number_format($loan->disbursement->amount, 0, ',', '.') }}
                                    </td>

                                    <td>
                                        {{ $loan->disbursement->account->name }}

                                        @if ($loan->disbursement->account->bank_name)
                                            <small class="text-muted d-block">
                                                {{ $loan->disbursement->account->bank_name }}
                                            </small>
                                        @endif
                                    </td>

                                    <td>
                                        {{ \Carbon\Carbon::parse($loan->disbursement->disbursed_at)->format('d/m/Y') }}
                                    </td>

                                    <td>
                                        {{ $loan->disbursement->treasurer->name }}
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Belum ada riwayat pencairan.
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
