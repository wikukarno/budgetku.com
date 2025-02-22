<!-- partial:partials/_navbar.html -->
<nav class="navbar p-0 fixed-top d-flex flex-row">
    <div class="navbar-brand-wrapper d-flex d-lg-none align-items-center justify-content-center">
        <a class="navbar-brand text-white" href="{{ route('home') }}">
            <b>WIKUKARNO.COM</b>
        </a>
    </div>
    <div class="navbar-menu-wrapper flex-grow d-flex justify-content-between align-items-stretch">
        {{-- <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="mdi mdi-menu"></span>
        </button> --}}
        <ul class="navbar-nav navbar-nav-right">
            <li class="nav-item dropdown">
                <a class="nav-link" href="#">
                    <div class="navbar-profile">
                        <p class="mb-0 d-none d-sm-block d-lg-block navbar-profile-name">
                            {{ Auth::user()->name }}
                        </p>
                    </div>
                </a>
            </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
            data-toggle="offcanvas">
            <span class="mdi mdi-format-line-spacing"></span>
        </button>
    </div>
</nav>
<!-- partial -->