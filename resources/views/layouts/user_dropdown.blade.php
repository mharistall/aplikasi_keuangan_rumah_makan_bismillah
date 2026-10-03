<ul class="navbar-nav ms-auto">
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            {{-- pakai inisial ROLE (A/P) dan styling konsisten --}}
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px; font-weight: bold;">
                {{ strtoupper(substr(Auth::user()->role, 0, 1)) ?? '?' }}
            </div>
            <span class="d-none d-md-inline">{{ Auth::user()->role ?? 'Pengguna' }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
            <li>
                <span class="dropdown-item disabled text-dark fw-bold">Login Sebagai: {{ Auth::user()->name ?? 'N/A' }}</span>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form id="logout-form-dropdown" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form-dropdown').submit();">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
            </li>
        </ul>
    </li>
</ul>