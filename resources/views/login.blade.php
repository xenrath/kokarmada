<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Login - KOPKARMADA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('storage/uploads/asset/logo-regular.png') }}">

    <!-- App css -->
    <link href="{{ asset('hyper/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('hyper/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="light-style" />
    <link href="{{ asset('hyper/assets/css/app-dark.min.css') }}" rel="stylesheet" type="text/css" id="dark-style" />

    <!-- Toastr alert -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>

<body class="authentication-bg pb-0" data-layout-config='{"darkMode":false}'>
    <div class="auth-fluid">
        <!--Auth fluid left content -->
        <div class="auth-fluid-form-box">
            <div class="align-items-center d-flex h-100">
                <div class="card-body">
                    <!-- Logo -->
                    <div class="auth-brand text-center text-lg-start">
                        <a href="index.html" class="logo-dark">
                            <span>
                                <img src="{{ asset('storage/uploads/asset/logo.png') }}" alt="" height="40">
                            </span>
                        </a>
                        <a href="index.html" class="logo-light">
                            <span>
                                <img src="{{ asset('storage/uploads/asset/logo.png') }}" alt="" height="18">
                            </span>
                        </a>
                    </div>

                    <!-- title-->
                    <h4 class="mt-0">LOGIN</h4>
                    <p class="text-muted mb-4">Masukan Email dan Password Anda</p>

                    <!-- form -->
                    <form action="{{ url('login') }}" method="POST" id="form-submit">
                        @csrf
                        <div class="mb-2">
                            <label for="email" class="form-label">Email</label>
                            <input class="form-control rounded-0 @error('email') is-invalid @enderror" type="email"
                                id="email" name="email" value="{{ old('email') }}" autofocus>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="mb-2">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input class="form-control rounded-0 @error('password') is-invalid @enderror"
                                    type="password" id="password" name="password" value="{{ old('password') }}">
                                <div class="input-group-text rounded-0" style="cursor: pointer;"
                                    onclick="show_password()">
                                    <i class="mdi mdi-eye" id="icon-eye"></i>
                                </div>
                            </div>
                            @error('password')
                                <div class="text-danger mt-0">
                                    <small>{{ $message }}</small>
                                </div>
                            @enderror
                        </div>
                        <div class="d-grid mb-0 text-center mt-4">
                            <button type="submit" class="btn btn-primary rounded-0" id="btn-submit"
                                onclick="form_submit()">
                                <i id="btn-submit-load" class="mdi mdi-spin mdi-loading" style="display: none;"></i>
                                <span id="btn-submit-text">
                                    <i class="mdi mdi-login"></i>
                                    Masuk
                                </span>
                            </button>
                        </div>
                        <!-- social-->
                    </form>
                    <!-- end form-->
                </div>
                <!-- end .card-body -->
            </div>
            <!-- end .align-items-center.d-flex.h-100-->
        </div>
        <!-- end auth-fluid-form-box-->

        <!-- Auth fluid right content -->
        <div class="auth-fluid-right text-center">
            <div class="auth-user-testimonial">
                <h2 class="mb-2">KOPKARMADA</h2>
                <p class="lead">
                    <i class="mdi mdi-format-quote-open"></i>
                    Dari Anggota, Oleh Anggota, Untuk Kesejahteraan.
                    <i class="mdi mdi-format-quote-close"></i>
                </p>
            </div> <!-- end auth-user-testimonial-->
        </div>
        <!-- end Auth fluid right content -->
    </div>
    <!-- end auth-fluid-->

    <script>
        function show_password() {
            var password = document.getElementById('password');
            var type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            var icon_eye = document.getElementById('icon-eye');
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

    <!-- bundle -->
    <script src="{{ asset('hyper/assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('hyper/assets/js/app.min.js') }}"></script>
    
    <!-- Toastr alert -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    @include('alert.toastr')
</body>

</html>
