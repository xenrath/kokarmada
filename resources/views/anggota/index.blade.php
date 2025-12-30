@extends('layout.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Start Content-->
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Dashboard</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        @if ($user_detail_exists)
            <div class="alert alert-info text-center rounded-0" role="alert">
                <div>
                    Selamat Datang di Dashboard Anggota. Terima kasih telah melengkapi data diri Anda.
                </div>
            </div>
        @else
            <div class="alert alert-warning text-center rounded-0" role="alert">
                <div>
                    Lengkapi data diri Anda untuk kemudahan dalam proses administrasi.
                </div>
                <div class="mt-2">
                    <a href="{{ url('anggota/profile') }}" class="btn btn-info btn-sm rounded-0">
                        Lengkapi Data Diri
                        <i class="mdi mdi-account-edit"></i>
                    </a>
                </div>
            </div>
        @endif
        <div class="card mb-2 rounded-0">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <strong>Nama Lengkap</strong>
                        <br>
                        {{ $user->nama }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Panggilan</strong>
                        <br>
                        {{ $user->panggilan }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <strong>Jenis Kelamin</strong>
                        <br>
                        {{ $user->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>No. HP / WhatsApp</strong>
                        <br>
                        {{ $user->telp }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <strong>Role</strong>
                        <br>
                        <span class="badge badge-info-lighten rounded-0">
                            {{ ucfirst($user->role) }}
                        </span>
                        @if ($user->spesial != 'normal')
                            <span class="badge badge-secondary-lighten rounded-0">
                                {{ ucfirst($user->spesial) }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body border-top">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <strong>Tempat Lahir</strong>
                        <br>
                        {{ $user_detail->tempat_lahir ?? '-' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Tanggal Lahir</strong>
                        <br>
                        {{ $user_detail && $user_detail->tanggal_lahir ? \Carbon\Carbon::parse($user_detail->tanggal_lahir)->translatedFormat('d F Y') : null }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <strong>File KTP</strong>
                        <br>
                        @if ($user_detail->file_ktp ?? null)
                            <a href="{{ asset('storage/uploads/' . $user_detail->file_ktp) }}"
                                class="btn btn-sm btn-outline-secondary rounded-0 mt-1" target="_blank">
                                Lihat File KTP
                            </a>
                        @else
                            -
                        @endif
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>File KK</strong>
                        <br>
                        @if ($user_detail->file_kk ?? null)
                            <a href="{{ asset('storage/uploads/' . $user_detail->file_kk) }}"
                                class="btn btn-sm btn-outline-secondary rounded-0 mt-1" target="_blank">
                                Lihat File KK
                            </a>
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <strong>Alamat Lengkap</strong>
                        <br>
                        {{ $user_detail->alamat ?? '-' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Kode POS</strong>
                        <br>
                        {{ $user_detail->kode_pos ?? '-' }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <strong>Pekerjaan</strong>
                        <br>
                        {{ $user_detail->pekerjaan ?? '-' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>No. NPWP</strong>
                        <br>
                        {{ $user_detail->no_npwp ?? '-' }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <strong>Nama Gadis Ibu Kandung</strong>
                        <br>
                        {{ $user_detail->nama_ibu ?? '-' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Tinggal Bersama</strong>
                        <br>
                        {{ $user_detail->tinggal_bersama ?? '-' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- container -->
@endsection
