<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Panel BigSport' }}</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/admin/vendor/fontawesome-free/css/all.min.css') }}">

    <!-- SB Admin 2 CSS -->
    <link rel="stylesheet" href="{{ asset('assets/admin/css/sb-admin-2.min.css') }}">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/admin/css/style.css') }}">

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    @stack('styles')

    <style>
        .select2-container--default .select2-selection--single {
            height: 38px !important;
            padding: 6px 12px !important;
        }

        .select2-selection__rendered {
            margin-top: 2px;
        }
    </style>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        {{-- Sidebar --}}
        @include('admin.components.sidebar')

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                {{-- Topbar --}}
                @include('admin.components.topbar')

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    @yield('content')

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            {{-- Footer --}}
            @include('admin.components.footer')

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- JQuery -->
    <script src="{{ asset('assets/admin/vendor/jquery/jquery.min.js') }}"></script>

    <!-- Bootstrap -->
    <script src="{{ asset('assets/admin/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- JQuery Easing -->
    <script src="{{ asset('assets/admin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- SB Admin -->
    <script src="{{ asset('assets/admin/js/sb-admin-2.min.js') }}"></script>

    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    @stack('scripts')

</body>

</html>