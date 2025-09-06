<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (app()->environment('production'))
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline'; worker-src 'self' blob:; child-src 'self' blob:; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; img-src 'self' data: blob:; font-src 'self' https://fonts.gstatic.com; connect-src 'self'; frame-ancestors 'self'; base-uri 'self'">
    @endif

    @stack('before-styles')
    @include('includes.v2.styles')
    @stack('after-styles')
    
    <!-- Title -->
    <title>
        @yield('title')
    </title>
    @vite(['resources/js/app.js'])
</head>

<body class="boxed-size bg-white">

    <!-- Start Main Content Area -->
    <div class="container">
        <div class="main-content d-flex flex-column p-0">
            <div class="m-auto m-1230">
                @yield('content')
            </div>
        </div>
    </div>
    <!-- Start Main Content Area -->

    @stack('before-scripts')
    @include('includes.v2.scripts')
    @stack('after-scripts')
</body>

</html>
