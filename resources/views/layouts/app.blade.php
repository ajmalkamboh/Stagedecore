<!DOCTYPE html>
<html data-bs-theme="light" lang="en-US" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') | {{ $globalSettings->business_name ?? 'Stage Decor' }}</title>

    <!-- Favicons -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('falcon/assets/img/favicons/favicon.ico') }}">
    <script src="{{ asset('falcon/assets/js/config.js') }}"></script>
    <script src="{{ asset('falcon/vendors/simplebar/simplebar.min.js') }}"></script>

    <!-- Stylesheets -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">
    <link href="{{ asset('falcon/vendors/simplebar/simplebar.min.css') }}" rel="stylesheet">
    <link href="{{ asset('falcon/assets/css/theme.min.css') }}" rel="stylesheet" id="style-default">
    <link href="{{ asset('falcon/assets/css/user.min.css') }}" rel="stylesheet" id="user-style-default">

    <!-- Custom CSS helpers for print and badge enhancements -->
    <style>
        .table > :not(caption) > * > * {
            padding: 0.65rem 0.85rem;
            vertical-align: middle;
        }
        .badge-subtle-confirmed { background-color: #d1e7dd; color: #0f5132; }
        .badge-subtle-tentative { background-color: #cff4fc; color: #055160; }
        .badge-subtle-completed { background-color: #cfe2ff; color: #084298; }
        .badge-subtle-cancelled { background-color: #f8d7da; color: #842029; }
        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            .content { margin: 0 !important; padding: 0 !important; }
            body { background: #fff !important; }
        }
        @media screen {
            .print-only { display: none !important; }
        }
    </style>

    @livewireStyles
    @stack('styles')
</head>

<body>
    <main class="main" id="top">
        <div class="container-fluid" data-layout="container">
            <!-- Sidebar Navigation -->
            <nav class="navbar navbar-light navbar-vertical navbar-expand-xl no-print">
                <div class="d-flex align-items-center">
                    <div class="toggle-icon-wrapper">
                        <button class="btn navbar-toggler-humburger-icon navbar-vertical-toggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Toggle Navigation">
                            <span class="navbar-toggle-icon"><span class="toggle-line"></span></span>
                        </button>
                    </div>
                    <a class="navbar-brand" href="{{ route('dashboard') }}">
                        <div class="d-flex align-items-center py-3">
                            <span class="fas fa-crown text-warning fs-3 me-2"></span>
                            <span class="font-sans-serif fw-bolder fs-3 text-primary">{{ Str::limit($globalSettings->business_name ?? 'StageDecor', 16) }}</span>
                        </div>
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
                    <div class="navbar-vertical-content scrollbar">
                        <ul class="navbar-nav flex-column mb-3" id="navbarVerticalNav">
                            <!-- Dashboard -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="fas fa-chart-pie"></span></span>
                                        <span class="nav-link-text ps-1">Dashboard</span>
                                    </div>
                                </a>
                            </li>

                            <!-- OPERATIONS SECTION -->
                            <li class="nav-item">
                                <div class="row navbar-vertical-label-wrapper mt-3 mb-2">
                                    <div class="col-auto navbar-vertical-label">OPERATIONS</div>
                                    <div class="col ps-0"><hr class="mb-0 navbar-vertical-divider" /></div>
                                </div>
                                
                                <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="fas fa-users"></span></span>
                                        <span class="nav-link-text ps-1">Customers</span>
                                    </div>
                                </a>

                                <a class="nav-link {{ request()->routeIs('bookings.*') ? 'active' : '' }}" href="{{ route('bookings.index') }}">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="fas fa-calendar-check"></span></span>
                                        <span class="nav-link-text ps-1">Stage Bookings</span>
                                    </div>
                                </a>

                                <a class="nav-link {{ request()->routeIs('calendar.*') ? 'active' : '' }}" href="{{ route('calendar.index') }}">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="fas fa-calendar-alt"></span></span>
                                        <span class="nav-link-text ps-1">Booking Calendar</span>
                                    </div>
                                </a>
                            </li>

                            <!-- FINANCE SECTION -->
                            <li class="nav-item">
                                <div class="row navbar-vertical-label-wrapper mt-3 mb-2">
                                    <div class="col-auto navbar-vertical-label">FINANCE</div>
                                    <div class="col ps-0"><hr class="mb-0 navbar-vertical-divider" /></div>
                                </div>

                                <a class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}" href="{{ route('payments.index') }}">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="fas fa-money-bill-wave"></span></span>
                                        <span class="nav-link-text ps-1">Payments</span>
                                    </div>
                                </a>

                                <a class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}" href="{{ route('expenses.index') }}">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="fas fa-receipt"></span></span>
                                        <span class="nav-link-text ps-1">Expenses</span>
                                    </div>
                                </a>

                                <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="fas fa-file-invoice-dollar"></span></span>
                                        <span class="nav-link-text ps-1">Reports &amp; Profit/Loss</span>
                                    </div>
                                </a>
                            </li>

                            <!-- SETTINGS SECTION -->
                            <li class="nav-item">
                                <div class="row navbar-vertical-label-wrapper mt-3 mb-2">
                                    <div class="col-auto navbar-vertical-label">SETTINGS</div>
                                    <div class="col ps-0"><hr class="mb-0 navbar-vertical-divider" /></div>
                                </div>

                                <a class="nav-link {{ request()->routeIs('event-types.*') ? 'active' : '' }}" href="{{ route('event-types.index') }}">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="fas fa-glass-cheers"></span></span>
                                        <span class="nav-link-text ps-1">Event Types</span>
                                    </div>
                                </a>

                                <a class="nav-link {{ request()->routeIs('venues.*') ? 'active' : '' }}" href="{{ route('venues.index') }}">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="fas fa-hotel"></span></span>
                                        <span class="nav-link-text ps-1">Venues</span>
                                    </div>
                                </a>

                                <a class="nav-link {{ request()->routeIs('decoration-categories.*') ? 'active' : '' }}" href="{{ route('decoration-categories.index') }}">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="fas fa-holly-berry"></span></span>
                                        <span class="nav-link-text ps-1">Decoration Categories</span>
                                    </div>
                                </a>

                                @if(auth()->user() && auth()->user()->isAdmin())
                                <a class="nav-link {{ request()->routeIs('settings.business') ? 'active' : '' }}" href="{{ route('settings.business') }}">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="fas fa-cog"></span></span>
                                        <span class="nav-link-text ps-1">Business Settings</span>
                                    </div>
                                </a>

                                <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="fas fa-user-shield"></span></span>
                                        <span class="nav-link-text ps-1">User Management</span>
                                    </div>
                                </a>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Main Content Container -->
            <div class="content">
                <!-- Top Navbar -->
                <nav class="navbar navbar-light navbar-glass navbar-top navbar-expand no-print mb-3">
                    <button class="btn navbar-toggler-humburger-icon navbar-toggler me-1 me-sm-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse" aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation">
                        <span class="navbar-toggle-icon"><span class="toggle-line"></span></span>
                    </button>
                    <a class="navbar-brand me-1 me-sm-3 d-xl-none" href="{{ route('dashboard') }}">
                        <div class="d-flex align-items-center"><span class="fas fa-crown text-warning fs-3 me-2"></span><span class="font-sans-serif fw-bolder">{{ Str::limit($globalSettings->business_name ?? 'StageDecor', 14) }}</span></div>
                    </a>

                    <div class="d-none d-md-flex align-items-center">
                        <span class="badge badge-subtle-primary me-2"><i class="fas fa-clock me-1"></i> Pakistan (Asia/Karachi)</span>
                        <span class="text-secondary small">{{ now()->format('l, d M Y') }}</span>
                    </div>

                    <ul class="navbar-nav navbar-nav-icons ms-auto flex-row align-items-center">
                        <!-- User Account Menu -->
                        <li class="nav-item dropdown">
                            <a class="nav-link pe-0 ps-2" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xl">
                                        <div class="avatar-name rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; font-weight: bold;">
                                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                        </div>
                                    </div>
                                    <div class="ms-2 d-none d-sm-block text-start">
                                        <div class="fw-bold fs-10 text-900 lh-1">{{ auth()->user()->name ?? 'User' }}</div>
                                        <small class="text-500 fs-11 text-capitalize">{{ auth()->user()->role ?? 'Staff' }}</small>
                                    </div>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-caret dropdown-menu-end py-0" aria-labelledby="navbarDropdownUser">
                                <div class="bg-white dark__bg-1000 rounded-2 py-2">
                                    <div class="px-3 py-1">
                                        <div class="fw-bold">{{ auth()->user()->name ?? 'User' }}</div>
                                        <div class="fs-10 text-500">{{ auth()->user()->email ?? '' }}</div>
                                    </div>
                                    <div class="dropdown-divider"></div>
                                    @if(auth()->user() && auth()->user()->isAdmin())
                                    <a class="dropdown-item" href="{{ route('settings.business') }}"><i class="fas fa-cog me-2"></i> Settings</a>
                                    <a class="dropdown-item" href="{{ route('users.index') }}"><i class="fas fa-users-cog me-2"></i> Users</a>
                                    <div class="dropdown-divider"></div>
                                    @endif
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    </ul>
                </nav>

                <!-- Notifications & Flash Messages -->
                @if(session('success'))
                <div class="alert alert-success border-2 d-flex align-items-center mb-3 no-print" role="alert">
                    <div class="bg-success me-3 icon-item text-white rounded-circle"><span class="fas fa-check-circle"></span></div>
                    <p class="mb-0 flex-1 fw-semi-bold">{{ session('success') }}</p>
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger border-2 d-flex align-items-center mb-3 no-print" role="alert">
                    <div class="bg-danger me-3 icon-item text-white rounded-circle"><span class="fas fa-exclamation-triangle"></span></div>
                    <p class="mb-0 flex-1 fw-semi-bold">{{ session('error') }}</p>
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger mb-3 no-print" role="alert">
                    <div class="fw-bold mb-1"><span class="fas fa-exclamation-circle me-1"></span> Please correct the following errors:</div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Page Content -->
                @yield('content')

                <!-- Footer -->
                <footer class="footer mt-4 no-print">
                    <div class="row g-0 justify-content-between fs-10 mt-4 mb-3">
                        <div class="col-12 col-sm-auto text-center">
                            <p class="mb-0 text-600">
                                {{ $globalSettings->business_name ?? 'Stage Decor' }} &copy; {{ date('Y') }}. All rights reserved.
                            </p>
                        </div>
                        <div class="col-12 col-sm-auto text-center">
                            <p class="mb-0 text-600">Currency: <span class="fw-bold">{{ $globalSettings->currency ?? 'PKR' }} ({{ $globalSettings->currency_symbol ?? 'Rs.' }})</span> | Timezone: <span class="fw-bold">Asia/Karachi</span></p>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </main>

    <!-- JavaScripts -->
    <script src="{{ asset('falcon/vendors/popper/popper.min.js') }}"></script>
    <script src="{{ asset('falcon/vendors/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('falcon/vendors/anchorjs/anchor.min.js') }}"></script>
    <script src="{{ asset('falcon/vendors/is/is.min.js') }}"></script>
    <script src="{{ asset('falcon/vendors/fontawesome/all.min.js') }}"></script>
    <script src="{{ asset('falcon/vendors/lodash/lodash.min.js') }}"></script>
    <script src="{{ asset('falcon/assets/js/theme.js') }}"></script>

    @livewireScripts
    @stack('scripts')
</body>
</html>
