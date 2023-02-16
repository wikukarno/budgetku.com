<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-white"
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href=" {{ route('dashboard') }} ">
            <img src="{{ asset('assets/img/logo.png') }}" class="navbar-brand-img h-100" alt="main_logo">
            <span class="ms-1 font-weight-bold">WIKUKARNO.ID</span>
        </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link {{ (request()->is('pages/dashboard') ? 'active' : '') }}"
                    href="{{ route('dashboard') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-gauge-high text-white"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('pages/dashboard/category-finance') ? 'active' : '' }} {{ request()->is('pages/dashboard/keuangan/{id}/edit') ? 'active' : '' }}"
                    href="{{ route('category-finance.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-list text-white"></i>
                    </div>
                    <span class="nav-link-text ms-1">Kategori Keuangan</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('pages/dashboard/bill') ? 'active' : '' }} {{ request()->is('pages/dashboard/bill/create') ? 'active' : '' }}"
                    href="{{ route('bill.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-regular fa-list-alt text-white"></i>
                    </div>
                    <span class="nav-link-text ms-1">Tagihan</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('pages/dashboard/salary') ? 'active' : '' }}"
                    href="{{ route('salary.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-money-bill-trend-up text-white"></i>
                    </div>
                    <span class="nav-link-text ms-1">Uang Masuk</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('pages/dashboard/finance') ? 'active' : '' }} {{ request()->is('pages/dashboard/keuangan/{id}/edit') ? 'active' : '' }}"
                    href="{{ route('finance.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-money-bill-trend-up text-white"></i>
                    </div>
                    <span class="nav-link-text ms-1">Uang Keluar</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('pages/dashboard/about') ? 'active' : '' }} {{ request()->is('pages/dashboard/about/create') ? 'active' : '' }}"
                    href="{{ route('about.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-regular fa-address-card text-white"></i>
                    </div>
                    <span class="nav-link-text ms-1">Tentang</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('pages/dashboard/portofolio') ? 'active' : '' }} {{ request()->is('pages/dashboard/portofolio/{id}/edit') ? 'active' : '' }}"
                    href="{{ route('portofolio.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-regular fa-images text-white"></i>
                    </div>
                    <span class="nav-link-text ms-1">Portofolio</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('pages/dashboard/document') ? 'active' : '' }} {{ request()->is('pages/dashboard/document/{id}/edit') ? 'active' : '' }}"
                    href="{{ route('document.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-file text-white"></i>
                    </div>
                    <span class="nav-link-text ms-1">Document</span>
                </a>
            </li>
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Account pages</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('pages/dashboard/account') ? 'active' : '' }}"
                    href="{{ route('account.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fa-regular fa-user text-white"></i>
                    </div>
                    <span class="nav-link-text ms-1">Profile</span>
                </a>
            </li>
        </ul>

    </div>
    <div class="sidenav-footer mx-3 ">
        <a class="btn bg-gradient-primary mt-3 w-100 text-white" href="javascript:void(0)" data-bs-toggle="modal"
            data-bs-target="#logoutModal"><i class="fa-solid fa-right-from-bracket"></i>
            Keluar</a>
    </div>
</aside>