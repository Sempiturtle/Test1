<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Dashboard')</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>

<body class="flex bg-gray-100 text-gray-900 min-h-screen font-sans">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-indigo-50 shadow-lg p-6 flex flex-col">
        <h2 class="text-2xl font-bold text-indigo-700 mb-6">Aisat Counseling</h2>
        <nav class="flex flex-col space-y-2">
            <a href="{{ route('admin.dashboard') }}"
                class="px-4 py-2 rounded hover:bg-indigo-200 transition">Dashboard</a>
            <a href="{{ route('admin.students.index') }}"
                class="px-4 py-2 rounded hover:bg-indigo-200 transition">Students</a>
            <a href="{{ route('admin.attendance.logs') }}"
                class="px-4 py-2 rounded hover:bg-indigo-200 transition">Attendance Logs</a>
            <form method="POST" action="/logout">
                @csrf
                <button class="px-4 py-2 rounded hover:bg-red-200 w-full mt-4">Logout</button>
            </form>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 p-8 overflow-auto">
        <!-- Flash Messages -->
        @if (session('success'))
            <p class="text-green-600 mb-4">{{ session('success') }}</p>
        @endif
        @if (session('error'))
            <p class="text-red-600 mb-4">{{ session('error') }}</p>
        @endif
        @if (session('info'))
            <p class="text-yellow-600 mb-4">{{ session('info') }}</p>
        @endif

        @yield('content')
    </main>


</body>

</html>
