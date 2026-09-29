@extends('layout.app')

@section('title', 'Dashboard Anggota')

@section('content')
    <div class="container-fluid">
        <div class="page-title-box">
            <h4 class="page-title">Dashboard Anggota</h4>
        </div>

        <div class="card">
            <div class="card-body">
                <h4>Selamat datang, {{ auth()->user()->nickname ?: auth()->user()->name }}</h4>

                <p class="text-muted mb-0">
                    Selamat datang di sistem KOPKARMADA.
                </p>
            </div>
        </div>
    </div>
@endsection
