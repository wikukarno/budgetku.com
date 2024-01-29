<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('pavicon.ico') }}" />
    <link rel="icon" type="image/png" href="{{ asset('pavicon.ico') }}" />
    <title>@yield('title')</title>

    @stack('before-styles')
    @include('includes.admin.styles')
    @stack('after-styles')
</head>

<body>
    {{-- <div class="container-scroller"> --}}

        @include('includes.admin.navbar')
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            @include('includes.admin.sidebar')
            <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">
                    @yield('content')
                </div>
                <!-- content-wrapper ends -->
                @include('includes.admin.footer')
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    {{-- </div> --}}
    <!-- container-scroller -->
    @stack('before-scripts')
    @include('includes.admin.scripts')
    @stack('after-scripts')
</body>

</html>