@extends('layout.app')

@section('content')
    <div class="container-fluid">

        <div class="page-title-box d-flex align-items-center">
            <h4 class="page-title mb-0">Profil Saya</h4>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card rounded-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="header-title mb-0">
                                Data Profil
                            </h4>

                            <a href="{{ route('member.profile.edit') }}" class="btn btn-primary rounded-0">
                                <i class="mdi mdi-pencil me-1"></i>
                                Edit Profil
                            </a>
                        </div>
                    </div>

                    <div class="card-body border-top">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label text-muted">
                                    Nomor Anggota
                                </label>
                                <div>
                                    {{ $user->memberProfile?->member_number ?? '-' }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted">
                                    Nama Lengkap
                                </label>
                                <div>{{ $user->name }}</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted">
                                    Nomor HP / WhatsApp
                                </label>
                                <div>{{ $user->phone }}</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted">
                                    Jenis Kelamin
                                </label>
                                <div>
                                    @if ($user->gender === 'L')
                                        Laki-laki
                                    @elseif ($user->gender === 'P')
                                        Perempuan
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted">
                                    NIK
                                </label>
                                <div>
                                    {{ $user->memberProfile?->national_id ?? '-' }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted">
                                    Masa Berlaku KTP
                                </label>
                                <div>
                                    @if ($user->memberProfile?->national_id_expiry)
                                        {{ \Carbon\Carbon::parse($user->memberProfile->national_id_expiry)->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted">
                                    Tempat Lahir
                                </label>
                                <div>
                                    {{ $user->memberProfile?->birth_place ?? '-' }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted">
                                    Tanggal Lahir
                                </label>
                                <div>
                                    @if ($user->memberProfile?->birth_date)
                                        {{ \Carbon\Carbon::parse($user->memberProfile->birth_date)->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label text-muted">
                                    Alamat
                                </label>
                                <div>
                                    {{ $user->memberProfile?->address ?? '-' }}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-muted">
                                    Kode Pos
                                </label>
                                <div>
                                    {{ $user->memberProfile?->postal_code ?? '-' }}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-muted">
                                    Pekerjaan
                                </label>
                                <div>
                                    {{ $user->memberProfile?->occupation ?? '-' }}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-muted">
                                    NPWP
                                </label>
                                <div>
                                    {{ $user->memberProfile?->tax_id ?? '-' }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted">
                                    Nama Ibu Kandung
                                </label>
                                <div>
                                    {{ $user->memberProfile?->mother_name ?? '-' }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted">
                                    Status Pernikahan
                                </label>
                                <div>
                                    {{ $user->memberProfile?->marital_status ?? '-' }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted">
                                    Nama Pasangan
                                </label>
                                <div>
                                    {{ $user->memberProfile?->spouse_name ?? '-' }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted">
                                    Pekerjaan Pasangan
                                </label>
                                <div>
                                    {{ $user->memberProfile?->spouse_occupation ?? '-' }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted">
                                    Nama Bank
                                </label>
                                <div>
                                    {{ $user->memberProfile?->bank_name ?? '-' }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-muted">
                                    Nomor Rekening
                                </label>
                                <div>
                                    {{ $user->memberProfile?->bank_account_number ?? '-' }}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
