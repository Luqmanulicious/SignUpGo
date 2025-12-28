<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SignUpGo')</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f5f6fa;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 260px;
            background: #2c3e50;
            color: white;
            padding: 2rem;
            position: fixed;
            height: 100vh;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
        }

        .sidebar-header img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 1rem;
        }

        .nav-menu {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.8rem 1rem;
            color: #ecf0f1;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: #34495e;
        }

        .nav-link.active {
            background: #3498db;
        }

        .nav-link.logout {
            background: #e74c3c;
            color: white;
            margin-top: 1rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 1rem;
        }

        .nav-link.logout:hover {
            background: #c0392b;
            color: white;
        }

        /* Main Content Area */
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 2rem;
            width: calc(100% - 260px);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                padding: 1rem;
            }

            .sidebar-header span,
            .nav-link span {
                display: none;
            }

            .main-content {
                margin-left: 70px;
                width: calc(100% - 70px);
            }
        }

        @yield('styles')
    </style>
</head>
<body>
    @include('partials.sidebar')

    <div class="main-content">
        @yield('content')
    </div>

    <!-- Toast Notification Component -->
    @include('components.toast-notification')

    @yield('scripts')
</body>
</html>
