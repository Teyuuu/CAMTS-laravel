<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - CAMTS</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: #333;
            background: #f5f7fa;
            overflow-x: hidden;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("{{ asset('images/logo.png') }}") no-repeat center center;
            background-size: 500px;
            opacity: 0.05;
            z-index: -1;
        }

        /* Sidebar */
        #sidebar {
            width: 220px;
            min-height: 100vh;
            background: linear-gradient(180deg, #e74c3c, #000);
            color: #fff;
            padding: 20px 0;
            position: fixed;
            top: 0;
            left: 0;
            transition: all 0.3s ease;
            z-index: 1000;
            overflow-y: auto;
        }

        #sidebar.collapsed {
            width: 70px;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .sidebar-header span {
            font-size: 20px;
            font-weight: bold;
            white-space: nowrap;
            transition: opacity 0.3s;
        }

        #sidebar.collapsed .sidebar-header span {
            opacity: 0;
        }

        #sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        #sidebar ul li {
            margin: 15px 0;
        }

        #sidebar ul li a {
            display: flex;
            align-items: center;
            color: #ecf0f1;
            text-decoration: none;
            padding: 10px 20px;
            transition: background 0.3s;
        }

        #sidebar ul li a:hover,
        #sidebar ul li a.active {
            background: #34495e;
            border-radius: 6px;
        }

        #sidebar ul li a span {
            margin-left: 12px;
            white-space: nowrap;
            transition: opacity 0.3s;
        }

        #sidebar.collapsed ul li a span {
            opacity: 0;
        }

        #sidebar ul li a img {
            width: 24px;
            height: 24px;
        }

        /* Hamburger Button */
        #sidebar-toggle {
            position: fixed;
            top: 15px;
            left: 15px;
            background: #e74c3c;
            border: none;
            border-radius: 6px;
            padding: 8px;
            cursor: pointer;
            z-index: 1100;
            display: none;
            transition: background 0.3s ease;
        }

        #sidebar-toggle:hover {
            background: #c0392b;
        }

        #sidebar-toggle .bar {
            display: block;
            width: 25px;
            height: 3px;
            margin: 5px auto;
            background-color: #fff;
            border-radius: 2px;
            transition: all 0.3s ease-in-out;
        }

        /* Animate hamburger into an X */
        #sidebar-toggle.active .bar:nth-child(1) {
            transform: translateY(8px) rotate(45deg);
        }
        #sidebar-toggle.active .bar:nth-child(2) {
            opacity: 0;
        }
        #sidebar-toggle.active .bar:nth-child(3) {
            transform: translateY(-8px) rotate(-45deg);
        }

        /* Content */
        #content {
            margin-left: 240px;
            padding: 30px;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }

        #sidebar.collapsed ~ #content {
            margin-left: 90px;
        }

        /* Alerts */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 500;
        }

        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .alert-danger {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .alert-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }

        /* Buttons */
        .btn-primary {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
        }

        .btn-primary:hover {
            background: #c0392b;
        }

        .badge {
            background: #e74c3c;
            color: white;
            font-size: 12px;
            font-weight: bold;
            padding: 3px 7px;
            border-radius: 12px;
            margin-left: 8px;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            #sidebar {
                transform: translateX(-100%);
                position: fixed;
                width: 220px;
                top: 0;
                left: 0;
            }

            #sidebar.mobile-open {
                transform: translateX(0);
            }

            #sidebar-toggle {
                display: block;
            }

            #content {
                margin-left: 0;
                padding: 20px;
            }

            /* Dim background when sidebar is open */
            body.sidebar-open::after {
                content: '';
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.4);
                z-index: 900;
            }
        }
    </style>
</head>
<body>
    <!-- Hamburger Menu -->
    <button id="sidebar-toggle" aria-label="Toggle sidebar">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
    </button>

    <!-- Sidebar -->
    <div id="sidebar">
        <div class="sidebar-header">
            <span>CAMTS</span>
        </div>

        <ul>
            <li>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <img src="{{ asset('images/icons/dashboard.png') }}" alt="Dashboard">
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('sales') }}" class="{{ request()->routeIs('sales*') ? 'active' : '' }}">
                    <img src="{{ asset('images/icons/sales.png') }}" alt="Sales">
                    <span>Sales</span>
                </a>
            </li>
            <li>
                <a href="{{ route('accounts') }}" class="{{ request()->routeIs('accounts*') ? 'active' : '' }}">
                    <img src="{{ asset('images/icons/accounts.png') }}" alt="Accounts">
                    <span>Accounts</span>
                </a>
            </li>
            <li>
                <a href="{{ route('delivery') }}" class="{{ request()->routeIs('delivery*') ? 'active' : '' }}">
                    <img src="{{ asset('images/icons/delivery.png') }}" alt="Delivery">
                    <span>Delivery</span>
                </a>
            </li>
            <li>
                <a href="{{ route('inventory') }}" class="{{ request()->routeIs('inventory*') ? 'active' : '' }}">
                    <img src="{{ asset('images/icons/inventory.png') }}" alt="Inventory">
                    <span>Inventory</span>
                </a>
            </li>
            <li>
                <a href="{{ route('attendance') }}" class="{{ request()->routeIs('attendance*') ? 'active' : '' }}">
                    <img src="{{ asset('images/icons/attendance.png') }}" alt="Attendance">
                    <span>Attendance</span>
                </a>
            </li>
            <li>
                <a href="{{ route('alerts') }}" class="{{ request()->routeIs('alerts*') ? 'active' : '' }}">
                    <img src="{{ asset('images/icons/alerts.png') }}" alt="Alerts">
                    <span>Alerts</span>
                    @if(isset($alert_count) && $alert_count > 0)
                        <span class="badge">{{ $alert_count }}</span>
                    @endif
                </a>
            </li>
        </ul>
    </div>

    <!-- Content -->
    <div id="content">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning">{{ session('warning') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>

    <script>
        const sidebar = document.getElementById("sidebar");
        const toggleBtn = document.getElementById("sidebar-toggle");

        toggleBtn.addEventListener("click", () => {
            if (window.innerWidth <= 1024) {
                sidebar.classList.toggle("mobile-open");
                document.body.classList.toggle("sidebar-open");
                toggleBtn.classList.toggle("active");
            } else {
                sidebar.classList.toggle("collapsed");
            }
        });

        // Close sidebar when clicking outside (mobile)
        document.addEventListener("click", (e) => {
            if (window.innerWidth <= 1024) {
                if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                    sidebar.classList.remove("mobile-open");
                    document.body.classList.remove("sidebar-open");
                    toggleBtn.classList.remove("active");
                }
            }
        });

        // Auto-hide alerts
        setTimeout(() => {
            document.querySelectorAll(".alert").forEach(alert => {
                alert.style.transition = "opacity 0.5s";
                alert.style.opacity = "0";
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>
