<li class="side-nav-title side-nav-item">Menu</li>

<li class="side-nav-item">
    <a href="{{ url('admin') }}" class="side-nav-link {{ request()->is('admin') ? 'active' : '' }}">
        <i class="uil-home-alt"></i>
        <span>Dashboard</span>
    </a>
</li>
<li class="side-nav-item">
    <a href="{{ url('admin/anggota') }}" class="side-nav-link {{ request()->is('admin/anggota*') ? 'active' : '' }}">
        <i class="uil-users-alt"></i>
        <span>Data Anggota</span>
    </a>
</li>