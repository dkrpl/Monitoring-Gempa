<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Earthquake Monitoring System Dashboard" />
    <meta name="author" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EQMonitor Dashboard')</title>

    <!-- SB Admin 2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-blue: #1a237e;
            --secondary-blue: #283593;
            --accent-teal: #00bcd4;
            --warning-orange: #ff9800;
            --danger-pink: #ff4081;
            --success-green: #4caf50;
            --light-gray: #f8f9fc;
            --dark-gray: #5a5c69;
        }

        body {
            background-color: var(--light-gray);
            font-family: 'Nunito', sans-serif;
        }

        #wrapper {
            display: flex;
        }

        /* Sidebar Styles */
        #sidebar-wrapper {
            min-height: 100vh;
            margin-left: -15rem;
            transition: margin 0.25s ease-out;
            width: 15rem;
            background: linear-gradient(180deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            z-index: 1000;
        }

        #sidebar-wrapper .sidebar-heading {
            padding: 1.5rem 1rem;
            font-size: 1.2rem;
            font-weight: 800;
            color: #fff;
            text-align: center;
            background: rgba(0, 0, 0, 0.1);
        }

        #sidebar-wrapper .sidebar-heading i {
            color: var(--accent-teal);
            margin-right: 0.5rem;
        }

        #sidebar-wrapper .list-group {
            width: 15rem;
            padding: 1rem 0;
        }

        #sidebar-wrapper .list-group-item {
            border: none;
            border-radius: 0;
            padding: 1rem 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            background: transparent;
            transition: all 0.3s;
        }

        #sidebar-wrapper .list-group-item:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
        }

        #sidebar-wrapper .list-group-item.active {
            color: #fff;
            background: var(--danger-pink);
            border-left: 4px solid #fff;
        }

        #sidebar-wrapper .list-group-item i {
            width: 20px;
            margin-right: 10px;
            text-align: center;
        }

        .sidebar-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            margin: 1rem 0;
        }

        .sidebar-heading {
            color: rgba(255, 255, 255, 0.4);
            padding: 0 1.5rem;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 1rem;
        }

        /* Main Content */
        #page-content-wrapper {
            min-width: 100vw;
            width: 100%;
            transition: all 0.25s ease-out;
        }

        #wrapper.toggled #sidebar-wrapper {
            margin-left: 0;
        }

        /* Topbar */
        .navbar {
            background: #fff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            padding: 0.75rem 1rem;
        }

        .navbar .navbar-brand {
            font-weight: 700;
            color: var(--primary-blue);
        }

        .topbar .nav-item .nav-link {
            color: var(--dark-gray);
            padding: 0.5rem 1rem;
        }

        .topbar .nav-item .nav-link:hover {
            color: var(--primary-blue);
        }

        .topbar .nav-item .nav-link span {
            font-size: 0.9rem;
        }

        /* User Dropdown */
        .dropdown-toggle {
            display: flex;
            align-items: center;
        }

        .dropdown-toggle::after {
            display: none;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e3e6f0;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border-radius: 0.35rem;
        }

        .dropdown-item {
            padding: 0.5rem 1rem;
            color: var(--dark-gray);
        }

        .dropdown-item:hover {
            background-color: #f8f9fc;
            color: var(--primary-blue);
        }

        .dropdown-item i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
            color: #d1d3e2;
        }

        /* Content Area */
        .container-fluid {
            padding: 2rem;
        }

        .page-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e3e6f0;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary-blue);
            margin: 0;
        }

        .page-subtitle {
            color: var(--dark-gray);
            font-size: 1rem;
            margin-top: 0.5rem;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.2);
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.25rem;
            border-radius: 0.5rem 0.5rem 0 0 !important;
        }

        .card-header h6 {
            font-weight: 700;
            color: var(--primary-blue);
            margin: 0;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(45deg, var(--primary-blue), var(--secondary-blue));
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 0.35rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: linear-gradient(45deg, var(--secondary-blue), var(--primary-blue));
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(26, 35, 126, 0.15);
        }

        .btn-success {
            background: linear-gradient(45deg, var(--success-green), #43a047);
            border: none;
        }

        .btn-warning {
            background: linear-gradient(45deg, var(--warning-orange), #fb8c00);
            border: none;
        }

        .btn-danger {
            background: linear-gradient(45deg, var(--danger-pink), #e91e63);
            border: none;
        }

        .btn-info {
            background: linear-gradient(45deg, var(--accent-teal), #00acc1);
            border: none;
        }

        /* Badges */
        .badge-admin {
            background: linear-gradient(45deg, var(--primary-blue), #3949ab);
            color: white;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 50px;
        }

        .badge-user {
            background: linear-gradient(45deg, var(--accent-teal), #00acc1);
            color: white;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 50px;
        }

        /* Tables */
        .table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }

        .table thead th {
            background: #f8f9fc;
            border-bottom: 2px solid #e3e6f0;
            color: var(--primary-blue);
            font-weight: 700;
            padding: 1rem;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-top: 1px solid #e3e6f0;
        }

        .table tbody tr:hover {
            background-color: #f8f9fc;
        }

        /* Forms */
        .form-control {
            border: 1px solid #d1d3e2;
            border-radius: 0.35rem;
            padding: 0.75rem 1rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--accent-teal);
            box-shadow: 0 0 0 0.2rem rgba(0, 188, 212, 0.25);
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-gray);
            margin-bottom: 0.5rem;
        }

        /* Footer */
        .sticky-footer {
            background: #fff;
            border-top: 1px solid #e3e6f0;
            padding: 1.5rem 0;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--accent-teal);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-blue);
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        /* Responsive */
        @media (min-width: 768px) {
            #sidebar-wrapper {
                margin-left: 0;
            }

            #page-content-wrapper {
                min-width: 0;
                width: 100%;
            }

            #wrapper.toggled #sidebar-wrapper {
                margin-left: -15rem;
            }
        }

        @media (max-width: 767.98px) {
            .container-fluid {
                padding: 1rem;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .card-body {
                padding: 1rem;
            }
        }

        /* Custom Components */
        .avatar-lg {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #fff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .stats-card {
            text-align: center;
            padding: 2rem;
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-blue);
            line-height: 1;
        }

        .stats-label {
            color: var(--dark-gray);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 0.5rem;
        }

        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 5px;
        }

        .status-online {
            background-color: var(--success-green);
        }

        .status-offline {
            background-color: #dc3545;
        }

        .status-warning {
            background-color: var(--warning-orange);
        }

        /* Tambahkan CSS berikut ke file app.blade.php di dalam style tag */

/* Fix wrapper and sidebar */
#wrapper {
    display: flex;
    overflow: hidden; /* Tambahkan ini */
}

#sidebar-wrapper {
    min-height: 100vh;
    margin-left: -15rem;
    transition: margin 0.25s ease-out;
    width: 15rem;
    background: linear-gradient(180deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
    z-index: 1000;
    position: fixed; /* Ubah menjadi fixed */
    top: 0;
    left: 0;
    bottom: 0;
    overflow-y: auto; /* Tambahkan scroll jika konten panjang */
}

/* Sidebar content wrapper */
.sidebar-content {
    display: flex;
    flex-direction: column;
    height: 100%;
}

/* Sidebar scrolling area */
.sidebar-scroll {
    flex: 1;
    overflow-y: auto;
    padding-bottom: 20px;
}

/* Sidebar toggler tetap di bawah */
.sidebar-toggler-container {
    padding: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    margin-top: auto;
}

/* Main content area */
#page-content-wrapper {
    min-width: 100vw;
    width: 100%;
    transition: all 0.25s ease-out;
    margin-left: 0; /* Reset margin */
    overflow-x: hidden; /* Mencegah scroll horizontal */
}

/* When sidebar is toggled */
#wrapper.toggled #sidebar-wrapper {
    margin-left: 0;
}

#wrapper.toggled #page-content-wrapper {
    margin-left: 15rem;
}

/* Untuk layar desktop */
@media (min-width: 768px) {
    #sidebar-wrapper {
        margin-left: 0;
        position: fixed;
    }

    #page-content-wrapper {
        min-width: 0;
        width: 100%;
        margin-left: 15rem; /* Space untuk sidebar */
    }

    #wrapper.toggled #sidebar-wrapper {
        margin-left: -15rem;
    }

    #wrapper.toggled #page-content-wrapper {
        margin-left: 0;
    }
}

/* Untuk layar mobile */
@media (max-width: 767.98px) {
    #sidebar-wrapper {
        position: fixed;
        z-index: 1040;
    }

    #page-content-wrapper {
        margin-left: 0 !important;
    }

    #wrapper.toggled #page-content-wrapper {
        position: relative;
    }
}

    /* Scrollbar khusus untuk sidebar */
    #sidebar-wrapper::-webkit-scrollbar {
        width: 6px;
    }

    #sidebar-wrapper::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.1);
    }

    #sidebar-wrapper::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 3px;
    }

    #sidebar-wrapper::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }
    </style>

    @stack('styles')
</head>
<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <div class="sidebar-heading">
                <i class="fas fa-earth-americas"></i> EQMonitor
            </div>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Nav Items -->
            <div class="list-group list-group-flush">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}"
                   class="list-group-item list-group-item-action {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>

                <!-- Divider -->
                <hr class="sidebar-divider">

                <!-- Management Heading -->
                <div class="sidebar-heading">
                    Management
                </div>

                <!-- Users -->
                <a href="{{ route('users.index') }}"
                   class="list-group-item list-group-item-action {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-users"></i>
                    <span>User Management</span>
                </a>

                <!-- Devices -->
                <a href="{{ route('devices.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('devices.*') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-microchip"></i>
                    <span>Devices</span>
                </a>

                <!-- Earthquake Events -->
                <a href="{{ route('earthquake-events.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('earthquake-events.*') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-earth-asia"></i>
                    <span>Earthquake Events</span>
                </a>


                <!-- Device Logs -->
                <a href="{{ route('device-logs.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('device-logs.*') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-clipboard-list"></i>
                    <span>Device Logs</span>
                </a>

                <!-- Divider -->
                <hr class="sidebar-divider">

                <!-- Settings Heading -->
                <div class="sidebar-heading">
                    Settings
                </div>

                <!-- Thresholds -->
                <a href="{{ route('thresholds.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('thresholds.*') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-sliders-h"></i>
                    <span>Threshold Settings</span>
                </a>

                <!-- System Settings -->
                <a href="{{ route('settings.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-cogs"></i>
                    <span>System Settings</span>
                </a>

                <!-- Divider -->
                <hr class="sidebar-divider">

                <!-- Reports Heading -->
                <div class="sidebar-heading">
                    Reports
                </div>

                <!-- Analytics -->
                <a href="{{ route('analytics') }}" class="list-group-item list-group-item-action {{ request()->routeIs('analytics*') ? 'active' : '' }}">
                    <i class="fas fa-fw fa-chart-bar"></i>
                    <span>Analytics</span>
                </a>

                <!-- Export Data -->
                <a href="#" class="list-group-item list-group-item-action" data-toggle="collapse" data-target="#exportSubmenu">
                    <i class="fas fa-fw fa-file-export"></i>
                    <span>Export Data</span>
                    <i class="fas fa-fw fa-caret-down float-right mt-1"></i>
                </a>
                <div id="exportSubmenu" class="collapse {{ request()->routeIs('exports.*') ? 'show' : '' }}">
                    <div class="list-group-item bg-light py-2">
                        <small class="text-muted font-weight-bold">EXPORT OPTIONS:</small>
                    </div>
                    <a class="list-group-item list-group-item-action py-2 {{ request()->routeIs('exports.users') ? 'active' : '' }}"
                    href="{{ route('exports.users') }}">
                        <i class="fas fa-users mr-2"></i>Users
                    </a>
                    <a class="list-group-item list-group-item-action py-2 {{ request()->routeIs('exports.devices') ? 'active' : '' }}"
                    href="{{ route('exports.devices') }}">
                        <i class="fas fa-microchip mr-2"></i>Devices
                    </a>
                    <a class="list-group-item list-group-item-action py-2 {{ request()->routeIs('exports.earthquake-events') ? 'active' : '' }}"
                    href="{{ route('exports.earthquake-events') }}">
                        <i class="fas fa-fw fa-earth-asia mr-2"></i>Earthquake Events
                    </a>
                    <a class="list-group-item list-group-item-action py-2 {{ request()->routeIs('exports.device-logs') ? 'active' : '' }}"
                    href="{{ route('exports.device-logs') }}">
                        <i class="fas fa-history mr-2"></i>Device Logs
                    </a>
                    <a class="list-group-item list-group-item-action py-2 {{ request()->routeIs('exports.statistics') ? 'active' : '' }}"
                    href="{{ route('exports.statistics') }}">
                        <i class="fas fa-chart-bar mr-2"></i>Statistics
                    </a>
                    <a class="list-group-item list-group-item-action py-2 {{ request()->routeIs('exports.all') ? 'active' : '' }}"
                    href="{{ route('exports.all') }}">
                        <i class="fas fa-database mr-2"></i>All Data
                    </a>
                </div>
            </div>

            <!-- Sidebar Toggler -->
            <div class="text-center mt-4">
                <button class="btn btn-circle btn-sm btn-outline-light" id="sidebarToggle">
                    <i class="fas fa-chevron-left"></i>
                </button>
            </div>
        </div>

        <!-- Content Wrapper -->
        <div id="page-content-wrapper">
            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light topbar static-top shadow">
                <!-- Sidebar Toggle (Topbar) -->
                <button class="btn btn-link rounded-circle mr-3" id="sidebarToggleTop">
                    <i class="fas fa-bars"></i>
                </button>


                <!-- Topbar Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600">
                                {{ Auth::user()->name }}
                            </span>
                            @if(Auth::user()->image)
                                <img class="user-avatar" src="{{ asset('storage/' . Auth::user()->image) }}"
                                     alt="{{ Auth::user()->name }}">
                            @else
                                <div class="user-avatar bg-primary d-flex align-items-center justify-content-center text-white">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif
                        </a>
                        <!-- Dropdown - User Information -->
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                             aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="{{ route('profile.show') }}">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>

                                @if(Auth::user()->isAdmin())
                                {{-- <a class="dropdown-item" href="{{ route('settings.index') }}">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a> --}}
                                <a class="dropdown-item" href="{{ route('activity-logs.index') }}">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Activity Log
                                </a>
                                @endif
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                        </div>
                    </li>
                </ul>
            </nav>

            <!-- Main Content -->
            <div class="container-fluid">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                            @hasSection('page-subtitle')
                                <p class="page-subtitle">@yield('page-subtitle')</p>
                            @endif
                        </div>
                        @hasSection('action-button')
                            <div class="action-buttons">
                                @yield('action-button')
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Content -->
                <div class="fade-in">
                    @yield('content')
                </div>
            </div>

            <!-- Footer -->
            <footer class="sticky-footer">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; EQMonitor {{ date('Y') }}</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Scripts -->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SB Admin 2 -->
    <script src="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/js/sb-admin-2.min.js"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom Scripts -->
    <script>
        // Toggle sidebar on mobile
        $('#sidebarToggle, #sidebarToggleTop').on('click', function(e) {
            e.preventDefault();
            $('body').toggleClass('sidebar-toggled');
            $('#wrapper').toggleClass('toggled');

            // Toggle icon
            const icon = $('#sidebarToggle i');
            if (icon.hasClass('fa-chevron-left')) {
                icon.removeClass('fa-chevron-left').addClass('fa-chevron-right');
            } else {
                icon.removeClass('fa-chevron-right').addClass('fa-chevron-left');
            }
        });

        // Close sidebar when clicking outside on mobile
        $(document).on('click', function(e) {
            if ($(window).width() <= 768) {
                if (!$(e.target).closest('#sidebar-wrapper, #sidebarToggle, #sidebarToggleTop').length) {
                    if ($('#wrapper').hasClass('toggled')) {
                        $('#wrapper').removeClass('toggled');
                        $('#sidebarToggle i').removeClass('fa-chevron-right').addClass('fa-chevron-left');
                    }
                }
            }
        });

        // Initialize DataTables
        $(document).ready(function() {
            $('.data-table').DataTable({
                "pageLength": 10,
                "language": {
                    "search": "_INPUT_",
                    "searchPlaceholder": "Search..."
                },
                "responsive": true,
                "autoWidth": false,
                "order": [[0, 'desc']]
            });

            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // Delete confirmation with SweetAlert
            $('.delete-btn').on('click', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // Auto-dismiss alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeTo(500, 0).slideUp(500, function() {
                    $(this).remove();
                });
            }, 5000);

            // Scroll to top button
            $(window).scroll(function() {
                if ($(this).scrollTop() > 100) {
                    $('.scroll-to-top').fadeIn();
                } else {
                    $('.scroll-to-top').fadeOut();
                }
            });

            $('.scroll-to-top').click(function() {
                $('html, body').animate({scrollTop: 0}, 500);
                return false;
            });
        });

        // Show success/error messages
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                timer: 4000,
                showConfirmButton: true
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                html: `@foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach`,
                showConfirmButton: true
            });
        @endif

        // Image preview for file inputs
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const file = input.files[0];
            const reader = new FileReader();

            reader.onloadend = function() {
                preview.src = reader.result;
                preview.style.display = 'block';
            }

            if (file) {
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.style.display = 'none';
            }
        }

        // Update file input label
        $(document).on('change', '.custom-file-input', function() {
            const fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });

        // Password strength checker
        function checkPasswordStrength(password) {
            let strength = 0;

            // Length check
            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;

            // Character type checks
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;

            return strength;
        }

        // Real-time password strength indicator
        $('input[type="password"]').on('input', function() {
            const password = $(this).val();
            const strength = checkPasswordStrength(password);
            const indicator = $(this).next('.password-strength');

            if (indicator.length === 0) {
                $(this).after('<div class="password-strength mt-2"></div>');
            }

            const strengthText = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong', 'Very Strong'][strength];
            const strengthColor = ['danger', 'danger', 'warning', 'info', 'success', 'success'][strength];
            const width = Math.min((strength / 5) * 100, 100);

            $(this).next('.password-strength').html(`
                <div class="progress" style="height: 5px;">
                    <div class="progress-bar bg-${strengthColor}" style="width: ${width}%"></div>
                </div>
                <small class="text-${strengthColor}">${strengthText}</small>
            `);
        });
    </script>

    @stack('scripts')
</body>
</html>
