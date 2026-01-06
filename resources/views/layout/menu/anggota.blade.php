<li class="side-nav-title side-nav-item">Menu</li>
<li class="side-nav-item {{ request()->is('anggota') ? 'menuitem-active' : '' }}">
    <a href="{{ url('anggota') }}" class="side-nav-link {{ request()->is('anggota') ? 'active' : '' }}">
        <i class="uil-home-alt"></i>
        <span>Dashboard</span>
    </a>
</li>
@if (auth()->user()->isKetua())
    <li class="side-nav-title side-nav-item">Menu Ketua</li>
    <li class="side-nav-item {{ request()->is('anggota/ketua/simpanan*') ? 'menuitem-active' : '' }}">
        <a href="{{ url('anggota/ketua/simpanan') }}"
            class="side-nav-link {{ request()->is('anggota/ketua/simpanan*') ? 'active' : '' }}">
            <i class="uil-money-bill"></i>
            <span>Data Simpanan</span>
        </a>
    </li>
    <li class="side-nav-item {{ request()->is('anggota/ketua/pinjaman*') ? 'menuitem-active' : '' }}">
        <a href="{{ url('anggota/ketua/pinjaman') }}"
            class="side-nav-link {{ request()->is('anggota/ketua/pinjaman*') ? 'active' : '' }}">
            <i class="uil-money-withdraw"></i>
            <span>Data Pinjaman</span>
        </a>
    </li>
@endif
@if (auth()->user()->isSekretaris())
    <li class="side-nav-title side-nav-item">Menu Sekretaris</li>
    <li class="side-nav-item {{ request()->is('anggota/sekretaris/simpanan*') ? 'menuitem-active' : '' }}">
        <a href="{{ url('anggota/sekretaris/simpanan') }}"
            class="side-nav-link {{ request()->is('anggota/sekretaris/simpanan*') ? 'active' : '' }}">
            <i class="uil-money-bill"></i>
            <span>Data Simpanan</span>
        </a>
    </li>
    <li class="side-nav-item {{ request()->is('anggota/sekretaris/pinjaman*') ? 'menuitem-active' : '' }}">
        <a href="{{ url('anggota/sekretaris/pinjaman') }}"
            class="side-nav-link {{ request()->is('anggota/sekretaris/pinjaman*') ? 'active' : '' }}">
            <i class="uil-money-withdraw"></i>
            <span>Data Pinjaman</span>
        </a>
    </li>
@endif
@if (auth()->user()->isBendahara())
    <li class="side-nav-title side-nav-item">Menu Bendahara</li>
    <li class="side-nav-item {{ request()->is('anggota/bendahara/simpanan*') ? 'menuitem-active' : '' }}">
        <a href="{{ url('anggota/bendahara/simpanan') }}"
            class="side-nav-link {{ request()->is('anggota/bendahara/simpanan*') ? 'active' : '' }}">
            <i class="uil-money-bill"></i>
            <span>Data Simpanan</span>
        </a>
    </li>
    <li class="side-nav-item {{ request()->is('anggota/bendahara/pinjaman*') ? 'menuitem-active' : '' }}">
        <a href="{{ url('anggota/bendahara/pinjaman') }}"
            class="side-nav-link {{ request()->is('anggota/bendahara/pinjaman*') ? 'active' : '' }}">
            <i class="uil-money-withdraw"></i>
            <span>Data Pinjaman</span>
        </a>
    </li>
@endif
@if (auth()->user()->isManajer())
    <li class="side-nav-title side-nav-item">Menu Manajer Analis</li>
    <li class="side-nav-item {{ request()->is('anggota/manajer/simpanan*') ? 'menuitem-active' : '' }}">
        <a href="{{ url('anggota/manajer/simpanan') }}"
            class="side-nav-link {{ request()->is('anggota/manajer/simpanan*') ? 'active' : '' }}">
            <i class="uil-money-bill"></i>
            <span>Data Simpanan</span>
        </a>
    </li>
    <li class="side-nav-item {{ request()->is('anggota/manajer/pinjaman*') ? 'menuitem-active' : '' }}">
        <a href="{{ url('anggota/manajer/pinjaman') }}"
            class="side-nav-link {{ request()->is('anggota/manajer/pinjaman*') ? 'active' : '' }}">
            <i class="uil-money-withdraw"></i>
            <span>Data Pinjaman</span>
        </a>
    </li>
@endif
@if (auth()->user()->isPetugas())
    <li class="side-nav-title side-nav-item">Menu Petugas</li>
    <li class="side-nav-item {{ request()->is('anggota/petugas/simpanan*') ? 'menuitem-active' : '' }}">
        <a href="{{ url('anggota/petugas/simpanan') }}"
            class="side-nav-link {{ request()->is('anggota/petugas/simpanan*') ? 'active' : '' }}">
            <i class="uil-money-bill"></i>
            <span>Data Simpanan</span>
        </a>
    </li>
    <li class="side-nav-item {{ request()->is('anggota/petugas/pinjaman*') ? 'menuitem-active' : '' }}">
        <a href="{{ url('anggota/petugas/pinjaman') }}"
            class="side-nav-link {{ request()->is('anggota/petugas/pinjaman*') ? 'active' : '' }}">
            <i class="uil-money-withdraw"></i>
            <span>Data Pinjaman</span>
        </a>
    </li>
@endif
@if (!auth()->user()->isNormal())
    <li class="side-nav-title side-nav-item">Menu Nasabah</li>
@endif
<li class="side-nav-item {{ request()->is('anggota/simpanan*') ? 'menuitem-active' : '' }}">
    <a href="{{ url('anggota/simpanan') }}"
        class="side-nav-link {{ request()->is('anggota/simpanan*') ? 'active' : '' }}">
        <i class="uil-money-bill"></i>
        <span>Simpanan Saya</span>
    </a>
</li>
<li class="side-nav-item {{ request()->is('anggota/pinjaman*') ? 'menuitem-active' : '' }}">
    <a href="{{ url('anggota/pinjaman') }}"
        class="side-nav-link {{ request()->is('anggota/pinjaman*') ? 'active' : '' }}">
        <i class="uil-money-withdraw"></i>
        <span>Pinjaman Saya</span>
    </a>
</li>
