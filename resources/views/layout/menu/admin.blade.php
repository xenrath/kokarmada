<li class="side-nav-title">MENU UTAMA</li>

<li class="side-nav-item">
    <a href="{{ route('admin.home') }}" class="side-nav-link">
        <i class="uil-dashboard"></i>
        <span>Dashboard</span>
    </a>
</li>

<li class="side-nav-item">
    <a href="{{ route('admin.users.index') }}" class="side-nav-link">
        <i class="uil-users-alt"></i>
        <span>Pengguna</span>
    </a>
</li>

<li class="side-nav-item">
    <a href="{{ route('admin.accounts.index') }}" class="side-nav-link">
        <i class="uil-wallet"></i>
        <span>Rekening</span>
    </a>
</li>

<li class="side-nav-item">
    <a href="{{ route('admin.loan-settings.edit') }}" class="side-nav-link">
        <i class="uil-setting"></i>
        <span>Pengaturan Pinjaman</span>
    </a>
</li>
