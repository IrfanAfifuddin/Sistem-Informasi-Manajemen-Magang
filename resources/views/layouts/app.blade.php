<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Sistem Pencatatan Magang')</title>
    
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('staradmin/dist/assets/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('staradmin/dist/assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staradmin/dist/assets/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('staradmin/dist/assets/vendors/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staradmin/dist/assets/vendors/typicons/typicons.css') }}">
    <link rel="stylesheet" href="{{ asset('staradmin/dist/assets/vendors/simple-line-icons/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('staradmin/dist/assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('staradmin/dist/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
    <!-- endinject -->
    
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('staradmin/dist/assets/css/style.css') }}">
    <!-- endinject -->
    <link rel="shortcut icon" href="{{ asset('staradmin/dist/assets/images/favicon.png') }}" />
    
    <!-- Scripts (Vite for AlpineJS & Tailwind CSS used in existing dashboards) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Notion Design System Styles -->
    <style>
      @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

      :root {
          --colors-canvas: #ffffff;
          --colors-canvas-soft: #f6f5f4;
          --colors-primary: #0075de;
          --colors-primary-active: #005bab;
          --colors-secondary: #213183;
          --colors-hairline: #e6e6e6;
          --colors-ink: rgba(0, 0, 0, 0.95);
          --colors-ink-secondary: #31302e;
          --colors-ink-muted: #615d59;
          --colors-ink-faint: #a39e98;
          
          /* Sticker Palette */
          --colors-accent-sky: #62aef0;
          --colors-accent-purple: #d6b6f6;
          --colors-accent-pink: #ff64c8;
          --colors-accent-orange: #dd5b00;
          --colors-accent-teal: #2a9d99;
          --colors-accent-green: #1aae39;
      }

      body {
          font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important;
          background-color: var(--colors-canvas-soft) !important;
          color: var(--colors-ink) !important;
      }

      .container-scroller {
          background-color: var(--colors-canvas-soft) !important;
      }

      /* Navbar styling */
      .navbar.default-layout {
          background-color: var(--colors-canvas) !important;
          border-bottom: 1px solid var(--colors-hairline) !important;
          box-shadow: none !important;
      }
      .navbar.default-layout .navbar-brand-wrapper {
          background-color: var(--colors-canvas) !important;
          border-right: 1px solid var(--colors-hairline) !important;
      }
      .navbar.default-layout .navbar-menu-wrapper {
          box-shadow: none !important;
          background-color: var(--colors-canvas) !important;
      }
      .navbar.default-layout .welcome-text {
          font-family: 'Inter', sans-serif !important;
          font-weight: 700 !important;
          color: var(--colors-ink) !important;
          letter-spacing: -1px;
      }
      .navbar.default-layout .welcome-sub-text {
          font-family: 'Inter', sans-serif !important;
          font-weight: 500 !important;
          color: var(--colors-ink-secondary) !important;
      }

      /* Sidebar styling */
      .sidebar {
          background-color: var(--colors-canvas-soft) !important;
          border-right: 1px solid var(--colors-hairline) !important;
          box-shadow: none !important;
      }
      .sidebar .nav {
          background-color: var(--colors-canvas-soft) !important;
          padding-top: 15px !important;
      }
      .sidebar .nav .nav-item {
          background-color: var(--colors-canvas-soft) !important;
          border-radius: 8px;
          margin: 2px 14px;
          transition: all 0.2s ease;
      }
      .sidebar .nav .nav-item .nav-link {
          font-family: 'Inter', sans-serif !important;
          font-weight: 500 !important;
          color: var(--colors-ink-secondary) !important;
          padding: 12px 16px !important;
          border-radius: 8px;
          transition: all 0.15s ease;
      }
      .sidebar .nav .nav-item .nav-link:hover {
          background-color: rgba(0, 0, 0, 0.04) !important;
          color: var(--colors-ink) !important;
      }
      .sidebar .nav .nav-item.active {
          background-color: rgba(0, 0, 0, 0.05) !important;
          border-radius: 8px;
      }
      .sidebar .nav .nav-item.active .nav-link {
          color: var(--colors-ink) !important;
          font-weight: 600 !important;
      }
      .sidebar .nav .nav-item.active .menu-icon {
          color: var(--colors-primary) !important;
      }

      /* Footer styling */
      .footer {
          background-color: var(--colors-canvas-soft) !important;
          border-top: 1px solid var(--colors-hairline) !important;
          color: var(--colors-ink-muted) !important;
          font-family: 'Inter', sans-serif !important;
          font-size: 13px !important;
      }

      /* Main Panel & Wrapper */
      .main-panel {
          background-color: var(--colors-canvas-soft) !important;
      }
      .content-wrapper {
          background-color: var(--colors-canvas-soft) !important;
          padding: 2rem !important;
      }

      /* Global Notion Classes */
      .notion-headline {
          font-family: 'Inter', sans-serif !important;
          color: var(--colors-ink) !important;
          font-weight: 700 !important;
          letter-spacing: -0.5px !important;
      }
      
      .notion-card {
          background-color: var(--colors-canvas) !important;
          border: 1px solid var(--colors-hairline) !important;
          border-radius: 12px !important;
          box-shadow: rgba(0,0,0,0.01) 0 0.175px 1.041px, 
                      rgba(0,0,0,0.02) 0 0.8px 2.925px, 
                      rgba(0,0,0,0.027) 0 2.025px 7.847px, 
                      rgba(0,0,0,0.04) 0 4px 18px !important;
          transition: transform 0.2s ease, box-shadow 0.2s ease;
          overflow: hidden;
      }
      .notion-card:hover {
          transform: translateY(-2px);
          box-shadow: rgba(0,0,0,0.015) 0 0.25px 1.5px, 
                      rgba(0,0,0,0.025) 0 1.2px 4px, 
                      rgba(0,0,0,0.035) 0 3px 10px, 
                      rgba(0,0,0,0.05) 0 6px 24px !important;
      }
      
      .notion-stats-card {
          background-color: var(--colors-canvas) !important;
          border: 1px solid var(--colors-hairline) !important;
          border-radius: 12px !important;
          padding: 20px !important;
          box-shadow: rgba(0,0,0,0.01) 0 0.175px 1.041px, 
                      rgba(0,0,0,0.02) 0 0.8px 2.925px, 
                      rgba(0,0,0,0.027) 0 2.025px 7.847px, 
                      rgba(0,0,0,0.04) 0 4px 18px !important;
          display: flex;
          align-items: center;
          position: relative;
      }
      .notion-stats-card::before {
          content: '';
          position: absolute;
          left: 0;
          top: 0;
          bottom: 0;
          width: 5px;
          border-top-left-radius: 12px;
          border-bottom-left-radius: 12px;
      }
      .notion-stats-card.sky::before { background-color: var(--colors-accent-sky); }
      .notion-stats-card.purple::before { background-color: var(--colors-accent-purple); }
      .notion-stats-card.teal::before { background-color: var(--colors-accent-teal); }
      .notion-stats-card.green::before { background-color: var(--colors-accent-green); }
      .notion-stats-card.orange::before { background-color: var(--colors-accent-orange); }
      .notion-stats-card.pink::before { background-color: var(--colors-accent-pink); }
      
      .stats-label {
          color: var(--colors-ink-muted) !important;
          font-size: 11px !important;
          font-weight: 600 !important;
          text-transform: uppercase;
          letter-spacing: 0.5px;
      }
      .stats-value {
          color: var(--colors-ink) !important;
          font-size: 26px !important;
          font-weight: 700 !important;
          line-height: 1;
      }

      .notion-sticker-badge {
          font-size: 12px !important;
          font-weight: 600 !important;
          letter-spacing: 0.125px !important;
          border-radius: 9999px !important;
          padding: 4px 10px !important;
          display: inline-flex;
          align-items: center;
          gap: 6px;
      }
      .sticker-teal {
          background-color: rgba(42, 157, 153, 0.1) !important;
          color: var(--colors-accent-teal) !important;
          border: 1px solid rgba(42, 157, 153, 0.15) !important;
      }
      .sticker-pink {
          background-color: rgba(255, 100, 200, 0.1) !important;
          color: var(--colors-accent-pink) !important;
          border: 1px solid rgba(255, 100, 200, 0.15) !important;
      }
      .sticker-orange {
          background-color: rgba(221, 91, 0, 0.1) !important;
          color: var(--colors-accent-orange) !important;
          border: 1px solid rgba(221, 91, 0, 0.15) !important;
      }
      .sticker-sky {
          background-color: rgba(98, 174, 240, 0.1) !important;
          color: var(--colors-accent-sky) !important;
          border: 1px solid rgba(98, 174, 240, 0.15) !important;
      }
      .sticker-purple {
          background-color: rgba(214, 182, 246, 0.15) !important;
          color: #903df5 !important;
          border: 1px solid rgba(214, 182, 246, 0.25) !important;
      }
      .sticker-green {
          background-color: rgba(26, 174, 57, 0.1) !important;
          color: var(--colors-accent-green) !important;
          border: 1px solid rgba(26, 174, 57, 0.15) !important;
      }
      .sticker-grey {
          background-color: rgba(100, 116, 139, 0.1) !important;
          color: #64748b !important;
          border: 1px solid rgba(100, 116, 139, 0.15) !important;
      }

      .table-responsive {
          border-radius: 8px;
          overflow-x: auto !important;
      }
      .table th {
          background: var(--colors-canvas-soft) !important;
          font-weight: 700 !important;
          text-transform: uppercase;
          font-size: 0.725rem !important;
          letter-spacing: 0.5px;
          color: var(--colors-ink-muted) !important;
          border: none !important;
          padding: 12px 16px !important;
      }
      .table td {
          padding: 16px 18px !important;
          vertical-align: middle !important;
          border-bottom: 1px solid var(--colors-hairline) !important;
          color: var(--colors-ink-secondary) !important;
      }
      
      .btn-notion-primary {
          background-color: var(--colors-primary) !important;
          color: #ffffff !important;
          border-radius: 9999px !important;
          font-weight: 500 !important;
          border: none !important;
          padding: 8px 20px !important;
          font-size: 14px !important;
          display: inline-flex;
          align-items: center;
          justify-content: center;
          gap: 6px;
          transition: background-color 0.15s ease, transform 0.1s ease;
      }
      .btn-notion-primary:hover {
          background-color: var(--colors-primary-active) !important;
          color: #ffffff !important;
      }
      .btn-notion-primary:active {
          transform: scale(0.96);
      }
      
      .btn-notion-utility {
          background-color: var(--colors-canvas) !important;
          color: var(--colors-ink-secondary) !important;
          border: 1px solid var(--colors-hairline) !important;
          border-radius: 8px !important;
          font-weight: 500 !important;
          padding: 6px 14px !important;
          font-size: 13px !important;
          display: inline-flex;
          align-items: center;
          justify-content: center;
          gap: 4px;
          transition: background-color 0.15s ease;
      }
      .btn-notion-utility:hover {
          background-color: var(--colors-canvas-soft) !important;
          color: var(--colors-ink) !important;
      }

      .btn-notion-danger {
          background-color: rgba(239, 68, 68, 0.08) !important;
          color: #ef4444 !important;
          border: 1px solid rgba(239, 68, 68, 0.15) !important;
          border-radius: 8px !important;
          font-weight: 500 !important;
          padding: 6px 12px !important;
          font-size: 13px !important;
          display: inline-flex;
          align-items: center;
          justify-content: center;
          transition: background-color 0.15s ease;
      }
      .btn-notion-danger:hover {
          background-color: rgba(239, 68, 68, 0.15) !important;
          color: #ef4444 !important;
      }

      .modal-content {
          border: 1px solid var(--colors-hairline) !important;
          border-radius: 12px !important;
          box-shadow: rgba(0, 0, 0, 0.05) 0 23px 52px !important;
      }

      .notion-input, .form-control, .form-select {
          border-radius: 4px !important;
          border: 1px solid #ddd !important;
          font-size: 14px !important;
          padding: 8px 12px !important;
          background-color: var(--colors-canvas) !important;
          color: var(--colors-ink) !important;
      }
      .notion-input:focus, .form-control:focus, .form-select:focus {
          border-color: var(--colors-primary) !important;
          box-shadow: rgba(0,75,222,0.15) 0 0 0 3px !important;
      }
      .form-label {
          font-weight: 600 !important;
          color: var(--colors-ink-secondary) !important;
          margin-bottom: 6px !important;
      }
    </style>
  </head>
  <body class="with-welcome-text">
    <div class="container-scroller">
      <!-- Navbar -->
      <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
          <div class="me-3">
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
              <span class="icon-menu"></span>
            </button>
          </div>
          <div>
            <a class="navbar-brand brand-logo" href="{{ route('dashboard') }}">
              <img src="{{ asset('staradmin/dist/assets/images/logo.svg') }}" alt="logo" />
            </a>
            <a class="navbar-brand brand-logo-mini" href="{{ route('dashboard') }}">
              <img src="{{ asset('staradmin/dist/assets/images/logo-mini.svg') }}" alt="logo" />
            </a>
          </div>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-top">
          <ul class="navbar-nav">
            <li class="nav-item fw-semibold d-none d-lg-block ms-0">
              <h1 class="welcome-text">Selamat Datang, <span class="text-black fw-bold">{{ Auth::user()->name }}</span></h1>
              <h3 class="welcome-sub-text">Sistem Pencatatan Magang - Role: <span class="badge bg-primary text-white">{{ strtoupper(Auth::user()->role) }}</span></h3>
            </li>
          </ul>
          <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown d-none d-lg-block user-dropdown">
              <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                <img class="img-xs rounded-circle" src="{{ asset('staradmin/dist/assets/images/faces/face8.jpg') }}" alt="Profile image">
              </a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                <div class="dropdown-header text-center">
                  <img class="img-md rounded-circle" src="{{ asset('staradmin/dist/assets/images/faces/face8.jpg') }}" alt="Profile image">
                  <p class="mb-1 mt-3 fw-semibold">{{ Auth::user()->name }}</p>
                  <p class="fw-light text-muted mb-0">{{ Auth::user()->email ?? Auth::user()->username }}</p>
                </div>
                <a href="{{ route('profile.edit') }}" class="dropdown-item">
                  <i class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i> Edit Profil
                </a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="dropdown-item border-0 w-100 text-start bg-transparent" style="outline: none;">
                    <i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i> Keluar
                  </button>
                </form>
              </div>
            </li>
          </ul>
          <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
          </button>
        </div>
      </nav>
      
      <!-- Page Body Wrapper -->
      <div class="container-fluid page-body-wrapper">
        <!-- Sidebar -->
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
          <ul class="nav">
            <li class="nav-item {{ request()->routeIs('dashboard') || request()->routeIs('admin.dashboard') || request()->routeIs('mentor.dashboard') || request()->routeIs('intern.dashboard') ? 'active' : '' }}">
              <a class="nav-link" href="{{ route('dashboard') }}">
                <i class="mdi mdi-grid-large menu-icon"></i>
                <span class="menu-title">Dashboard</span>
              </a>
            </li>
            @if(in_array(Auth::user()->role, ['admin', 'mentor']))
              <li class="nav-item {{ request()->routeIs('reports.index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reports.index') }}">
                  <i class="menu-icon mdi mdi-file-document-outline"></i>
                  <span class="menu-title">Laporan Rekap</span>
                </a>
              </li>
            @endif
            <li class="nav-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
              <a class="nav-link" href="{{ route('profile.edit') }}">
                <i class="menu-icon mdi mdi-account-circle-outline"></i>
                <span class="menu-title">Edit Profil</span>
              </a>
            </li>
            <li class="nav-item">
              <form action="{{ route('logout') }}" method="POST" class="w-100">
                @csrf
                <button type="submit" class="nav-link border-0 bg-transparent text-start w-100" style="outline: none; padding: 12px 16px !important;">
                  <i class="menu-icon mdi mdi-power"></i>
                  <span class="menu-title">Keluar</span>
                </button>
              </form>
            </li>
          </ul>
        </nav>
        
        <!-- Main Panel -->
        <div class="main-panel">
          <div class="content-wrapper">
            @yield('content')
            {{ $slot ?? '' }}
          </div>
          <!-- content-wrapper ends -->
          
          <footer class="footer">
            <div class="d-sm-flex justify-content-center justify-content-sm-between">
              <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Sistem Pencatatan Magang - Star Admin Template</span>
              <span class="float-none float-sm-end d-block mt-1 mt-sm-0 text-center">Copyright © 2026. All rights reserved.</span>
            </div>
          </footer>
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    
    <!-- plugins:js -->
    <script src="{{ asset('staradmin/dist/assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('staradmin/dist/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <!-- endinject -->
    
    <!-- inject:js -->
    <script src="{{ asset('staradmin/dist/assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('staradmin/dist/assets/js/template.js') }}"></script>
    <script src="{{ asset('staradmin/dist/assets/js/settings.js') }}"></script>
    <script src="{{ asset('staradmin/dist/assets/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('staradmin/dist/assets/js/todolist.js') }}"></script>
    <!-- endinject -->
    @stack('scripts')
    <script>
    $(document).ready(function() {
        $(document).on('click', '.btn-trigger-modal', function(e) {
            e.preventDefault();
            var target = $(this).attr('data-target-modal');
            $(target).modal('show');
        });
    });
    </script>
  </body>
</html>
