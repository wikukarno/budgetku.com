<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <a href="#" class="nav-link">
                <div class="nav-profile-image">
                    @if (Auth::user()->avatar == null)
                    <img src="{{ asset('assets/images/faces/face1.jpg') }}" alt="image" />
                    @else
                    <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="image" />
                    <span class="login-status online"></span>
                    @endif
                </div>
                <div class="nav-profile-text d-flex flex-column">
                    <span class="font-weight-bold mb-2">{{ Auth::user()->name ?? '' }}</span>
                    <span class="text-secondary text-small">{{ Auth::user()->roles ?? '' }}</span>
                </div>
                <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
            </a>
        </li>

        @if (Auth::user()->roles == 'Owner')
        <li class="nav-item {{ (request()->is('pages/admin/dashboard') ? 'active' : '') }}">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ (request()->is('pages/admin/admin-category-income') ? 'active' : '') }}">
            <a class="nav-link" href="{{ route('admin-category-income.index') }}">
            <span class="menu-title">
                Category Income
            </span>
                <i class="mdi mdi-format-list-bulleted menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ (request()->is('pages/admin/category') ? 'active' : '') }}">
            <a class="nav-link" href="{{ route('category.index') }}">
                <span class="menu-title">
                    Category Finance
                </span>
                <i class="mdi mdi-format-list-bulleted menu-icon"></i>
            </a>
        </li>
        {{-- <li class="nav-item {{ (request()->is('pages/admin/debt') ? 'active' : '') }}">
            <a class="nav-link" href="{{ route('debt.index') }}">
                <span class="menu-title">Debt</span>
                <i class="mdi mdi-cash-multiple menu-icon"></i>
            </a>
        </li> --}}
        <li class="nav-item {{ (request()->is('pages/admin/bill') ? 'active' : '') }}">
            <a class="nav-link" href="{{ route('bill.index') }}">
                <span class="menu-title">Bill</span>
                <i class="mdi mdi-cash-multiple menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ (request()->is('pages/admin/salary') ? 'active' : '') }}">
            <a class="nav-link" href="{{ route('salary.index') }}">
                <span class="menu-title">Salary</span>
                <i class="mdi mdi mdi-currency-usd menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ (request()->is('pages/admin/finance') ? 'active' : '') }}">
            <a class="nav-link" href="{{ route('finance.index') }}">
                <span class="menu-title">Finance</span>
                <i class="mdi mdi-cash-100 menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ (request()->is('pages/admin/account') ? 'active' : '') }}">
            <a class="nav-link" href="{{ route('account.index') }}">
                <span class="menu-title">Account</span>
                <i class="mdi mdi-account-box-outline menu-icon"></i>
            </a>
        </li>
        <li class="nav-item sidebar-actions">
            <span class="nav-link d-grid">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf

                    <button type="submit" class="btn btn-block col btn-lg btn-gradient-primary mt-4">
                        Keluar
                    </button>
                </form>
            </span>
        </li>

        @endif
    </ul>
</nav>
