<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Dashboard')</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>

<body class="flex bg-gray-900 text-white min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-black/50 backdrop-blur border-r border-white/10 p-6 flex flex-col">
        <h2 class="text-2xl font-bold text-indigo-400 mb-6">RFID Admin</h2>
        <nav class="flex flex-col space-y-2">
            <a href="{{ route('admin.dashboard') }}"
                class="px-4 py-2 rounded hover:bg-indigo-500/20 transition">Dashboard</a>
            <a href="{{ route('admin.students.index') }}"
                class="px-4 py-2 rounded hover:bg-indigo-500/20 transition">Students</a>
            <a href="{{ route('admin.attendance.logs') }}"
                class="px-4 py-2 rounded hover:bg-indigo-500/20 transition">Attendance Logs</a>
            <form method="POST" action="/logout">
                @csrf
                <button class="px-4 py-2 rounded hover:bg-red-500/20 w-full mt-4">Logout</button>
            </form>
        </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 p-8 overflow-auto">
        @yield('content')
    </main>

    <!-- HIDDEN RFID FORM -->
    <form id="rfidForm" method="POST" action="{{ route('admin.rfid.tap') }}">
        @csrf
        <input id="rfidInput" name="rfid_uid" class="hidden">
    </form>

    <script>
        let buffer = '';
        document.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                document.getElementById('rfidInput').value = buffer;
                document.getElementById('rfidForm').submit();
                buffer = '';
            } else {
                buffer += e.key;
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
