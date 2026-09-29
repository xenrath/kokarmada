@extends('layout.app')

@section('content')
    <div class="container-fluid">

        <div class="page-title-box d-flex align-items-center">
            <a href="{{ route('treasurer.installments.index') }}" class="btn btn-light rounded-0 me-3">
                <i class="mdi mdi-arrow-left"></i>
            </a>

            <h4 class="page-title mb-0">Detail Angsuran</h4>
        </div>

        <div class="row">

            <div class="col-lg-5">

                <div class="card rounded-0">
                    <div class="card-body">
                        <h4 class="header-title">Informasi Pinjaman</h4>
                    </div>

                    <div class="card-body border-top">

                        <div class="row mb-3">
                            <div class="col-5 text-muted">Kode</div>
                            <div class="col-7">
                                {{ $loan->code }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-5 text-muted">Anggota</div>
                            <div class="col-7">
                                {{ $loan->user->name }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-5 text-muted">Jumlah Pinjaman</div>
                            <div class="col-7">
                                Rp {{ number_format($loan->approved_amount, 0, ',', '.') }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-5 text-muted">Bunga</div>
                            <div class="col-7">
                                {{ $loan->interest_rate }}%
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-5 text-muted">Tenor</div>
                            <div class="col-7">
                                {{ $loan->term_months }} bulan
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-5 text-muted">Angsuran / Bulan</div>
                            <div class="col-7">
                                @if ($loan->monthly_installment)
                                    Rp {{ number_format($loan->monthly_installment, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-5 text-muted">Status</div>
                            <div class="col-7">
                                @if ($loan->status === 'paid_off')
                                    <span class="badge bg-success">
                                        Lunas
                                    </span>
                                @elseif ($loan->status === 'disbursed')
                                    <span class="badge bg-info">
                                        Berjalan
                                    </span>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <div class="col-lg-7">

                <div class="card rounded-0">
                    <div class="card-body">
                        <h4 class="header-title">Jadwal Angsuran</h4>
                    </div>

                    <div class="card-body border-top">

                        <div class="table-responsive">
                            <table class="table table-bordered table-centered table-nowrap mb-0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Jatuh Tempo</th>
                                        <th>Pokok</th>
                                        <th>Bunga</th>
                                        <th>Denda</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($loan->installments as $installment)
                                        <tr>
                                            <td>
                                                {{ $installment->installment_number }}
                                            </td>

                                            <td>
                                                {{ $installment->due_date ? \Carbon\Carbon::parse($installment->due_date)->format('d-m-Y') : '-' }}
                                            </td>

                                            <td>
                                                Rp {{ number_format($installment->principal_amount, 0, ',', '.') }}
                                            </td>

                                            <td>
                                                Rp {{ number_format($installment->interest_amount, 0, ',', '.') }}
                                            </td>

                                            <td>
                                                Rp {{ number_format($installment->penalty_amount, 0, ',', '.') }}
                                            </td>

                                            <td>
                                                Rp {{ number_format($installment->total_amount, 0, ',', '.') }}
                                            </td>

                                            <td>
                                                @if ($installment->status === 'paid')
                                                    <span class="badge bg-success">
                                                        Lunas
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        Belum Bayar
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($installment->status !== 'paid')
                                                    <button type="button" class="btn btn-success btn-sm rounded-0"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#payInstallmentModal{{ $installment->id }}">
                                                        <i class="mdi mdi-cash"></i>
                                                    </button>
                                                @else
                                                    <span class="text-muted">
                                                        {{ $installment->paid_at ? \Carbon\Carbon::parse($installment->paid_at)->format('d-m-Y H:i') : '-' }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                        @if ($installment->status !== 'paid')
                                            <div class="modal fade" id="payInstallmentModal{{ $installment->id }}"
                                                tabindex="-1" aria-hidden="true">

                                                <div class="modal-dialog">
                                                    <div class="modal-content rounded-0">

                                                        <div class="modal-header">
                                                            <h5 class="modal-title">
                                                                Pembayaran Angsuran
                                                            </h5>

                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close">
                                                            </button>
                                                        </div>

                                                        <form method="POST"
                                                            action="{{ route('treasurer.installments.pay', $installment) }}">

                                                            @csrf

                                                            <div class="modal-body">

                                                                <div class="mb-3">
                                                                    <label class="form-label">
                                                                        Angsuran ke
                                                                    </label>

                                                                    <input type="text" class="form-control rounded-0"
                                                                        value="{{ $installment->installment_number }}"
                                                                        readonly>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label">
                                                                        Total Pembayaran
                                                                    </label>

                                                                    <input type="text" class="form-control rounded-0"
                                                                        value="Rp {{ number_format($installment->total_amount, 0, ',', '.') }}"
                                                                        readonly>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label for="account_id_{{ $installment->id }}"
                                                                        class="form-label">
                                                                        Masuk ke Akun
                                                                    </label>

                                                                    <select name="account_id"
                                                                        id="account_id_{{ $installment->id }}"
                                                                        class="form-select rounded-0" required>

                                                                        <option value="">
                                                                            -- Pilih Akun --
                                                                        </option>

                                                                        @foreach ($accounts as $account)
                                                                            <option value="{{ $account->id }}">
                                                                                {{ $account->name }}

                                                                                @if ($account->bank_name)
                                                                                    - {{ $account->bank_name }}
                                                                                @endif

                                                                                - Saldo Rp
                                                                                {{ number_format($account->available_balance, 0, ',', '.') }}
                                                                            </option>
                                                                        @endforeach

                                                                    </select>
                                                                </div>

                                                                <div class="alert alert-warning rounded-0 mb-0">
                                                                    Pastikan pembayaran angsuran benar-benar
                                                                    sudah diterima sebelum mencatat transaksi.
                                                                </div>

                                                            </div>

                                                            <div class="modal-footer">

                                                                <button type="button" class="btn btn-light rounded-0"
                                                                    data-bs-dismiss="modal">
                                                                    Batal
                                                                </button>

                                                                <button type="submit" class="btn btn-success rounded-0">
                                                                    <i class="mdi mdi-cash-check me-1"></i>
                                                                    Konfirmasi Pembayaran
                                                                </button>

                                                            </div>

                                                        </form>

                                                    </div>
                                                </div>

                                            </div>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">
                                                Belum ada jadwal angsuran.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>
@endsection
