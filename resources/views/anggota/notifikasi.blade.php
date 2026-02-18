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
                            <small class="text-muted">{{ $notifikasi->created_at->format('H:i d M Y') }}</small>
                        </div>
                    </div>
                </div>
            @endforeach
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
