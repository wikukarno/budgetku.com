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
                    {{-- <li class="header-right-item">
                        <div class="dropdown notifications noti">
                            <button class="btn btn-secondary border-0 p-0 position-relative badge" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="material-symbols-outlined">notifications</span>
                            </button>
                            <div class="dropdown-menu dropdown-lg p-0 border-0 p-0 dropdown-menu-end">
                                <div class="d-flex justify-content-between align-items-center title">
                                    <span class="fw-semibold fs-15 text-secondary">Notifications <span
                                            class="fw-normal text-body fs-14">(03)</span></span>
                                    <button class="p-0 m-0 bg-transparent border-0 fs-14 text-primary">Clear
                                        All</button>
                                </div>

                                <div class="max-h-217" data-simplebar>
                                    <div class="notification-menu">
                                        <a href="notification.html" class="dropdown-item">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <i class="material-symbols-outlined text-primary">sms</i>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <p>You have requested to <span class="fw-semibold">withdrawal</span>
                                                    </p>
                                                    <span class="fs-13">2 hrs ago</span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="notification-menu unseen">
                                        <a href="notification.html" class="dropdown-item">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <i class="material-symbols-outlined text-info">person</i>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <p>A new user added in Trezo</p>
                                                    <span class="fs-13">3 hrs ago</span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="notification-menu">
                                        <a href="notification.html" class="dropdown-item">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <i
                                                        class="material-symbols-outlined text-success">mark_email_unread</i>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <p>You have requested to <span class="fw-semibold">withdrawal</span>
                                                    </p>
                                                    <span class="fs-13">1 day ago</span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="notification-menu">
                                        <a href="notification.html" class="dropdown-item">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <i class="material-symbols-outlined text-primary">sms</i>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <p>You have requested to <span class="fw-semibold">withdrawal</span>
                                                    </p>
                                                    <span class="fs-13">2 hrs ago</span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="notification-menu unseen">
                                        <a href="notification.html" class="dropdown-item">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <i class="material-symbols-outlined text-info">person</i>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <p>A new user added in Trezo</p>
                                                    <span class="fs-13">3 hrs ago</span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="notification-menu">
                                        <a href="notification.html" class="dropdown-item">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <i
                                                        class="material-symbols-outlined text-success">mark_email_unread</i>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <p>You have requested to <span class="fw-semibold">withdrawal</span>
                                                    </p>
                                                    <span class="fs-13">1 day ago</span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>

                                <a href="notification.html"
                                    class="dropdown-item text-center text-primary d-block view-all fw-medium rounded-bottom-3">
                                    <span>See All Notifications </span>
                                </a>
                            </div>
                        </div>
                    </li> --}}
                    <li class="header-right-item">
                        <div class="dropdown admin-profile">
                            <div class="d-xxl-flex align-items-center bg-transparent border-0 text-start p-0 cursor dropdown-toggle"
                                data-bs-toggle="dropdown">
                                <div class="flex-shrink-0">
                                    <img class="rounded-circle wh-40 administrator"
                                        src="{{ asset('v2/images/administrator.jpg') }}" alt="admin">
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-none d-xxl-block">
                                            <div class="d-flex align-content-center">
                                                <h3>
                                                    {{ Auth::user()->name }}
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="dropdown-menu border-0 bg-white dropdown-menu-end">
                                <div class="d-flex align-items-center info">
                                    <div class="flex-shrink-0">
                                        <img class="rounded-circle wh-30 administrator"
                                            src="{{ asset('v2/images/administrator.jpg') }}" alt="admin">
                                    </div>
                                    <div class="flex-grow-1 ms-2">
                                        <h3 class="fw-medium">
                                            {{ Auth::user()->name }}
                                        </h3>
                                        <span class="fs-12">
                                            {{ Auth::user()->roles }}
                                        </span>
                                    </div>
                                </div>
                                <ul class="admin-link ps-0 mb-0 list-unstyled">
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center text-body"
                                            href="{{ route('customer.account.index') }}">
                                            <i class="material-symbols-outlined">account_circle</i>
                                            <span class="ms-2">My Profile</span>
                                        </a>
                                    </li>
                                    {{-- <li>
                                        <a class="dropdown-item d-flex align-items-center text-body"
                                            href="my-profile.html">
                                            <i class="material-symbols-outlined">credit_card </i>
                                            <span class="ms-2">Billing</span>
                                        </a>
                                    </li> --}}
                                </ul>
                                <ul class="admin-link ps-0 mb-0 list-unstyled">
                                    {{-- <li>
                                        <a class="dropdown-item d-flex align-items-center text-body"
                                            href="settings.html">
                                            <i class="material-symbols-outlined">settings </i>
                                            <span class="ms-2">Settings</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center text-body"
                                            href="tickets.html">
                                            <i class="material-symbols-outlined">support</i>
                                            <span class="ms-2">Support</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center text-body"
                                            href="lock-screen.html">
                                            <i class="material-symbols-outlined">lock</i>
                                            <span class="ms-2">Lock Screen</span>
                                        </a>
                                    </li> --}}
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