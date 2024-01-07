<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title')</title>
    @stack('before-styles')
    @include('includes.admin.styles')
    @stack('after-styles')
</head>

<body>
    <div class="container-scroller">
        @include('includes.admin.sidebar')
        <div class="container-fluid page-body-wrapper">
            @include('includes.admin.navbar')
            <div class="main-panel">
                <div class="content-wrapper">
                    @yield('content')
                </div>
                <!-- content-wrapper ends -->
                <!-- partial:partials/_footer.html -->
                @include('includes.admin.footer')
                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    @include('components.modal-logout')
    @stack('before-scripts')
    @include('includes.admin.scripts')
    @stack('after-scripts')
</body>

</html>