<x-app-layout>
    <div class="p-6">

        <h1 class="text-2xl font-bold mb-4">
            Admin Dashboard – RFID Attendance
        </h1>

        @if (session('success'))
            <p class="text-green-600">{{ session('success') }}</p>
        @endif

        @if (session('error'))
            <p class="text-red-600">{{ session('error') }}</p>
        @endif

        <div class="grid grid-cols-2 gap-4 my-6">
            <div class="p-4 bg-white shadow rounded">
                <p>Total Students</p>
                <h2 class="text-3xl">{{ $totalStudents }}</h2>
            </div>

            <div class="p-4 bg-white shadow rounded">
                <p>Attendance Today</p>
                <h2 class="text-3xl">{{ $attendanceToday }}</h2>
            </div>
        </div>

        <form method="POST" action="{{ route('simulate.rfid') }}" class="mb-6">
            @csrf
            <input type="text" name="rfid_uid" placeholder="Scan RFID UID" class="border p-2" required>
            <button class="bg-blue-600 text-white px-4 py-2">
                Simulate RFID Tap
            </button>
        </form>

        <table class="w-full border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">Student Name</th>
                    <th class="border p-2">RFID UID</th>
                    <th class="border p-2">Session</th>
                    <th class="border p-2">Time In</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendanceLogs as $log)
                    <tr>
                        <td class="border p-2">{{ $log->student->name }}</td>
                        <td class="border p-2">{{ $log->rfid_uid }}</td>
                        <td class="border p-2">{{ $log->session }}</td>
                        <td class="border p-2">{{ $log->time_in }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</x-app-layout>
