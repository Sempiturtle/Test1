<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

        * {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        .sidebar-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        /* Hide expand button by default */
        #expandButton {
            display: none;
        }

        /* Show expand button when sidebar is collapsed */
        body.sidebar-collapsed #expandButton {
            display: flex;
        }

        /* Hide collapse button when sidebar is collapsed */
        body.sidebar-collapsed #collapseButton {
            display: none;
        }

        /* Hide text elements when sidebar is collapsed */
        body.sidebar-collapsed .sidebar-text {
            display: none;
        }

        /* Adjust main content margin */
        body.sidebar-collapsed #mainContent {
            margin-left: 5rem;
        }

        /* Sidebar width when collapsed */
        body.sidebar-collapsed #sidebar {
            width: 5rem;
        }

        /* Smooth transitions */
        #sidebar,
        #mainContent,
        #expandButton {
            transition: all 0.3s ease;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-gray-100 to-gray-200 overflow-x-hidden">

    <!-- SIDEBAR -->
    <aside id="sidebar"
        class="fixed left-0 top-0 h-screen w-64 bg-gradient-to-b from-indigo-600 via-purple-600 to-purple-700 shadow-2xl z-50 overflow-y-auto">
        <!-- Pattern Overlay -->
        <div class="absolute inset-0 sidebar-pattern opacity-50 pointer-events-none"></div>

        <!-- Sidebar Content -->
        <div class="relative z-10 p-6 flex flex-col h-full">

            <!-- Logo & Collapse Button -->
            <div class="flex items-center gap-3 mb-6">
                <div
                    class="w-11 h-11 bg-white/20 backdrop-blur-sm border border-white/30 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-school text-white text-xl"></i>
                </div>
                <div class="sidebar-text flex-1">
                    <h1 class="text-lg font-bold text-white leading-tight">AISAT</h1>
                    <p class="text-xs text-white/80 font-medium">Counseling System</p>
                </div>
                <button id="collapseButton" onclick="toggleSidebar()"
                    class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-all flex-shrink-0">
                    <i class="fa-solid fa-chevron-left text-sm"></i>
                </button>
            </div>

            <!-- User Section -->
            <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl p-3.5 mb-6">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-full bg-white/30 border-2 border-white/50 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <div class="sidebar-text flex-1 min-w-0">
                        <p class="text-white font-semibold text-sm truncate">Admin</p>
                        <p class="text-white/70 text-xs truncate">System Administrator</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 space-y-1 overflow-y-auto">
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/15 transition-all group {{ request()->routeIs('admin.dashboard') ? 'bg-white/20 font-semibold' : '' }}">
                    <i class="fa-solid fa-house text-lg w-5 text-center flex-shrink-0"></i>
                    <span class="sidebar-text text-sm font-medium">Dashboard</span>
                    @if(request()->routeIs('admin.dashboard'))
                        <div class="absolute left-0 w-1 h-8 bg-white rounded-r"></div>
                    @endif
                </a>

                <a href="{{ route('admin.students.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/15 transition-all group {{ request()->routeIs('admin.students.*') ? 'bg-white/20 font-semibold' : '' }}">
                    <i class="fa-solid fa-user-graduate text-lg w-5 text-center flex-shrink-0"></i>
                    <span class="sidebar-text text-sm font-medium">Students</span>
                    @if(request()->routeIs('admin.students.*'))
                        <div class="absolute left-0 w-1 h-8 bg-white rounded-r"></div>
                    @endif
                </a>

                <a href="{{ route('admin.attendance.logs') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/15 transition-all group {{ request()->routeIs('admin.attendance.*') ? 'bg-white/20 font-semibold' : '' }}">
                    <i class="fa-solid fa-list-check text-lg w-5 text-center flex-shrink-0"></i>
                    <span class="sidebar-text text-sm font-medium">Attendance</span>
                    @if(request()->routeIs('admin.attendance.*'))
                        <div class="absolute left-0 w-1 h-8 bg-white rounded-r"></div>
                    @endif
                </a>

                <a href="{{ route('admin.analytics.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/15 transition-all group {{ request()->routeIs('admin.analytics.*') ? 'bg-white/20 font-semibold' : '' }}">
                    <i class="fa-solid fa-chart-line text-lg w-5 text-center flex-shrink-0"></i>
                    <span class="sidebar-text text-sm font-medium">Analytics</span>
                    @if(request()->routeIs('admin.analytics.*'))
                        <div class="absolute left-0 w-1 h-8 bg-white rounded-r"></div>
                    @endif
                </a>


                <div class="sidebar-text h-px bg-white/20 my-4"></div>

                <a href="#"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/15 transition-all">
                    <i class="fa-solid fa-chart-bar text-lg w-5 text-center flex-shrink-0"></i>
                    <span class="sidebar-text text-sm font-medium">Reports</span>
                </a>

                <a href="#"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-white/15 transition-all">
                    <i class="fa-solid fa-gear text-lg w-5 text-center flex-shrink-0"></i>
                    <span class="sidebar-text text-sm font-medium">Settings</span>
                </a>
            </nav>

            <!-- Logout -->
            <form method="POST" action="/logout" class="mt-4">
                @csrf
                <button type="submit"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-white/90 hover:bg-red-500/90 transition-all w-full">
                    <i class="fa-solid fa-right-from-bracket text-lg w-5 text-center flex-shrink-0"></i>
                    <span class="sidebar-text text-sm font-medium">Logout</span>
                </button>
            </form>

            <!-- Footer -->
            <div class="sidebar-text mt-4 pt-4 border-t border-white/20 text-center">
                <p class="text-white/60 text-xs">© 2025 AISAT</p>
                <p class="text-white/60 text-xs">Version 1.0.0</p>
            </div>

        </div>
    </aside>

    <!-- EXPAND BUTTON (Only visible when sidebar is collapsed) -->
    <button id="expandButton" onclick="toggleSidebar()"
        class="fixed left-16 top-8 w-8 h-8 bg-white border-2 border-indigo-100 rounded-full shadow-xl items-center justify-center text-indigo-600 hover:scale-110 transition-all z-50">
        <i class="fa-solid fa-chevron-right text-sm"></i>
    </button>

    <!-- MAIN CONTENT -->
    <main id="mainContent" class="ml-64 p-8 min-h-screen transition-all duration-300">

        <!-- Alerts -->
        @if (session('success'))
            <div
                class="flex items-start gap-3 bg-green-50 border-l-4 border-green-500 text-green-800 px-5 py-4 rounded-xl shadow-sm mb-6 animate-slideDown">
                <i class="fa-solid fa-check-circle text-xl text-green-600 flex-shrink-0 mt-0.5"></i>
                <div>
                    <p class="font-semibold">Success!</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div
                class="flex items-start gap-3 bg-red-50 border-l-4 border-red-500 text-red-800 px-5 py-4 rounded-xl shadow-sm mb-6 animate-slideDown">
                <i class="fa-solid fa-exclamation-circle text-xl text-red-600 flex-shrink-0 mt-0.5"></i>
                <div>
                    <p class="font-semibold">Error!</p>
                    <p class="text-sm">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if (session('info'))
            <div
                class="flex items-start gap-3 bg-blue-50 border-l-4 border-blue-500 text-blue-800 px-5 py-4 rounded-xl shadow-sm mb-6 animate-slideDown">
                <i class="fa-solid fa-info-circle text-xl text-blue-600 flex-shrink-0 mt-0.5"></i>
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
            document.body.classList.toggle('sidebar-collapsed');
        }
    </script>

    <style>
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

        .animate-slideDown {
            animation: slideDown 0.3s ease-out;
        }
    </style>

    @stack('scripts')
</body>

</html>