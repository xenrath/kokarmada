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
                        Tambah Rekening
                    </h4>

                </div>

            </div>
        </div>

        <div class="card rounded-0">

            <div class="card-body">
                <h4 class="header-title">
                    Data Rekening
                </h4>
            </div>

            <div class="card-body border-top">

                <form method="POST" action="{{ route('admin.accounts.store') }}">

                    @csrf

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label for="type" class="form-label">
                                Jenis Rekening
                            </label>

                            <input type="text" id="type" name="type"
                                class="form-control rounded-0 @error('type') is-invalid @enderror"
                                value="{{ old('type') }}" placeholder="Contoh: bank">

                            @error('type')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        <div class="col-md-6 mb-3">

                            <label for="name" class="form-label">
                                Nama Rekening
                            </label>

                            <input type="text" id="name" name="name"
                                class="form-control rounded-0 @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" placeholder="Contoh: Rekening Operasional KOPKARMADA">

                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        <div class="col-md-6 mb-3">

                            <label for="bank_name" class="form-label">
                                Nama Bank
                            </label>

                            <input type="text" id="bank_name" name="bank_name"
                                class="form-control rounded-0 @error('bank_name') is-invalid @enderror"
                                value="{{ old('bank_name') }}" placeholder="Contoh: Bank BRI">

                            @error('bank_name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        <div class="col-md-6 mb-3">

                            <label for="account_number" class="form-label">
                                Nomor Rekening
                            </label>

                            <input type="text" id="account_number" name="account_number"
                                class="form-control rounded-0 @error('account_number') is-invalid @enderror"
                                value="{{ old('account_number') }}">

                            @error('account_number')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        <div class="col-md-6 mb-3">

                            <label for="account_name" class="form-label">
                                Atas Nama
                            </label>

                            <input type="text" id="account_name" name="account_name"
                                class="form-control rounded-0 @error('account_name') is-invalid @enderror"
                                value="{{ old('account_name') }}">

                            @error('account_name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                        <div class="col-md-6 mb-3">

                            <label for="opening_balance" class="form-label">
                                Saldo Awal
                            </label>

                            <input type="number" id="opening_balance" name="opening_balance"
                                class="form-control rounded-0 @error('opening_balance') is-invalid @enderror"
                                value="{{ old('opening_balance', 0) }}" min="0" step="0.01">

                            <div class="form-text">
                                Default Rp0. Saldo awal dapat ditentukan saat rekening dibuat.
                            </div>

                            @error('opening_balance')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>

                    <div class="border-top pt-3 mt-2">

                        <button type="submit" class="btn btn-primary rounded-0">
                            <i class="mdi mdi-content-save me-1"></i>
                            Simpan
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
