<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @if (app()->environment('production'))
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline'; worker-src 'self' blob:; child-src 'self' blob:; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; img-src 'self' data: blob:; font-src 'self' https://fonts.gstatic.com; connect-src 'self'; frame-ancestors 'self'; base-uri 'self'">
    @endif

    <!-- Include v2 CSS Files -->
    <link rel="stylesheet" href="{{ asset('v2/css/sidebar-menu.css') }}">
    <link rel="stylesheet" href="{{ asset('v2/css/simplebar.css') }}">
    <link rel="stylesheet" href="{{ asset('v2/css/apexcharts.css') }}">
    <link rel="stylesheet" href="{{ asset('v2/css/prism.css') }}">
    <link rel="stylesheet" href="{{ asset('v2/css/rangeslider.css') }}">
    <link rel="stylesheet" href="{{ asset('v2/css/sweetalert.min.css') }}">
    <link rel="stylesheet" href="{{ asset('v2/css/quill.snow.css') }}">
    <link rel="stylesheet" href="{{ asset('v2/css/google-icon.css') }}">
    <link rel="stylesheet" href="{{ asset('v2/css/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('v2/css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('v2/css/fullcalendar.main.css') }}">
    <link rel="stylesheet" href="{{ asset('v2/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('v2/css/custom.css') }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('v2/images/favicon.png') }}">
    
    @inertiaHead
    <script>
        window.E2EE_MEMORY_ONLY = true; // keep E2EE storage key only in memory (no localStorage)
        window.E2EE_SHARE_ACROSS_TABS = true; // share via SharedWorker for current browser session
    </script>
    {{-- Samakan dengan layouts/auth: hanya load JS via Vite agar CSS v2 tidak tertimpa bootstrap dari app.scss --}}
    @vite(['resources/js/app.js'])
    <title inertia>BudgetKu</title>
</head>
<body class="boxed-size">
    @inertia
    @include('includes.v2.scripts')
</body>
</html>
