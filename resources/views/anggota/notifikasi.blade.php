@extends('layout.app')

@section('title', 'Notifikasi Saya')

@section('content')
    <!-- Start Content-->
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Notifikasi Saya</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <ul class="nav nav-tabs nav-bordered mb-3">
            <li class="nav-item">
                <a href="#ribbons-notifikasi" data-bs-toggle="tab" aria-expanded="false" class="nav-link active">
                    Notifikasi
                </a>
            </li>
            <li class="nav-item">
                <a href="#ribbons-aktivitas" data-bs-toggle="tab" aria-expanded="true" class="nav-link">
                    Aktivitas
                </a>
            </li>
        </ul>
        <!-- end nav-->
        <div class="tab-content">
            <div class="tab-pane show active" id="ribbons-notifikasi">
                <div class="row">
                    @foreach ($notifikasis as $notifikasi)
                        <div class="col-md-6">
                            <div class="card mb-2 rounded-0">
                                <div class="card-body">
                                    <div class="mb-2">
                                        <strong>{{ $notifikasi->judul }}</strong>
                                        <br>
                                        {{ $notifikasi->pesan }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $notifikasi->created_at->format('H:i') }} WIB •
                                        {{ $notifikasi->created_at->translatedFormat('d M Y') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="tab-pane" id="ribbons-aktivitas">
                <div class="row">
                    @foreach ($aktivitass as $aktivitas)
                        <div class="col-md-6">
                            <div class="card mb-2 rounded-0">
                                <div class="card-body">
                                    <div class="mb-2">
                                        <strong>{{ $aktivitas->judul }}</strong>
                                        <br>
                                        {{ $aktivitas->pesan }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $aktivitas->created_at->format('H:i') }} WIB •
                                        {{ $aktivitas->created_at->translatedFormat('d M Y') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <!-- container -->
@endsection

@section('script')
    <script>
        function form_submit() {
            $('#btn-submit').prop('disabled', true);
            $('#btn-submit-text').hide();
            $('#btn-submit-load').show();
            $('#form-submit').submit();
        }
    </script>
@endsection
