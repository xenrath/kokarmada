<li class="side-nav-title side-nav-item">Menu</li>

<li class="side-nav-item {{ request()->is('admin') ? 'menuitem-active' : '' }}">
    <a href="{{ url('admin') }}" class="side-nav-link {{ request()->is('admin') ? 'active' : '' }}">
        <i class="uil-home-alt"></i>
        <span>Dashboard</span>
    </a>
</li>
<li class="side-nav-item {{ request()->is('admin/anggota*') ? 'menuitem-active' : '' }}">
    <a href="{{ url('admin/anggota') }}" class="side-nav-link {{ request()->is('admin/anggota*') ? 'active' : '' }}">
        <i class="uil-users-alt"></i>
        <span>Data Anggota</span>
    </a>
</li>
<li class="side-nav-item {{ request()->is('admin/simpanan*') ? 'menuitem-active' : '' }}">
    <a href="{{ url('admin/simpanan') }}" class="side-nav-link {{ request()->is('admin/simpanan*') ? 'active' : '' }}">
        <i class="uil-money-bill"></i>
        <span>Data Simpanan</span>
    </a>
</li>
<li class="side-nav-item {{ request()->is('admin/pinjaman*') ? 'menuitem-active' : '' }}">
    <a href="{{ url('admin/pinjaman') }}"
        class="side-nav-link {{ request()->is('admin/pinjaman*') ? 'active' : '' }}">
        <i class="uil-money-withdraw"></i>
        <span>Data Pinjaman</span>
    </a>
</li>
