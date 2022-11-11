<!-- Navbar -->
<nav class="navbar navbar-expand-lg px-0 py-1 mx-4 shadow-none border-radius-xl bg-white" id="navbarBlur"
    navbar-scroll="true">
    <div class="navbar-content ms-auto">
        <ul class="navbar-nav mb-lg-0 my-3">
            <figure class="figure d-flex align-items-center me-3 pt-3">
                <p class="me-3 mt-2">Hi <b>{{ Auth::user()->name }}</b></p>
                @if (Auth::user()->avatar != null)
                <img src="{{ Storage::url(Auth::user()->avatar) }}" class="figure-img img-fluid"
                    style="object-fit: cover; border-radius: 50%; max-height: 45px" alt="">
                @else
                <img src="{{ asset('assets/img/avatar.png') }}" class="figure-img img-fluid"
                    style="object-fit: cover; border-radius: 50%; max-height: 45px" alt="">
                @endif
            </figure>
        </ul>
    </div>
    <div class="nav-item d-xl-none ps-3 d-flex align-items-center me-3">
        <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
            <div class="sidenav-toggler-inner">
                <i class="sidenav-toggler-line"></i>
                <i class="sidenav-toggler-line"></i>
                <i class="sidenav-toggler-line"></i>
            </div>
        </a>
    </div>
</nav>
<!-- End Navbar -->