<li class="side-nav-title side-nav-item">Menu</li>

<li class="side-nav-item {{ request()->is('anggota') ? 'menuitem-active' : '' }}">
    <a href="{{ url('anggota') }}" class="side-nav-link {{ request()->is('anggota') ? 'active' : '' }}">
        <i class="uil-home-alt"></i>
        <span>Dashboard</span>
    </a>
</li>
<li class="side-nav-item {{ request()->is('anggota/simpanan*') ? 'menuitem-active' : '' }}">
    <a href="{{ url('anggota/simpanan') }}" class="side-nav-link {{ request()->is('anggota/simpanan*') ? 'active' : '' }}">
        <i class="uil-users-alt"></i>
        <span>Simpanan Saya</span>
    </a>
</li>
<li class="side-nav-item {{ request()->is('anggota/pinjaman*') ? 'menuitem-active' : '' }}">
    <a href="{{ url('anggota/pinjaman') }}" class="side-nav-link {{ request()->is('anggota/pinjaman*') ? 'active' : '' }}">
        <i class="uil-users-alt"></i>
        <span>Pinjaman Saya</span>
    </a>
</li>