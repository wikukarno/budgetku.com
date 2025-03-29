<div class="sidebar-area" id="sidebar-area">
    <div class="logo position-relative">
        <a href="index.html" class="d-block text-decoration-none position-relative">
            <img src="{{ asset('v2/images/logo-icon.png') }}" alt="logo-icon">
            <span class="logo-text fw-bold text-dark">Trezo</span>
        </a>
        <button
            class="sidebar-burger-menu bg-transparent p-0 border-0 opacity-0 z-n1 position-absolute top-50 end-0 translate-middle-y"
            id="sidebar-burger-menu">
            <i data-feather="x"></i>
        </button>
    </div>

    <aside id="layout-menu" class="layout-menu menu-vertical menu active" data-simplebar>
        <ul class="menu-inner">
            <li class="menu-title small text-uppercase">
                <span class="menu-title-text">MAIN</span>
            </li>
            <li class="menu-item open">
                <a href="javascript:void(0);" class="menu-link">
                    <span class="material-symbols-outlined menu-icon">dashboard</span>
                    <span class="title">Dashboard</span>
                </a>
            </li>

            <li class="menu-title small text-uppercase">
                <span class="menu-title-text">CATEGORY</span>
            </li>

            @if (Auth::user()->roles == "Customer")
                <li class="menu-item {{ request()->is('pages/customer/category/income/v2') ? 'open' : '' }}">
                    <a href="{{ route('category.income.index.v2') }}" class="menu-link {{ request()->is('pages/customer/category/income/v2') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">
                            list_alt
                        </span>
                        <span class="title">Income Categories</span>
                    </a>
                </li>
            @endif

            <li class="menu-title small text-uppercase">
                <span class="menu-title-text">APPS</span>
            </li>

            <li class="menu-item">
                <a href="calendar.html" class="menu-link">
                    <span class="material-symbols-outlined menu-icon">
                        attach_money
                    </span>
                    <span class="title">Incomes</span>
                </a>
            </li>

            <li class="menu-item">
                <a href="calendar.html" class="menu-link">
                    <span class="material-symbols-outlined menu-icon">
                        payments
                    </span>
                    <span class="title">Expenses</span>
                </a>
            </li>

            <li class="menu-title small text-uppercase">
                <span class="menu-title-text">ACCOUNT</span>
            </li>

            <li class="menu-item">
                <a href="profile.html" class="menu-link">
                    <span class="material-symbols-outlined menu-icon">person</span>
                    <span class="title">My Profile</span>
                </a>
            </li>

            <li class="menu-item">
                <a href="logout.html" class="menu-link">
                    <span class="material-symbols-outlined menu-icon">logout</span>
                    <span class="title">Logout</span>
                </a>
            </li>
        </ul>
    </aside>
</div>