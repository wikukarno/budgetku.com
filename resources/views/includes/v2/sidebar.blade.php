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
            <li class="menu-item {{ request()->is('pages/customer/dashboard/v2') ? 'open' : '' }}">
                <a href="{{ route('customer.dashboard.v2') }}" class="menu-link {{ request()->is('pages/customer/dashboard/v2') ? 'active' : '' }}">
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
                        <span class="title">Income Category</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('pages/customer/category/expense/v2') ? 'open' : '' }}">
                    <a href="{{ route('category.expense.index.v2') }}" class="menu-link {{ request()->is('pages/customer/category/expense/v2') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">
                            list_alt
                        </span>
                        <span class="title">Expense Category</span>
                    </a>
                </li>
            @endif

            <li class="menu-title small text-uppercase">
                <span class="menu-title-text">TRANSACTIONS</span>
            </li>

            <li class="menu-item">
                <a href="calendar.html" class="menu-link">
                    <span class="material-symbols-outlined menu-icon">
                        attach_money
                    </span>
                    <span class="title">Income</span>
                </a>
            </li>

            <li class="menu-item">
                <a href="calendar.html" class="menu-link">
                    <span class="material-symbols-outlined menu-icon">
                        payments
                    </span>
                    <span class="title">Expense</span>
                </a>
            </li>

            <li class="menu-title small text-uppercase">
                <span class="menu-title-text">ACCOUNT</span>
            </li>

            <li class="menu-item">
                <a href="profile.html" class="menu-link">
                    <span class="material-symbols-outlined menu-icon">person</span>
                    <span class="title">Profile</span>
                </a>
            </li>

            <li class="menu-item">
                <a href="logout.html" class="menu-link">
                    <span class="material-symbols-outlined menu-icon">logout</span>
                    <span class="title">Sign Out</span>
                </a>
            </li>
        </ul>
    </aside>
</div>