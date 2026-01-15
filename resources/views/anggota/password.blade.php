@extends('layout.app')

@section('title', 'Ubah Password')

@section('content')
    <!-- Start Content-->
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Ubah Password</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="card mb-2 rounded-0">
            <form action="{{ url('anggota/password') }}" method="POST" autocomplete="off" id="form-submit">
                @csrf
                <div class="card-body">
                    <div class="form-group mb-2">
                        <label for="password" class="form-label">Password Baru *</label>
                        <div class="input-group">
                            <input class="form-control rounded-0 @error('password') is-invalid @enderror" type="password"
                                id="password" name="password" value="{{ old('password') }}">
                            <div class="input-group-text rounded-0" style="cursor: pointer;" onclick="show_password()">
                                <i class="mdi mdi-eye" id="icon-eye"></i>
                            </div>
                        </div>
                        @error('password')
                            <div class="text-danger mt-0">
                                <small>{{ $message }}</small>
                            </div>
                        @enderror
                    </div>
                    <div class="form-group mb-2">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password *</label>
                        <div class="input-group">
                            <input class="form-control rounded-0 @error('password_confirmation') is-invalid @enderror"
                                type="password" id="password_confirmation" name="password_confirmation"
                                value="{{ old('password_confirmation') }}">
                            <div class="input-group-text rounded-0" style="cursor: pointer;"
                                onclick="show_password_confirmation()">
                                <i class="mdi mdi-eye" id="icon-eye-confirmation"></i>
                            </div>
                        </div>
                        @error('password_confirmation')
                            <div class="text-danger mt-0">
                                <small>{{ $message }}</small>
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary rounded-0" id="btn-submit" onclick="form_submit()">
                        <span id="btn-submit-text">
                            <i class="mdi mdi-content-save"></i>
                            Simpan
                        </span>
                        <span id="btn-submit-load" style="display: none;">
                            <i class="mdi mdi-spin mdi-loading"></i>
                            Memproses...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- container -->
@endsection

@section('script')
    <script>
        function show_password() {
            var password = document.getElementById('password');
            var type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            var icon_eye = document.getElementById('icon-eye');
            var icon_change = icon_eye.className === 'mdi mdi-eye' ? 'mdi mdi-eye-off' : 'mdi mdi-eye';
            icon_eye.className = icon_change;
        }

        function show_password_confirmation() {
            var password = document.getElementById('password_confirmation');
            var type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            var icon_eye = document.getElementById('icon-eye-confirmation');
            var icon_change = icon_eye.className === 'mdi mdi-eye' ? 'mdi mdi-eye-off' : 'mdi mdi-eye';
            icon_eye.className = icon_change;
        }

        function form_submit() {
            $('#btn-submit').prop('disabled', true);
            $('#btn-submit-text').hide();
            $('#btn-submit-load').show();
            $('#form-submit').submit();
        }
    </script>
@endsection
