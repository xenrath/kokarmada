@extends('layout.app')

@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">

                <div class="page-title-box d-flex align-items-center">

                    <a href="{{ route('admin.accounts.index') }}" class="btn btn-light rounded-0 me-3">
                        <i class="mdi mdi-arrow-left"></i>
                    </a>

                    <h4 class="page-title mb-0">
                        Tetapkan Saldo Awal
                    </h4>

                </div>

            </div>
        </div>

        <div class="card rounded-0">

            <div class="card-body">
                <h4 class="header-title mb-3">
                    {{ $account->name }}
                </h4>

                <div class="alert alert-warning rounded-0">
                    Rekening ini sudah memiliki transaksi sebelum sistem saldo awal diterapkan.
                    Saldo awal harus ditetapkan satu kali untuk mengaktifkan perhitungan saldo.
                </div>
            </div>

            <div class="card-body border-top">

                <form method="POST" action="{{ route('admin.accounts.initialize-opening-balance', $account) }}">

                    @csrf
                    @method('PATCH')

                    <div class="mb-3">

                        <label for="opening_balance" class="form-label">
                            Saldo Awal
                        </label>

                        <input type="number" id="opening_balance" name="opening_balance"
                            class="form-control rounded-0 @error('opening_balance') is-invalid @enderror"
                            value="{{ old('opening_balance', $account->opening_balance) }}" min="0" step="0.01"
                            required>

                        @error('opening_balance')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="form-text">
                            Nilai ini akan menjadi saldo awal rekening dan tidak dapat diubah
                            setelah proses inisialisasi selesai.
                        </div>

                    </div>

                    <div class="border-top pt-3 mt-2">

                        <button type="submit" class="btn btn-primary rounded-0">
                            <i class="mdi mdi-content-save me-1"></i>
                            Simpan Saldo Awal
                        </button>

                        <a href="{{ route('admin.accounts.index') }}" class="btn btn-light rounded-0 ms-1">
                            Batal
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>
@endsection
