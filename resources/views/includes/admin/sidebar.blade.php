<!-- partial:partials/_sidebar.html -->
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-center fixed-top">
        <a class="sidebar-brand brand-logo text-white" href="/" style="text-decoration: none">
            {{-- <img src="assets/images/logo.svg" alt="logo" /> --}}
            <b>WIKUKARNO.COM</b>
        </a>
        <a class="sidebar-brand brand-logo-mini text-white" href="/">
            WK
        </a>
    </div>
    <ul class="nav">
        <li class="nav-item profile">
            <div class="profile-desc">
                <div class="profile-pic">
                    <div class="count-indicator">
                        <img class="img-xs rounded-circle " src="{{ asset('assets/images/bocil2.jpg') }}" alt="">
                        <span class="count bg-success"></span>
                    </div>
                    <div class="profile-name">
                        <h5 class="mb-0 font-weight-normal">{{ Auth::user()->name }}</h5>
                        <span>{{ Auth::user()->roles }}</span>
                    </div>
                </div>
                <a href="#" id="profile-dropdown" data-bs-toggle="dropdown"><i class="mdi mdi-dots-vertical"></i></a>
                <div class="dropdown-menu dropdown-menu-right sidebar-dropdown preview-list"
                    aria-labelledby="profile-dropdown">
                    <a href="#" class="dropdown-item preview-item">
                        <div class="preview-thumbnail">
                            <div class="preview-icon bg-dark rounded-circle">
                                <i class="mdi mdi-settings text-primary"></i>
                            </div>
                        </div>
                        <div class="preview-item-content">
                            <p class="preview-subject ellipsis mb-1 text-small">Account settings</p>
                        </div>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item preview-item">
                        <div class="preview-thumbnail">
                            <div class="preview-icon bg-dark rounded-circle">
                                <i class="mdi mdi-onepassword  text-info"></i>
                            </div>
                        </div>
                        <div class="preview-item-content">
                            <p class="preview-subject ellipsis mb-1 text-small">Change Password</p>
                        </div>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item preview-item">
                        <div class="preview-thumbnail">
                            <div class="preview-icon bg-dark rounded-circle">
                                <i class="mdi mdi-calendar-today text-success"></i>
                            </div>
                        </div>
                        <div class="preview-item-content">
                            <p class="preview-subject ellipsis mb-1 text-small">To-do list</p>
                        </div>
                    </a>
                </div>
            </div>
        </li>
        <li class="nav-item nav-category">
            <span class="nav-link">Navigation</span>
        </li>
        <li class="nav-item menu-items {{ request()->is('pages/dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-speedometer"></i>
                </span>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item menu-items {{ request()->is('pages/dashboard/category-finance') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('category-finance.index') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-speedometer"></i>
                </span>
                <span class="menu-title">Kategori Keuangan</span>
            </a>
        </li>
        <li class="nav-item menu-items {{ request()->is('pages/dashboard/bill') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('bill.index') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-speedometer"></i>
                </span>
                <span class="menu-title">Tagihan</span>
            </a>
        </li>
        <li class="nav-item menu-items {{ request()->is('pages/dashboard/salary') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('salary.index') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-speedometer"></i>
                </span>
                <span class="menu-title">Uang masuk</span>
            </a>
        </li>
        <li class="nav-item menu-items {{ request()->is('pages/dashboard/finance') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('finance.index') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-speedometer"></i>
                </span>
                <span class="menu-title">Uang keluar</span>
            </a>
        </li>
        <li class="nav-item menu-items {{ request()->is('pages/dashboard/about') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('about.index') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-speedometer"></i>
                </span>
                <span class="menu-title">Tentang</span>
            </a>
        </li>
        <li class="nav-item menu-items {{ request()->is('pages/dashboard/portofolio') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('portofolio.index') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-speedometer"></i>
                </span>
                <span class="menu-title">Portofolio</span>
            </a>
        </li>
        <li class="nav-item menu-items {{ request()->is('pages/dashboard/document') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('document.index') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-speedometer"></i>
                </span>
                <span class="menu-title">Document</span>
            </a>
        </li>
        <li class="nav-item menu-items {{ request()->is('pages/dashboard/account') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('account.index') }}">
                <span class="menu-icon">
                    <i class="mdi mdi-speedometer"></i>
                </span>
                <span class="menu-title">Profile</span>
            </a>
        </li>
        <li class="nav-item menu-items {{ request()->is('pages/dashboard/account') ? 'active' : '' }}">
            <a class="nav-link" href="javascript:void(0)" onclick="logout()">
                <span class="menu-icon">
                    <i class="mdi mdi-arrow-left-bold-circle"></i>
                </span>
                <span class="menu-title">Keluar</span>
            </a>
        </li>
    </ul>
</nav>
<!-- partial -->