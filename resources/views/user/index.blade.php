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
                <div class="row mb-2">
                    <div class="col-md-6">
                        <strong>Nama Lengkap</strong>
                        <br>
                        {{ $user->nama }}
                    </div>
                    <div class="col-md-6">
                        <strong>Email</strong>
                        <br>
                        {{ $user->email }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <strong>Jenis Kelamin</strong>
                        <br>
                        {{ $user->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}
                    </div>
                    <div class="col-md-6">
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
                <div class="row mb-2">
                    <div class="col-md-6">
                        <strong>Tempat & Tanggal Lahir</strong>
                        <br>
                        {{ $user->nama }}
                    </div>
                    <div class="col-md-6">
                        <strong>Alamat Lengkap</strong>
                        <br>
                        {{ $user->email }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <strong>No. HP / WhatsApp</strong>
                        <br>
                        {{ $user->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}
                    </div>
                    <div class="col-md-6">
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
