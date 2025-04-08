<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    @include('includes.v2.styles')
    
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

    @include('includes.v2.scripts')
</body>

</html>