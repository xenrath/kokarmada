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
        <div class="row">
            <div class="col-xl-4 col-lg-4">
                <div class="card tilebox-one rounded-0">
                    <div class="card-body">
                        <i class='uil uil-users-alt float-end'></i>
                        <h6 class="text-uppercase mt-0">Data Anggota</h6>
                        <h2 class="my-2" id="active-users-count">
                            {{ $user }}
                        </h2>
                    </div> <!-- end card-body-->
                </div>
            </div>
        </div>
        <!--end card-->
    </div>
    <!-- container -->
@endsection
