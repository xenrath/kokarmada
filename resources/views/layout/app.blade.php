<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="Sistem Informasi Koperasi berbasis web untuk pengelolaan anggota, simpanan, pinjaman, dan laporan keuangan koperasi.">
    <meta name="author" content="Labib Bhamada">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('storage/uploads/asset/logo-regular.png') }}">

    <!-- App css -->
    <link href="{{ asset('hyper/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('hyper/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="light-style">
    <link href="{{ asset('hyper/assets/css/app-dark.min.css') }}" rel="stylesheet" type="text/css" id="dark-style">

    @yield('style')

    <!-- Toastr alert -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

</head>

<body class="loading"
    data-layout-config='{"leftSideBarTheme":"dark","layoutBoxed":false, "leftSidebarCondensed":false, "leftSidebarScrollable":false,"darkMode":false, "showRightSidebarOnStart": true}'>
    <!-- Begin page -->
    <div class="wrapper">
        <!-- ========== Left Sidebar Start ========== -->
        <div class="leftside-menu">

            <!-- LOGO -->
            <a href="index.html" class="logo text-center logo-light">
                <span class="logo-lg">
                    <img src="{{ asset('hyper/assets/images/logo.png') }}" alt="" height="16">
                </span>
                <span class="logo-sm">
                    <img src="{{ asset('hyper/assets/images/logo_sm.png') }}" alt="" height="16">
                </span>
            </a>

            <!-- LOGO -->
            <a href="index.html" class="logo text-center logo-dark">
                <span class="logo-lg">
                    <img src="{{ asset('hyper/assets/images/logo-dark.png') }}" alt="" height="16">
                </span>
                <span class="logo-sm">
                    <img src="{{ asset('hyper/assets/images/logo_sm_dark.png') }}" alt="" height="16">
                </span>
            </a>

            <div class="h-100" id="leftside-menu-container" data-simplebar="">

                <!--- Sidemenu -->
                <ul class="side-nav">
                    @if (auth()->user()->isDev())
                        @include('layout.menu.dev')
                    @endif
                    @if (auth()->user()->isAdmin())
                        @include('layout.menu.admin')
                    @endif
                    @if (auth()->user()->isAnggota())
                        @include('layout.menu.anggota')
                    @endif
                </ul>
                <!-- End Sidebar -->

                <div class="clearfix"></div>

            </div>
            <!-- Sidebar -left -->

        </div>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">
                <!-- Topbar Start -->
                <div class="navbar-custom">
                    <ul class="list-unstyled topbar-menu float-end mb-0">
                        <li class="dropdown notification-list">
                            @php
                                $notifikasis = \App\Models\Notifikasi::where('user_id', auth()->user()->id)
                                    ->orderByDesc('created_at')
                                    ->limit(5)
                                    ->get();
                            @endphp
                            <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#"
                                role="button" aria-haspopup="false" aria-expanded="false">
                                <i class="dripicons-bell noti-icon"></i>
                                @if (count($notifikasis))
                                    <span class="noti-icon-badge"></span>
                                @endif
                            </a>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg rounded-0">
                                <!-- item-->
                                <div class="dropdown-item noti-title">
                                    <h5 class="m-0">
                                        <span class="float-end">
                                            <a href="javascript: void(0);" class="text-dark">
                                                <small>Hapus Semua</small>
                                            </a>
                                        </span>
                                        Notifikasi
                                    </h5>
                                </div>
                                <div style="max-height: 230px;" data-simplebar="">
                                    <!-- item-->
                                    @forelse ($notifikasis as $notifikasi)
                                        <a href="{{ url($notifikasi->link) }}" class="dropdown-item notify-item">
                                            <div class="notify-icon bg-primary">
                                                <i class="mdi mdi-cash-plus"></i>
                                            </div>
                                            <p class="notify-details">
                                                {{ $notifikasi->pesan }}
                                                <small class="text-muted">
                                                    {{ $notifikasi->created_at->format('H:i') }}
                                                    WIB •
                                                    {{ $notifikasi->created_at->translatedFormat('d M Y') }}
                                                </small>
                                            </p>
                                        </a>
                                    @empty
                                        <div class="dropdown-item notify-item d-flex justify-content-center align-items-center"
                                            style="height: 60px;">
                                            <p class="text-muted mb-0">
                                                - Tidak ada notifikasi -
                                            </p>
                                        </div>
                                    @endforelse
                                </div>
                                <!-- All-->
                                <a href="{{ url('anggota/notifikasi') }}"
                                    class="dropdown-item text-center text-primary notify-item notify-all">
                                    Lihat Semua
                                </a>
                            </div>
                        </li>
                        <li class="dropdown notification-list">
                            <a class="nav-link dropdown-toggle nav-user arrow-none me-0" data-bs-toggle="dropdown"
                                href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                <span class="account-user-avatar">
                                    <img src="{{ asset('storage/uploads/asset/profile.jpg') }}" alt="user-image"
                                        class="rounded-circle">
                                </span>
                                <span>
                                    <span class="account-user-name">{{ auth()->user()->nama }}</span>
                                    <span class="account-position">
                                        @if (auth()->user()->role == 'anggota')
                                            @if (auth()->user()->spesial == 'normal')
                                                Anggota
                                            @else
                                                {{ ucfirst(auth()->user()->spesial) }}
                                            @endif
                                        @else
                                            {{ ucfirst(auth()->user()->role) }}
                                        @endif
                                    </span>
                                </span>
                            </a>
                            <div
                                class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu profile-dropdown rounded-0">
                                <!-- item-->
                                <a href="{{ url(auth()->user()->role . '/profile') }}"
                                    class="dropdown-item notify-item">
                                    <i class="mdi mdi-account-circle me-1"></i>
                                    <span>Profile Saya</span>
                                </a>

                                <!-- item-->
                                <a href="{{ url(auth()->user()->role . '/password') }}"
                                    class="dropdown-item notify-item">
                                    <i class="mdi mdi-lock-reset me-1"></i>
                                    <span>Ubah Password</span>
                                </a>

                                <!-- item-->
                                <a href="#" class="dropdown-item notify-item" data-bs-toggle="modal"
                                    data-bs-target="#modal-logout">
                                    <i class="mdi mdi-logout me-1"></i>
                                    <span>Logout</span>
                                </a>
                            </div>
                        </li>
                    </ul>
                    <button class="button-menu-mobile open-left">
                        <i class="mdi mdi-menu"></i>
                    </button>
                </div>
                <!-- end Topbar -->

                @yield('content')

            </div>
            <!-- content -->

            <!-- Footer Start -->
            <footer class="footer">
                <div class="container-fluid text-end">
                    <script>
                        document.write(new Date().getFullYear())
                    </script>
                    ©
                    <a href="https://it.bhamada.ac.id/" target="_blank">
                        <strong>IT BHAMADA</strong>
                    </a>
                </div>
            </footer>
            <!-- end Footer -->
        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->

        <div id="modal-logout" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-logout"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content rounded-0">
                    <div class="modal-header">
                        <h4 class="modal-title" id="modal-logout">Logout</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-hidden="true"></button>
                    </div>
                    <div class="modal-body">
                        <p>Yakin keluar dari sistem <strong>KOPKARMADA</strong>?</p>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-light rounded-0" data-bs-dismiss="modal">Batal</button>
                        <form action="{{ url('logout') }}" method="POST" id="form-logout">
                            @csrf
                            <button type="button" class="btn btn-danger rounded-0" id="btn-logout"
                                onclick="form_logout()">
                                <span id="btn-logout-text">Keluar</span>
                                <span id="btn-logout-load" style="display: none;">
                                    <i class="mdi mdi-spin mdi-loading"></i>
                                    Memproses
                                </span>
                            </button>
                        </form>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
    </div>
    <!-- END wrapper -->

    <!-- bundle -->
    <script src="{{ asset('hyper/assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('hyper/assets/js/app.min.js') }}"></script>

    <!-- Toastr alert -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    @include('alert.toastr')

    <script>
        function form_logout() {
            $('#btn-logout').prop('disabled', true);
            $('#btn-logout-text').hide();
            $('#btn-logout-load').show();
            $('#form-logout').submit();
        }
    </script>

    @yield('script')

</body>

</html>
