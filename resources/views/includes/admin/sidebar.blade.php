<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile">
            <a href="#" class="nav-link">
                <div class="nav-profile-image">
                    @if (Auth::user()->foto_profile == null)
                    <img src="{{ asset('assets/images/faces/face1.jpg') }}" alt="image" />
                    @else
                    <img src="{{ Storage::url(Auth::user()->foto_profile) }}" alt="image" />
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
        <li class="nav-item {{ (request()->is('pages/admin/category') ? 'active' : '') }}">
            <a class="nav-link" href="{{ route('category.index') }}">
                <span class="menu-title">
                    Category Finance
                </span>
                <i class="mdi mdi-format-list-bulleted menu-icon"></i>
            </a>
        </li>
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
        <li class="nav-item {{ (request()->is('pages/admin/about') ? 'active' : '') }}">
            <a class="nav-link" href="{{ route('about.index') }}">
                <span class="menu-title">About</span>
                <i class="mdi mdi-information-outline menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ (request()->is('pages/admin/portofolio') ? 'active' : '') }}">
            <a class="nav-link" href="{{ route('portofolio.index') }}">
                <span class="menu-title">Portofolio</span>
                <i class="mdi mdi-folder-multiple-image menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ (request()->is('pages/admin/document') ? 'active' : '') }}">
            <a class="nav-link" href="{{ route('document.index') }}">
                <span class="menu-title">Document</span>
                <i class="mdi mdi-folder-multiple-image menu-icon"></i>
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

        @elseif (Auth::user()->roles == 'Customer')

        <li class="nav-item {{ (request()->is('pages/customer/dashboard') ? 'active' : '') }}">
            <a class="nav-link" href="{{ route('customer.dashboard') }}">
                <span class="menu-title">Dashboard</span>
                <i class="mdi mdi-home menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ (request()->is('pages/customer/category-finance') ? 'active' : '') }}">
            <a class="nav-link" href="{{ route('category-finance.index') }}">
                <span class="menu-title">
                    Expense Category
                </span>
                <i class="mdi mdi-format-list-bulleted menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ (request()->is('pages/customer/category-income') ? 'active' : '') }}">
            <a class="nav-link" href="{{ route('category-income.index') }}">
                <span class="menu-title">
                    Income Category
                </span>
                <i class="mdi mdi-format-list-bulleted menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ (request()->is('pages/customer/income') ? 'active' : '') }}">
            <a class="nav-link " href="{{ route('income.index') }}">
                <span class="menu-title">Income</span>
                <i class="mdi mdi mdi-currency-usd menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ (request()->is('pages/customer/expense') ? 'active' : '') }}">
            <a class="nav-link" href="{{ route('expense.index') }}">
                <span class="menu-title">Expense</span>
                <i class="mdi mdi-cash-100 menu-icon"></i>
            </a>
        </li>
        <li class="nav-item {{ (request()->is('pages/customer/akun') ? 'active' : '') }}">
            <a class="nav-link" href="{{ route('akun.index') }}">
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