<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title') | RFID Attendance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 font-sans">

<div class="flex min-h-screen">

    {{-- SIDEBAR --}}
    <aside class="w-64 bg-indigo-700 text-white flex flex-col">
        <div class="p-6 text-xl font-bold border-b border-indigo-600">
            RFID System
        </div>

        <nav class="flex-1 p-4 space-y-2">
            <a href="{{ route('admin.dashboard') }}"
               class="block px-4 py-2 rounded hover:bg-indigo-600">
                Dashboard
            </a>

            <a href="{{ route('admin.students') }}"
               class="block px-4 py-2 rounded hover:bg-indigo-600">
                Students
            </a>

            <a href="{{ route('admin.attendance') }}"
               class="block px-4 py-2 rounded hover:bg-indigo-600">
                Attendance
            </a>
        </nav>

        <form method="POST" action="{{ route('logout') }}" class="p-4 border-t border-indigo-600">
            @csrf
            <button class="w-full bg-red-500 hover:bg-red-600 py-2 rounded">
                Logout
            </button>
        </form>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="flex-1 p-8">
        <h1 class="text-2xl font-bold mb-6">@yield('title')</h1>
        @yield('content')
    </main>

</div>

{{-- SCRIPTS GO HERE --}}
@stack('scripts')

</body>
</html>
