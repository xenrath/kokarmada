<li class="side-nav-title">MENU</li>

<li class="side-nav-item">
    <a href="{{ route('treasurer.home') }}" class="side-nav-link">
        <i class="uil-dashboard"></i>
        <span>Dashboard</span>
    </a>
</li>

<li class="side-nav-item">
    <a href="{{ route('treasurer.loans.index') }}" class="side-nav-link">
        <i class="uil-money-withdraw"></i>
        <span>Review Pinjaman</span>
    </a>
</li>

<li class="side-nav-item">
    <a href="{{ route('treasurer.disbursements.index') }}" class="side-nav-link">
        <i class="uil-money-insert"></i>
        <span>Pencairan Pinjaman</span>
    </a>
</li>

<li class="side-nav-item">
    <a href="{{ route('treasurer.disbursements.history') }}" class="side-nav-link">
        <i class="uil-history"></i>
        <span>Riwayat Pencairan</span>
    </a>
</li>

<li class="side-nav-item">
    <a href="{{ route('treasurer.installments.index') }}" class="side-nav-link">
        <i class="uil-money-withdraw"></i>
        <span>Angsuran</span>
    </a>
</li>
