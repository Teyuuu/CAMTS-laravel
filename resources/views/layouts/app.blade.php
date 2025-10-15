<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - CAMTS</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Sidebar Container */
        #sidebar {
            width: 220px;
            min-height: 100vh;
            background: linear-gradient(180deg, #e74c3c, #000);
            color: #fff;
            padding: 20px 0;
            position: fixed;
            top: 0;
            left: 0;
            transition: width 0.3s;
            z-index: 1000;
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

        #sidebar-toggle {
            position: absolute;
            top: 20px;
            left: 20px;
            background: none;
            border: none;
            cursor: pointer;
            z-index: 1000;
        }

        #sidebar-toggle img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            transition: transform 0.3s ease;
        }

        #sidebar-toggle img:hover {
            transform: scale(1.1);
        }

        #content {
            margin-left: 240px;
            padding: 30px;
            transition: margin-left 0.3s;
            min-height: 100vh;
        }

        #sidebar.collapsed ~ #content {
            margin-left: 90px;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: #333;
            background: #f5f7fa;
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

        .badge {
            background: #e74c3c;
            color: white;
            font-size: 12px;
            font-weight: bold;
            padding: 3px 7px;
            border-radius: 12px;
            margin-left: 8px;
        }

        /* Alert Messages */
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

        /* Button styles */
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
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div id="sidebar">
        <button id="sidebar-toggle">
            <img src="{{ asset('images/logo.png') }}" alt="Toggle Sidebar" onerror="this.style.display='none'">
        </button>

        <div class="sidebar-header">
            <span>CAMTS</span>
        </div>

        <ul>
            <li>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <img src="{{ asset('images/icons/dashboard.png') }}" alt="Dashboard" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2224%22 height=%2224%22 viewBox=%220 0 24 24%22 fill=%22white%22%3E%3Cpath d=%22M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z%22/%3E%3C/svg%3E'">
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('sales') }}" class="{{ request()->routeIs('sales*') ? 'active' : '' }}">
                    <img src="{{ asset('images/icons/sales.png') }}" alt="Sales" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2224%22 height=%2224%22 viewBox=%220 0 24 24%22 fill=%22white%22%3E%3Cpath d=%22M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.67 0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87-1.5 0-2.4.68-2.4 1.64 0 .84.65 1.39 2.67 1.91s4.18 1.39 4.18 3.91c-.01 1.83-1.38 2.83-3.12 3.16z%22/%3E%3C/svg%3E'">
                    <span>Sales</span>
                </a>
            </li>
            <li>
                <a href="{{ route('accounts') }}" class="{{ request()->routeIs('accounts*') ? 'active' : '' }}">
                    <img src="{{ asset('images/icons/accounts.png') }}" alt="Accounts" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2224%22 height=%2224%22 viewBox=%220 0 24 24%22 fill=%22white%22%3E%3Cpath d=%22M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z%22/%3E%3C/svg%3E'">
                    <span>Accounts</span>
                </a>
            </li>
            <li>
                <a href="{{ route('delivery') }}" class="{{ request()->routeIs('delivery*') ? 'active' : '' }}">
                    <img src="{{ asset('images/icons/delivery.png') }}" alt="Delivery" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2224%22 height=%2224%22 viewBox=%220 0 24 24%22 fill=%22white%22%3E%3Cpath d=%22M18 18.5c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5-1.5.67-1.5 1.5.67 1.5 1.5 1.5zm1.5-9H17V12h4.46L19.5 9.5zM6 18.5c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5-1.5.67-1.5 1.5.67 1.5 1.5 1.5zM20 8l3 4v5h-2c0 1.66-1.34 3-3 3s-3-1.34-3-3H9c0 1.66-1.34 3-3 3s-3-1.34-3-3H1V6c0-1.11.89-2 2-2h14v4h3z%22/%3E%3C/svg%3E'">
                    <span>Delivery</span>
                </a>
            </li>
            <li>
                <a href="{{ route('inventory') }}" class="{{ request()->routeIs('inventory*') ? 'active' : '' }}">
                    <img src="{{ asset('images/icons/inventory.png') }}" alt="Inventory" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2224%22 height=%2224%22 viewBox=%220 0 24 24%22 fill=%22white%22%3E%3Cpath d=%22M20 2H4c-1 0-2 .9-2 2v3.01c0 .72.43 1.34 1 1.69V20c0 1.1 1.1 2 2 2h14c.9 0 2-.9 2-2V8.7c.57-.35 1-.97 1-1.69V4c0-1.1-1-2-2-2zm-5 12H9v-2h6v2zm5-7H4V4h16v3z%22/%3E%3C/svg%3E'">
                    <span>Inventory</span>
                </a>
            </li>
            <li>
                <a href="{{ route('attendance') }}" class="{{ request()->routeIs('attendance*') ? 'active' : '' }}">
                    <img src="{{ asset('images/icons/attendance.png') }}" alt="Attendance" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2224%22 height=%2224%22 viewBox=%220 0 24 24%22 fill=%22white%22%3E%3Cpath d=%22M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z%22/%3E%3C/svg%3E'">
                    <span>Attendance</span>
                </a>
            </li>
            <li>
                <a href="{{ route('alerts') }}" class="{{ request()->routeIs('alerts*') ? 'active' : '' }}">
                    <img src="{{ asset('images/icons/alerts.png') }}" alt="Alerts" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2224%22 height=%2224%22 viewBox=%220 0 24 24%22 fill=%22white%22%3E%3Cpath d=%22M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z%22/%3E%3C/svg%3E'">
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
        {{-- Flash Messages --}}
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

        {{-- Page Content --}}
        @yield('content')
    </div>

    <script>
        // Sidebar toggle
        document.getElementById("sidebar-toggle").addEventListener("click", function () {
            document.getElementById("sidebar").classList.toggle("collapsed");
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>