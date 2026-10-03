<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse p-0">
    <div class="position-sticky pt-3 p-3 text-white">
        <div class="d-flex align-items-center mb-4">
            <i class="fas fa-utensils fa-2x me-3"></i>
            <h4 class="m-0">Rumah Makan Bissmillah</h4>
        </div>
        <hr class="bg-light">
        <ul class="nav flex-column mt-4">

            {{-- NAVIGASI UTAMA ROLE PEMILIK --}}
            @if(Auth::check() && Auth::user()->role === 'pemilik')

                {{-- 1. Dashboard (HANYA PEMILIK) --}}
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link sidebar-link p-3 {{ Request::routeIs('dashboard') ? 'active-link' : '' }}">
                        <i class="fas fa-tachometer-alt me-3"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <hr class="bg-light mt-2 mb-2">

                {{-- NAVIGASI LAPORAN (HANYA PEMILIK) --}}
                <li class="nav-item">
                    <a href="{{ route('laba-rugi.index') }}" class="nav-link sidebar-link p-3 {{ Request::routeIs('laba-rugi.index') ? 'active-link' : '' }}">
                        <i class="fas fa-chart-line me-3"></i>
                        <span>Laporan Laba Rugi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('rekap.harian') }}" class="nav-link sidebar-link p-3 {{ Request::routeIs('rekap.harian') || Request::routeIs('rekap.harian.grafik') ? 'active-link' : '' }}">
                        <i class="fas fa-file-invoice-dollar me-3"></i>
                        <span>Laporan Harian</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('rekap.bulanan') }}" class="nav-link sidebar-link p-3 {{ Request::routeIs('rekap.bulanan') || Request::routeIs('rekap.bulanan.grafik') ? 'active-link' : '' }}">
                        <i class="fas fa-chart-bar me-3"></i>
                        <span>Laporan Bulanan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('rekap.tahunan') }}" class="nav-link sidebar-link p-3 {{ Request::routeIs('rekap.tahunan') || Request::routeIs('rekap.tahunan.grafik') ? 'active-link' : '' }}">
                        <i class="fas fa-chart-bar me-3"></i>
                        <span>Laporan Tahunan</span>
                    </a>
                </li>
            @endif

            {{-- NAVIGASI KHUSUS ROLE ADMIN --}}
            @if(Auth::check() && Auth::user()->role === 'admin')
                <hr class="bg-light mt-2 mb-2">

                {{-- INPUT TRANSAKSI (BASE LINK ADMIN) --}}
                <li class="nav-item">
                    <a href="{{ route('input.transaksi') }}" class="nav-link sidebar-link p-3 {{ Request::routeIs('input.transaksi') ? 'active-link' : '' }}">
                        <i class="fas fa-cash-register me-3"></i>
                        <span>Input Transaksi</span>
                    </a>
                </li>

            {{-- Bagian Navigasi Khusus ROLE ADMIN --}}
                @if(Auth::check() && Auth::user()->role === 'admin')
                    <li class="nav-item">
                        <a href="{{ route('admin.pengeluaran.index') }}" class="nav-link sidebar-link p-3 {{ Request::is('admin/kelola-pengeluaran*') ? 'active-link' : '' }}">
                            <i class="fas fa-hand-holding-usd me-3"></i>
                                <span>Input Pengeluaran Harian</span>
                        </a>
                    </li>
                @endif

                {{-- Edit Menu (HANYA ADMIN) --}}
                <li class="nav-item">
                    <a href="{{ route('menu.index') }}" class="nav-link sidebar-link p-3 {{ Request::routeIs('menu.index') ? 'active-link' : '' }}">
                        <i class="fas fa-utensils me-3"></i>
                        <span>Edit Menu</span>
                    </a>
                </li>

                {{-- Master Kategori (HANYA ADMIN) --}}
                <li class="nav-item">
                    <a href="{{ route('master.kategori.index') }}" class="nav-link sidebar-link p-3 {{ Request::routeIs('master.kategori.index') ? 'active-link' : '' }}">
                        <i class="fas fa-list me-3"></i>
                        <span>Master Kategori</span>
                    </a>
                </li>
            @endif

            {{-- Bagian Navigasi ROLE PEMILIK --}}
            @if(Auth::check() && Auth::user()->role === 'pemilik')
                <li class="nav-item">
                    <a href="{{ route('pemilik.pengeluaran.index') }}" class="nav-link sidebar-link p-3 {{ Request::is('pemilik/daftar-pengeluaran*') ? 'active-link' : '' }}">
                            <i class="fas fa-search-dollar me-3"></i>
                        <span>Meninjau Pengeluaran </span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
</nav>