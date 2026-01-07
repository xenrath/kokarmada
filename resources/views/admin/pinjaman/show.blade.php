@extends('layout.app')

@section('title', 'Detail Pinjaman')

@section('content')
    <!-- Start Content-->
    <div class="container-fluid">
        <!-- start page title -->
        <div class="d-flex align-items-center gap-2">
            <a href="{{ url('anggota/pinjaman') }}" class="btn btn-secondary rounded-0">
                <i class="mdi mdi-arrow-left"></i>
            </a>
            <div class="page-title-box">
                <h4 class="page-title">Detail Pinjaman</h4>
            </div>
        </div>
        <!-- end page title -->
        <div class="card mb-2 rounded-0">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-4">
                        <strong>Nama Lengkap</strong>
                    </div>
                    <div class="col-md-8">
                        {{ $user->nama }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">
                        <strong>Email</strong>
                    </div>
                    <div class="col-md-8">
                        {{ $user->email }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">
                        <strong>Role</strong>
                    </div>
                    <div class="col-md-8">
                        <h5 class="m-0">
                            @if ($user->role != 'anggota')
                                <span class="badge badge-secondary-lighten rounded-0">
                                    {{ ucfirst($user->role) }}
                                </span>
                            @else
                                <span class="badge badge-info-lighten rounded-0">
                                    {{ ucfirst($user->role) }}
                                </span>
                            @endif
                        </h5>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">
                        <strong>Status</strong>
                    </div>
                    <div class="col-md-8">
                        <h5 class="m-0">
                            @if ($user->status == 'aktif')
                                <span class="badge badge-primary-lighten rounded-0">
                                    {{ ucfirst($user->status) }}
                                </span>
                            @else
                                <span class="badge badge-danger-lighten rounded-0">
                                    {{ ucfirst($user->status) }}
                                </span>
                            @endif
                        </h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-2">
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-4">
                        <strong>Nama Lengkap</strong>
                    </div>
                    <div class="col-md-8">
                        {{ $user->nama }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">
                        <strong>Email</strong>
                    </div>
                    <div class="col-md-8">
                        {{ $user->email }}
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">
                        <strong>Role</strong>
                    </div>
                    <div class="col-md-8">
                        <h5 class="m-0">
                            @if ($user->role != 'anggota')
                                <span class="badge badge-secondary-lighten rounded-0">
                                    {{ ucfirst($user->role) }}
                                </span>
                            @else
                                <span class="badge badge-info-lighten rounded-0">
                                    {{ ucfirst($user->role) }}
                                </span>
                            @endif
                        </h5>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">
                        <strong>Status</strong>
                    </div>
                    <div class="col-md-8">
                        <h5 class="m-0">
                            @if ($user->status == 'aktif')
                                <span class="badge badge-primary-lighten rounded-0">
                                    {{ ucfirst($user->status) }}
                                </span>
                            @else
                                <span class="badge badge-danger-lighten rounded-0">
                                    {{ ucfirst($user->status) }}
                                </span>
                            @endif
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- container -->
@endsection
