<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    @include('includes.auth.styles')
    
    <!-- Title -->
    <title>
        @yield('title')
    </title>
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

    @include('includes.auth.scripts')
</body>

</html>


{{-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('pavicon.ico') }}" />
    <link rel="icon" type="image/png" href="{{ asset('pavicon.ico') }}" />
    <title>
        @yield('title')
    </title>
    @include('includes.admin.styles')
</head>

<body class="">
    <main class="main-content  mt-0">
        @yield('content')
        @include('includes.admin.scripts')
    </main>
</body>

</html> --}}