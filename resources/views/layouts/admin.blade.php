<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
            height: 100%;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 260px;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            transition: transform 0.3s ease;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.15);
        }

        .sidebar.collapsed {
            transform: translateX(-190px);
        }

        .sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.5;
            pointer-events: none;
        }

        .sidebar-content {
            position: relative;
            z-index: 1;
            padding: 24px 20px;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 32px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
            width: calc(100% - 260px);
        }

        .main-content.expanded {
            margin-left: 70px;
            width: calc(100% - 70px);
        }

        /* Logo */
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }

        .logo-icon {
            width: 44px;
            height: 44px;
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sidebar.collapsed .logo-text {
            display: none;
        }

        /* User Section */
        .user-box {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 14px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar.collapsed .user-box {
            padding: 10px;
            justify-content: center;
        }

        .sidebar.collapsed .user-info {
            display: none;
        }

        /* Navigation */
        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 12px;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: all 0.2s ease;
            margin-bottom: 4px;
            position: relative;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.15);
            color: white;
        }

        .nav-item.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            font-weight: 600;
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 60%;
            background: white;
            border-radius: 0 4px 4px 0;
        }

        .nav-item i {
            width: 20px;
            font-size: 18px;
            flex-shrink: 0;
            text-align: center;
        }

        .sidebar.collapsed .nav-label {
            display: none;
        }

        /* Toggle Button */
        .sidebar-toggle {
            position: fixed;
            left: 245px;
            top: 32px;
            width: 32px;
            height: 32px;
            background: white;
            border: 2px solid #e0e7ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #6366f1;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            z-index: 1001;
        }

        .sidebar-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        }

        .sidebar.collapsed ~ .sidebar-toggle {
            left: 55px;
        }

        .sidebar.collapsed ~ .sidebar-toggle i {
            transform: rotate(180deg);
        }

        /* Footer */
        .sidebar-footer {
            margin-top: auto;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            text-align: center;
        }

        .sidebar.collapsed .sidebar-footer {
            display: none;
        }

        /* Alerts */
        .alert {
            animation: slideDown 0.3s ease-out;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 24px;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <aside id="sidebar" class="sidebar">
        <div class="sidebar-content">
            
            <!-- Logo -->
            <div class="logo">
                <div class="logo-icon">
                    <i class="fa-solid fa-school text-white text-xl"></i>
                </div>
                <div class="logo-text">
                    <h1 class="text-lg font-bold text-white leading-tight">AISAT</h1>
                    <p class="text-xs text-white/80 font-medium">Counseling System</p>
                </div>
            </div>

            <!-- User Box -->
            <div class="user-box">
                <div class="h-10 w-10 rounded-full bg-white/30 flex items-center justify-center text-white font-bold text-sm border-2 border-white/50 flex-shrink-0">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <div class="user-info flex-1">
                    <p class="text-white font-semibold text-sm">Admin</p>
                    <p class="text-white/70 text-xs">System Administrator</p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto">
                <a href="{{ route('admin.dashboard') }}" 
                   class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-house"></i>
                    <span class="nav-label text-sm font-medium">Dashboard</span>
                </a>
                
                <a href="{{ route('admin.students.index') }}" 
                   class="nav-item {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-graduate"></i>
                    <span class="nav-label text-sm font-medium">Students</span>
                </a>
                
                <a href="{{ route('admin.attendance.logs') }}" 
                   class="nav-item {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-list-check"></i>
                    <span class="nav-label text-sm font-medium">Attendance</span>
                </a>

                <div class="border-t border-white/20 my-4"></div>

                <a href="#" class="nav-item">
                    <i class="fa-solid fa-chart-bar"></i>
                    <span class="nav-label text-sm font-medium">Reports</span>
                </a>

                <a href="#" class="nav-item">
                    <i class="fa-solid fa-gear"></i>
                    <span class="nav-label text-sm font-medium">Settings</span>
                </a>
            </nav>

            <!-- Logout -->
            <form method="POST" action="/logout">
                @csrf
                <button type="submit" class="nav-item w-full hover:bg-red-500/90 mt-4">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span class="nav-label text-sm font-medium">Logout</span>
                </button>
            </form>

            <!-- Footer -->
            <div class="sidebar-footer mt-4">
                <p class="text-white/60 text-xs">© 2025 AISAT</p>
                <p class="text-white/60 text-xs">Version 1.0.0</p>
            </div>

        </div>
    </aside>

    <!-- Toggle Button -->
    <button class="sidebar-toggle" onclick="toggleSidebar()">
        <i class="fa-solid fa-chevron-left text-sm"></i>
    </button>

    <!-- MAIN CONTENT -->
    <main id="mainContent" class="main-content">
        
        <!-- Alerts -->
        @if (session('success'))
            <div class="alert bg-green-50 border-l-4 border-green-500 text-green-800">
                <i class="fa-solid fa-check-circle text-xl text-green-600 flex-shrink-0"></i>
                <div>
                    <p class="font-semibold">Success!</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="alert bg-red-50 border-l-4 border-red-500 text-red-800">
                <i class="fa-solid fa-exclamation-circle text-xl text-red-600 flex-shrink-0"></i>
                <div>
                    <p class="font-semibold">Error!</p>
                    <p class="text-sm">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if (session('info'))
            <div class="alert bg-blue-50 border-l-4 border-blue-500 text-blue-800">
                <i class="fa-solid fa-info-circle text-xl text-blue-600 flex-shrink-0"></i>
                <div>
                    <p class="font-semibold">Info!</p>
                    <p class="text-sm">{{ session('info') }}</p>
                </div>
            </div>
        @endif

        <!-- Page Content -->
        @yield('content')
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        }
    </script>

    @stack('scripts')
</body>

</html>