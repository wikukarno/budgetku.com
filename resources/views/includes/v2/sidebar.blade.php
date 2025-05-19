<div class="sidebar-area" id="sidebar-area">
    <div class="logo position-relative">
        <a href="index.html" class="d-block text-decoration-none position-relative">
            {{-- <img src="{{ asset('v2/images/logo-icon.png') }}" alt="logo-icon"> --}}
            <span class="logo-text fw-bold text-dark">
                BudgetKu
            </span>
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
            
            @if (Auth::user()->roles == "Owner")
                <li class="menu-item {{ request()->is('pages/admin/dashboard') ? 'open' : '' }}">
                    <a href="{{ route('admin.dashboard') }}"
                        class="menu-link {{ request()->is('pages/admin/dashboard') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">dashboard</span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>
            @else
                <li class="menu-item {{ request()->is('pages/customer/dashboard') ? 'open' : '' }}">
                    <a href="{{ route('customer.dashboard') }}"
                        class="menu-link {{ request()->is('pages/customer/dashboard') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">dashboard</span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>
            @endif

            <li class="menu-title small text-uppercase">
                <span class="menu-title-text">CATEGORY</span>
            </li>

            @if (Auth::user()->roles == "Owner")
                <li class="menu-item {{ request()->is('pages/admin/category/income') ? 'open' : '' }}">
                    <a href="{{ route('admin.category.income.index') }}"
                        class="menu-link {{ request()->is('pages/admin/category/income') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">
                            list_alt
                        </span>
                        <span class="title">Income Category</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('pages/admin/category/expense') ? 'open' : '' }}">
                    <a href="{{ route('admin.category.expense.index') }}"
                        class="menu-link {{ request()->is('pages/admin/category/expense') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">
                            list_alt
                        </span>
                        <span class="title">Expense Category</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('pages/admin/payment-method') ? 'open' : '' }}">
                    <a href="{{ route('admin.payment.method.index') }}"
                        class="menu-link {{ request()->is('pages/admin/payment-method') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">
                            credit_card
                        </span>
                        <span class="title">Payment Method</span>
                    </a>
                </li>
            @endif

            @if (Auth::user()->roles == "Customer")
                <li class="menu-item {{ request()->is('pages/customer/category/income') ? 'open' : '' }}">
                    <a href="{{ route('customer.category.income.index') }}" class="menu-link {{ request()->is('pages/customer/category/income') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">
                            list_alt
                        </span>
                        <span class="title">Income Category</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('pages/customer/category/expense') ? 'open' : '' }}">
                    <a href="{{ route('customer.category.expense.index') }}" class="menu-link {{ request()->is('pages/customer/category/expense') ? 'active' : '' }}">
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

            @if (Auth::user()->roles == "Owner")
                <li class="menu-item {{ request()->is('pages/admin/income') ? 'open' : '' }}">
                    <a href="{{ route('admin.income.index') }}"
                        class="menu-link {{ request()->is('pages/admin/income') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">
                            attach_money
                        </span>
                        <span class="title">Income</span>
                    </a>
                </li>
                
                <li class="menu-item {{ request()->is('pages/admin/expense') ? 'open' : '' }}">
                    <a href="{{ route('admin.expense.index') }}"
                        class="menu-link {{ request()->is('pages/admin/expense') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">
                            payments
                        </span>
                        <span class="title">Expense</span>
                    </a>
                </li>
            @else
                <li class="menu-item {{ request()->is('pages/customer/income') ? 'open' : '' }}">
                    <a href="{{ route('customer.income.index') }}"
                        class="menu-link {{ request()->is('pages/customer/income') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">
                            attach_money
                        </span>
                        <span class="title">Income</span>
                    </a>
                </li>
                
                <li class="menu-item {{ request()->is('pages/customer/expense') ? 'open' : '' }}">
                    <a href="{{ route('customer.expense.index') }}"
                        class="menu-link {{ request()->is('pages/customer/expense') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">
                            payments
                        </span>
                        <span class="title">Expense</span>
                    </a>
                </li>
            @endif

            <li class="menu-title small text-uppercase">
                <span class="menu-title-text">ACCOUNT</span>
            </li>

            @if (Auth::user()->roles == "Owner")
                <li class="menu-item {{ request()->is('pages/admin/account') ? 'open' : '' }}">
                    <a href="{{ route('admin.account.index') }}"
                        class="menu-link {{ request()->is('pages/admin/account') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">person</span>
                        <span class="title">Profile</span>
                    </a>
                </li>
            @else
                <li class="menu-item {{ request()->is('pages/customer/account') ? 'open' : '' }}">
                    <a href="{{ route('customer.account.index') }}"
                        class="menu-link {{ request()->is('pages/customer/account') ? 'active' : '' }}">
                        <span class="material-symbols-outlined menu-icon">person</span>
                        <span class="title">Profile</span>
                    </a>
                </li>
            @endif

            <li class="menu-item">
                <a href="javascript:void()" onclick="logout()" class="menu-link">
                    <span class="material-symbols-outlined menu-icon">logout</span>
                    <span class="title">Sign Out</span>
                </a>
            </li>
        </ul>
    </aside>
</div>