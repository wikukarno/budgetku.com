<!DOCTYPE html>
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

</html>