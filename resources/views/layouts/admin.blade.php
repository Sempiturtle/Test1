<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
</head>

<body class="flex font-sans bg-gray-100 text-black-900 min-h-screen">

    <!-- SIDEBAR -->
    <aside x-data="{ open: true }"
           class="fixed left-0 top-0 h-full flex flex-col transition-all duration-300
                  bg-gradient-to-t from-indigo-500 via-sky-500 to-emerald-500
                  shadow-lg shadow-black/50 rounded-r-2xl p-6 z-50"
           :class="open ? 'w-64' : 'w-20'">
        <!-- Brand -->
        <div class="flex items-center gap-2 mb-8">
            <i class="fa-solid fa-school text-2xl text-white"></i>
            <span class="text-xl font-bold text-white" :class="open ? '' : 'hidden'">Aisat Counseling</span>
        </div>

        <!-- Navigation -->
        <nav class="flex flex-col flex-1 gap-3">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 p-3 rounded-xl transition hover:bg-black/20"
               :class="{'bg-black/20': $route === '{{ route('admin.dashboard') }}'}">
                <i class="fa-solid fa-house text-white text-lg"></i>
                <span :class="open ? '' : 'hidden'">Dashboard</span>
            </a>
            <a href="{{ route('admin.students.index') }}"
               class="flex items-center gap-3 p-3 rounded-xl transition hover:bg-black/20">
                <i class="fa-solid fa-user-graduate text-white text-lg"></i>
                <span :class="open ? '' : 'hidden'">Students</span>
            </a>
            <a href="{{ route('admin.attendance.logs') }}"
               class="flex items-center gap-3 p-3 rounded-xl transition hover:bg-black/20">
                <i class="fa-solid fa-list-check text-white text-lg"></i>
                <span :class="open ? '' : 'hidden'">Attendance Logs</span>
            </a>
        </nav>

        <!-- Logout -->
        <form method="POST" action="/logout" class="mt-auto w-full">
            @csrf
            <button class="flex items-center gap-3 w-full p-3 rounded-xl transition hover:bg-red-500">
                <i class="fa-solid fa-right-from-bracket text-white text-lg"></i>
                <span :class="open ? '' : 'hidden'">Logout</span>
            </button>
        </form>

        <!-- Toggle -->
        <button @click="open = !open"
                class="absolute -right-5 top-5 w-10 h-10 bg-black/70 text-white rounded-full shadow-lg flex items-center justify-center hover:scale-110 transition">
            <i class="fa-solid fa-arrow-left" :class="open ? '' : 'rotate-180'"></i>
        </button>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 transition-all duration-300 ml-64 p-8" :class="open ? 'ml-64' : 'ml-20'">
        @if (session('success'))
            <p class="text-green-400 mb-4">{{ session('success') }}</p>
        @endif
        @if (session('error'))
            <p class="text-red-400 mb-4">{{ session('error') }}</p>
        @endif
        @if (session('info'))
            <p class="text-yellow-400 mb-4">{{ session('info') }}</p>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
</body>

</html>
