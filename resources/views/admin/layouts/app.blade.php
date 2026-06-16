<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Panel Bagindo Jaya' }}</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/admin/vendor/fontawesome-free/css/all.min.css') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

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

        .btn-outline-light:hover {
            color: #111111 !important;
            background-color: #ffffff !important;
            border-color: #ffffff !important;
        }
        
        .btn-outline-light:hover i {
            color: #111111 !important;
        }

        /* Fix close buttons for alert dismissible in SB Admin 2 (Bootstrap 4) */
        .alert-dismissible .btn-close {
            position: absolute;
            top: 0;
            right: 0;
            z-index: 2;
            padding: 0.75rem 1.25rem;
            color: inherit;
            background: transparent;
            border: 0;
            opacity: 0.5;
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
            text-shadow: 0 1px 0 #fff;
            cursor: pointer;
            transition: opacity 0.15s ease-in-out;
        }
        .alert-dismissible .btn-close:hover {
            opacity: 0.75;
            text-decoration: none;
        }
        .alert-dismissible .btn-close::before {
            content: "×";
        }
        .alert-dismissible .btn-close.btn-close-white {
            color: #fff;
            text-shadow: 0 1px 0 #000;
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

    <script>
        $(document).ready(function() {
            // Fix Bootstrap 5 style close buttons for Bootstrap 4 alert dismissal
            $(document).on('click', '[data-bs-dismiss="alert"]', function() {
                $(this).closest('.alert').alert('close');
            });
        });
    </script>

</body>

</html>