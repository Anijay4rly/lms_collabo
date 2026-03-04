<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Font Awesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- AdminLTE CSS -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    <!-- DataTable CSS -->
    <link rel="stylesheet"
          href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Google Font -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    @stack('css')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    @include('admin.layouts.topbar')
    @include('admin.layouts.sidebar')

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <h1>@yield('page-title')</h1>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                @include('admin.layouts.messages')
                @yield('content')
            </div>
        </section>
    </div>

<footer class="main-footer text-center py-3 border-top">
    <div class="container">
        <span class="text-muted">
            &copy; {{ now()->year }} {{ config('app.name') }}.
            All rights reserved.
        </span>
    </div>
</footer>

</div>

<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<!-- Bootstrap 5 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

<!-- DataTable JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<!-- AdminLTE JS -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<script>
    // SweetAlert2 Modal Messages
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            Swal.fire({
                title: '✓ Success!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#28a745',
                backdrop: true,
                allowOutsideClick: true,
                didOpen: (modal) => {
                    setTimeout(() => {
                        modal.hideModal();
                    }, 3000);
                }
            });
        @endif

        @if(session('error'))
            Swal.fire({
                title: '✕ Error!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc3545',
                backdrop: true,
                allowOutsideClick: true
            });
        @endif

        @if(session('warning'))
            Swal.fire({
                title: '⚠ Warning!',
                text: '{{ session('warning') }}',
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#ffc107',
                backdrop: true,
                allowOutsideClick: true
            });
        @endif

        @if(session('info'))
            Swal.fire({
                title: 'ℹ Info',
                text: '{{ session('info') }}',
                icon: 'info',
                confirmButtonText: 'OK',
                confirmButtonColor: '#17a2b8',
                backdrop: true,
                allowOutsideClick: true
            });
        @endif
    });
</script>

@stack('js')
@yield('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
</body>
</html>
