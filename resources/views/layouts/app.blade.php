<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="shortcut icon" href="{{ url('assets/img/logo.svg') }}">
    <title>
        @yield('title')
    </title>
    @stack('before-styles')
    @include('includes.admin.styles')
    <style>
        .bullet {
            animation: pulse 1.5s linear infinite;
            background-color: #db0000;
            border-radius: 50%;
            content: "";
            display: inline-block;
            height: 10px;
            width: 10px;

        }

        @keyframes pulse {

            /* 50% {
                opacity: 0;
            } */
            100% {
                transform: scale(2);
                opacity: 0;
            }
        }
    </style>
    @stack('after-styles')
</head>

<body class="g-sidenav-show  bg-gray-100">

    @include('includes.admin.sidebar')

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">

        @include('includes.admin.navbar')

        <div class="container-fluid py-4">

            @yield('content')
        </div>
        @include('includes.admin.footer')
    </main>

    {{-- @include('includes.admin.sidebar-plugin') --}}

    @stack('before-scripts')@include('components.modal-logout')
    @include('includes.admin.scripts')
    @stack('after-scripts')
</body>

</html>