<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title')</title>
  <!-- Include your CSS links here -->
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
  <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">

  @yield('head-script')
  @stack('head-script')
  @yield('style')
</head>                                     
<body class="hold-transition sidebar-mini">
  <div class="wrapper">

    <?php
      if (!isset($active_sub)) $active_sub = '*********';
      if (!isset($active)) $active = '*********';
    ?>

      <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
  </div>

    <!-- Navbar -->
    {{-- @include('layouts.navbar') --}}
    <!-- Sidebar -->
    @include('layouts.sidebar')
    <!-- Content Wrapper -->
    <div class="content-wrapper">
      @yield('content')
    </div>
    <!-- Footer -->
    @include('layouts.footer')


  </div>

      @if ($errors->any())
    <div style="display:none" id="error_list">{!! implode('<br/>', $errors->all()) !!}</div>
@endif

  <!-- Scripts -->
  @include('layouts.scripts')

  @yield('scripts')
  @stack('scripts')
</body>
</html>
