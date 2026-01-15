@extends('layout.app')

@section('title', 'Detail Anggota')

@section('content')
    <!-- Start Content-->
    <div class="container-fluid">
        <!-- start page title -->
        <div class="d-flex align-items-center gap-2">
            <a href="{{ url('admin/anggota') }}" class="btn btn-secondary rounded-0">
                <i class="mdi mdi-arrow-left"></i>
            </a>
            <div class="page-title-box">
                <h4 class="page-title">Detail Anggota</h4>
            </div>
        </div>
        <!-- end page title -->
        <div class="card mb-2 rounded-0">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <img src="{{ asset('storage/uploads/' . $user_detail->foto_diri) }}" alt="" class="img-fluid">
                    </div>
                    <div class="col-md-6 mb-2">
                        <div class="mb-2">
                            <strong>Nama Lengkap</strong>
                            <br>
                            {{ $user->nama }}
                        </div>
                        <div class="mb-2">
                            <strong>Nama Panggilan</strong>
                            <br>
                            {{ $user->panggilan }}
                        </div>
                        <div class="mb-2">
                            <strong>Jenis Kelamin</strong>
                            <br>
                            {{ $user->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}
                        </div>
                        <div class="mb-2">
                            <strong>No. HP / WhatsApp</strong>
                            <br>
                            {{ $user->telp }}
                        </div>
                        <div class="mb-2">
                            <strong>Role</strong>
                            <br>
                            <span class="badge badge-info-lighten rounded-0">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>
                        <div class="mb-2">
                            <strong>Spesial</strong>
                            <br>
                            @if ($user->spesial != 'normal')
                                <span class="badge badge-secondary-lighten rounded-0">
                                    {{ ucfirst($user->spesial) }}
                                </span>
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-2 rounded-0">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <strong>No. KTP</strong>
                        <br>
                        {{ $user_detail->no_ktp ?? '-' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Masa Berlaku KTP</strong>
                        <br>
                        {{ $user_detail->masa_berlaku_ktp ?? '-' }}
                    </div>
                </div>
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
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <strong>
                            Nama
                            @if ($user->gender == 'L')
                                Istri
                            @else
                                Suami
                            @endif
                        </strong>
                        <br>
                        {{ $user_detail->nama_pasangan ?? '-' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>
                            Pekerjaan
                            @if ($user->gender == 'L')
                                Istri
                            @else
                                Suami
                            @endif
                        </strong>
                        <br>
                        {{ $user_detail->pekerjaan_pasangan ?? '-' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- container -->
@endsection
