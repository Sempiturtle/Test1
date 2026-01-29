<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Dashboard') | Aisat Guidance Portal</title>
    
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

        :root {
            --primary: 79, 70, 229; /* Indigo 600 */
            --primary-light: 129, 140, 248; /* Indigo 400 */
            --accent: 14, 165, 233;  /* Sky 500 */
        }

        * {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            transition: background-color 0.15s, border-color 0.15s, transform 0.15s, box-shadow 0.15s;
        }

        body.sidebar-collapsed #sidebar {
            width: 5rem;
        }

        body.sidebar-collapsed #mainContent {
            margin-left: 5rem;
        }

        body.sidebar-collapsed .sidebar-text {
            display: none;
        }

        #sidebar, #mainContent {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Light Mode Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.4s ease-out forwards;
        }

        /* Professional Shadow Utilities */
        .shadow-soft {
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.05), 0 1px 2px -1px rgb(0 0 0 / 0.05);
        }

        .shadow-card {
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
        }
    </style>
</head>

<body class="h-full bg-slate-50 text-slate-900 antialiased">

    <!-- SIDEBAR -->
    <aside id="sidebar"
        class="fixed left-0 top-0 h-screen w-64 bg-white border-r border-slate-200 shadow-lg z-50 overflow-hidden flex flex-col">
        
        <!-- Sidebar Header -->
        <div class="p-6 flex items-center justify-between border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-indigo-500 rounded-xl flex items-center justify-center shadow-md">
                    <i class="fa-solid fa-graduation-cap text-white text-lg"></i>
                </div>
                <div class="sidebar-text">
                    <h1 class="text-base font-bold tracking-tight text-slate-900 leading-none">Aisat Guidance</h1>
                    <span class="text-[10px] uppercase tracking-wider text-indigo-600 font-semibold">Portal</span>
                </div>
            </div>
            <button onclick="toggleSidebar()" class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400 transition-all">
                <i class="fa-solid fa-bars text-sm"></i>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">
            <div class="sidebar-text px-3 mb-3">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Academic Portal</span>
            </div>

            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all group {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <i class="fa-solid fa-shapes text-base {{ request()->routeIs('admin.dashboard') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-500' }}"></i>
                <span class="sidebar-text font-semibold text-sm">Dashboard</span>
                @if(request()->routeIs('admin.dashboard'))
                    <div class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-600"></div>
                @endif
            </a>

            <a href="{{ route('admin.students.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all group {{ request()->routeIs('admin.students.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <i class="fa-solid fa-users text-base {{ request()->routeIs('admin.students.*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-500' }}"></i>
                <span class="sidebar-text font-semibold text-sm">Students</span>
                @if(request()->routeIs('admin.students.*'))
                    <div class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-600"></div>
                @endif
            </a>

            <a href="{{ route('admin.attendance.logs') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all group {{ request()->routeIs('admin.attendance.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <i class="fa-solid fa-fingerprint text-base {{ request()->routeIs('admin.attendance.*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-500' }}"></i>
                <span class="sidebar-text font-semibold text-sm">Attendance</span>
                @if(request()->routeIs('admin.attendance.*'))
                    <div class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-600"></div>
                @endif
            </a>

            <a href="{{ route('admin.analytics.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all group {{ request()->routeIs('admin.analytics.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <i class="fa-solid fa-chart-line text-base {{ request()->routeIs('admin.analytics.*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-500' }}"></i>
                <span class="sidebar-text font-semibold text-sm">Analytics</span>
                @if(request()->routeIs('admin.analytics.*'))
                    <div class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-600"></div>
                @endif
            </a>

            <div class="sidebar-text h-px bg-slate-200 my-4 mx-3"></div>

            <div class="sidebar-text px-3 mb-3">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">System Preferences</span>
            </div>

            <a href="{{ route('admin.calendar.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all group {{ request()->routeIs('admin.calendar.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <i class="fa-solid fa-calendar-alt text-base {{ request()->routeIs('admin.calendar.*') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-500' }}"></i>
                <span class="sidebar-text font-semibold text-sm">Calendar</span>
                @if(request()->routeIs('admin.calendar.*'))
                    <div class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-600"></div>
                @endif
            </a>

            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-all group">
                <i class="fa-solid fa-sliders text-base text-slate-400 group-hover:text-slate-600"></i>
                <span class="sidebar-text font-semibold text-sm">Settings</span>
            </a>
        </nav>

        <!-- Sidebar Footer -->
        <div class="p-3 border-t border-slate-100 bg-slate-50/50">
            <form method="POST" action="/logout">
                @csrf
                <button type="submit"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:text-rose-600 hover:bg-rose-50 transition-all w-full group">
                    <i class="fa-solid fa-arrow-right-from-bracket text-base group-hover:scale-110"></i>
                    <span class="sidebar-text font-semibold text-sm">Sign Out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main id="mainContent" class="ml-64 min-h-screen bg-slate-50 flex flex-col">
        
        <!-- Header / Status Bar -->
        <header class="h-16 flex items-center justify-between px-8 border-b border-slate-200 bg-white sticky top-0 z-40 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-emerald-50 rounded-lg border border-emerald-200">
                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                    <span class="text-[10px] uppercase tracking-wider font-semibold text-emerald-700">System Online</span>
                </div>
            </div>

            <div class="flex items-center gap-6">
                <!-- Search -->
                <div class="hidden md:flex items-center bg-slate-50 border border-slate-200 rounded-lg px-4 py-2 focus-within:border-indigo-300 focus-within:bg-white transition-all">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs mr-3"></i>
                    <input type="text" placeholder="Search portal..." class="bg-transparent border-none focus:ring-0 text-xs text-slate-900 placeholder-slate-400 w-48">
                </div>

                <!-- Profile -->
                <div class="flex items-center gap-3 pl-6 border-l border-slate-200">
                    <div class="text-right hidden sm:block">
                        <p class="text-[11px] font-bold text-slate-900 leading-tight">ADMINISTRATOR</p>
                        <p class="text-[10px] text-indigo-600 font-semibold">Principal Access</p>
                    </div>
                    <div class="relative group">
                        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-indigo-600 to-indigo-500 flex items-center justify-center text-white shadow-md group-hover:shadow-lg transition-all">
                            <i class="fa-solid fa-user-shield text-sm"></i>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="flex-1 p-8 animate-fade-in">
            
            <!-- Breadcrumbs / Page Title Area -->
            <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[10px] font-semibold uppercase tracking-wider text-indigo-600">Navigation</span>
                        <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
                        <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">@yield('title')</span>
                    </div>
                    <h2 class="text-3xl font-bold text-slate-900 tracking-tight">@yield('title')</h2>
                </div>
                
                @yield('actions')
            </div>

            <!-- Alerts -->
            @if (session('success'))
                <div class="flex items-center gap-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-3 rounded-xl shadow-sm mb-8 animate-fade-in">
                    <i class="fa-solid fa-circle-check text-xl"></i>
                    <p class="text-sm font-semibold">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="flex items-center gap-4 bg-rose-50 border border-rose-200 text-rose-700 px-6 py-3 rounded-xl shadow-sm mb-8 animate-fade-in">
                    <i class="fa-solid fa-circle-exclamation text-xl"></i>
                    <p class="text-sm font-semibold">{{ session('error') }}</p>
                </div>
            @endif

            <div class="content-container">
                @yield('content')
            </div>
        </div>

        <!-- Footer -->
        <footer class="p-8 border-t border-slate-200 text-center mt-auto bg-white">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">
                &copy; 2026 Aisat Guidance Counseling &bull; Professional Academic System
            </p>
        </footer>
    </main>

    <script>
        function toggleSidebar() {
            document.body.classList.toggle('sidebar-collapsed');
            window.dispatchEvent(new Event('resize')); // Recalculate charts if any
        }
    </script>

    @stack('scripts')
</body>

</html>