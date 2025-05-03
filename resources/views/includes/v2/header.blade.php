<header class="header-area bg-white mb-4 rounded-bottom-15" id="header-area">
    <div class="row align-items-center">
        <div class="col-lg-4 col-sm-6">
            <div class="left-header-content">
                <ul
                    class="d-flex align-items-center ps-0 mb-0 list-unstyled justify-content-center justify-content-sm-start">
                    <li>
                        <button class="header-burger-menu bg-transparent p-0 border-0" id="header-burger-menu">
                            <span class="material-symbols-outlined">menu</span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-lg-8 col-sm-6">
            <div class="right-header-content mt-2 mt-sm-0">
                <ul
                    class="d-flex align-items-center justify-content-center justify-content-sm-end ps-0 mb-0 list-unstyled">
                    <li class="header-right-item">
                        <div class="light-dark">
                            <button class="switch-toggle settings-btn dark-btn p-0 bg-transparent" id="switch-toggle">
                                <span class="dark"><i class="material-symbols-outlined">light_mode</i></span>
                                <span class="light"><i class="material-symbols-outlined">dark_mode</i></span>
                            </button>
                        </div>
                    </li>
                    <li class="header-right-item">
                        <button class="fullscreen-btn bg-transparent p-0 border-0" id="fullscreen-button">
                            <i class="material-symbols-outlined text-body">fullscreen</i>
                        </button>
                    </li>

                    <li class="header-right-item">
                        <div class="dropdown admin-profile">
                            <div class="d-xxl-flex align-items-center bg-transparent border-0 text-start p-0 cursor dropdown-toggle"
                                data-bs-toggle="dropdown">
                                <div class="flex-shrink-0">
                                    <img class="rounded-circle wh-40 administrator"
                                        src="{{ Auth::user()->avatar ?? asset('v2/images/administrator.jpg') }}" alt="admin">
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-none d-xxl-block">
                                            <div class="d-flex align-content-center">
                                                <h3>{{ Auth::user()->name }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    
                            <div class="dropdown-menu border-0 bg-white dropdown-menu-end">
                                <div class="d-flex align-items-center info">
                                    <div class="flex-shrink-0">
                                        <img class="rounded-circle wh-30 administrator"
                                            src="{{ Auth::user()->avatar ?? asset('v2/images/administrator.jpg') }}" alt="admin">
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h3 class="fw-medium">{{ Auth::user()->name }}</h3>
                                        <span class="fs-12">{{ Auth::user()->roles ?? 'User' }}</span>
                                    </div>
                                </div>
                                <ul class="admin-link ps-0 mb-0 list-unstyled">
                                    @if (Auth::user()->roles == "Owner")
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center text-body" href="{{ route('admin.account.index') }}">
                                                <i class="material-symbols-outlined">account_circle</i>
                                                <span class="ms-2">My Profile</span>
                                            </a>
                                        </li>
                                    @else
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center text-body" href="{{ route('customer.account.index') }}">
                                                <i class="material-symbols-outlined">account_circle</i>
                                                <span class="ms-2">My Profile</span>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                                <ul class="admin-link ps-0 mb-0 list-unstyled">
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center text-body" onclick="logout()">
                                            <i class="material-symbols-outlined">logout</i>
                                            <span class="ms-2">Logout</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>