<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="{{ url('assets/img/logo.svg') }}">
    <title>
        Administrator Of Wiku Karno
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