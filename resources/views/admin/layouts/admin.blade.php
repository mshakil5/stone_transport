<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale())}}">

    @php
        $company = \App\Models\CompanyDetails::select('fav_icon', 'company_name')->first();
    @endphp  

<head>
  <meta charset="utf-8">
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $company->company_name }} - Admin</title>
  
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/company/' . $company->fav_icon) }}">

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('assets/admin/plugins/fontawesome-free/css/all.min.css')}}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('assets/admin/css/adminlte.min.css')}}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{ asset('assets/admin/css/OverlayScrollbars.min.css')}}">
  <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('assets/admin/datatables/dataTables.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{ asset('assets/admin/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{ asset('assets/admin/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{ asset('assets/admin/select2/select2.min.css')}}">
  <link rel="stylesheet" href="{{ asset('assets/admin/summernote/summernote-bs4.min.css')}}">
  <link rel="stylesheet" href="{{ asset('assets/admin/css/customize.css')}}">
  

</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="{{ asset('logo.png')}}" alt="logo" height="60" width="60">
  </div>

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

      
      <li class="nav-item d-none d-sm-inline-block">
        <a class="dropdown-item" href="{{ route('clearSessionData') }}">
          Logout
        </a>
      </li>
      
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">

    <a class="brand-link" style="cursor: pointer;">
      <img src="{{ asset('avatar5.png')}}" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-bold">{{Auth::user()->name}}</span>
    </a>

    <div class="sidebar">
      @if (Auth::user() && Auth::user()->sidebar == 1)

      @include('admin.inc.business_management')

      @else

      @include('admin.inc.accounting_management')

      @endif

    </div>

  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Main content -->
    @yield('content')
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
  {{-- <footer class="main-footer">
    <strong>Copyright &copy; 2023 <a href="#">AdminLTE.io</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 3.2.0
    </div>
  </footer> --}}

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->


<!-- jQuery -->
<script src="{{ asset('assets/admin/js/jquery.min.js')}}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('assets/admin/js/jquery-ui.min.js')}}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{ asset('assets/admin/js/bootstrap.bundle.min.js')}}"></script>
<!-- overlayScrollbars -->
<script src="{{ asset('assets/admin/js/jquery.overlayScrollbars.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('assets/admin/js/adminlte.js')}}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{ asset('assets/admin/js/dashboard.js')}}"></script>
<!-- DataTables  & Plugins -->
<script src="{{ asset('assets/admin/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{ asset('assets/admin/datatables/dataTables.bootstrap4.min.js')}}"></script>

<script src="{{ asset('assets/admin/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{ asset('assets/admin/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>


<script src="{{ asset('assets/admin/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{ asset('assets/admin/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{ asset('assets/admin/datatables/jszip/jszip.min.js')}}"></script>
<script src="{{ asset('assets/admin/datatables/pdfmake/pdfmake.min.js')}}"></script>
<script src="{{ asset('assets/admin/datatables/pdfmake/vfs_fonts.js')}}"></script>

<script src="{{ asset('assets/admin/datatables-buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{ asset('assets/admin/datatables-buttons/js/buttons.print.min.js')}}"></script>
<script src="{{ asset('assets/admin/datatables-buttons/js/buttons.colVis.min.js')}}"></script>
<script src="{{ asset('assets/admin/js/sweetalert.min.js')}}"></script>
<script src="{{ asset('assets/admin/select2/select2.min.js')}}"></script>
<script src="{{ asset('assets/admin/moment/moment.min.js')}}"></script>
<script src="{{ asset('assets/admin/summernote/summernote.min.js')}}"></script>

@yield('script')

<script>
  function pagetop() {
      window.scrollTo({
          top: 0,
          behavior: 'smooth' 
      });
  }
</script>

</body>
</html>
