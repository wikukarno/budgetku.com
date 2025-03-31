<!DOCTYPE html>
<html lang="zxx">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    @stack('before-styles')
    @include('includes.v2.styles')
    @stack('after-styles')
    <!-- Title -->
    <title>
        @yield('title')
    </title>
</head>

<body class="boxed-size">
    <!-- Start Preloader Area -->
    {{-- <div class="preloader" id="preloader">
        <div class="preloader">
            <div class="waviy position-relative">
                <span class="d-inline-block">T</span>
                <span class="d-inline-block">R</span>
                <span class="d-inline-block">E</span>
                <span class="d-inline-block">Z</span>
                <span class="d-inline-block">O</span>
            </div>
        </div>
    </div> --}}
    <!-- End Preloader Area -->

    <!-- Start Sidebar Area -->
    @include('includes.v2.sidebar')
    <!-- End Sidebar Area -->

    <!-- Start Main Content Area -->
    <div class="container-fluid">
        <div class="main-content d-flex flex-column">
            <!-- Start Header Area -->
            @include('includes.v2.header')
            <!-- End Header Area -->

            <div class="main-content-container overflow-hidden">
                <div id="customAlertWrapper" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>
                @yield('content')
            </div>

            <div class="flex-grow-1"></div>

            <!-- Start Footer Area -->
            @include('includes.v2.footer')
            <!-- End Footer Area -->
        </div>
    </div>
    <!-- Start Main Content Area -->

    <!-- Link Of JS File -->
    @stack('before-scripts')
    @include('includes.v2.scripts')
    @stack('after-scripts')
</body>

</html>