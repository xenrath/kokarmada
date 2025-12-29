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
        <div class="card mb-2 rounded-0">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <strong>Nama Lengkap</strong>
                        <br>
                        {{ $user->nama }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Email</strong>
                        <br>
                        {{ $user->email }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <strong>Jenis Kelamin</strong>
                        <br>
                        {{ $user->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}
                    </div>
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
                <div class="alert alert-info text-center rounded-0" role="alert">
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
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <strong>Tempat & Tanggal Lahir</strong>
                        <br>
                        {{ $user->nama }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Alamat Lengkap</strong>
                        <br>
                        {{ $user->email }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <strong>No. HP / WhatsApp</strong>
                        <br>
                        {{ $user->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>KTP</strong>
                        <br>
                        <span class="badge badge-primary-lighten rounded-0">
                            Ada
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- container -->
@endsection
