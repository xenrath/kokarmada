<li class="side-nav-title side-nav-item">Menu</li>

<li class="side-nav-item">
    <a href="{{ url('anggota') }}" class="side-nav-link {{ request()->is('anggota') ? 'active' : '' }}">
        <i class="uil-home-alt"></i>
        <span>Dashboard</span>
    </a>
</li>
<li class="side-nav-item">
    <a href="{{ url('anggota/user') }}" class="side-nav-link {{ request()->is('anggota/user*') ? 'active' : '' }}">
        <i class="uil-users-alt"></i>
        <span>Data Pengguna</span>
    </a>
</li>