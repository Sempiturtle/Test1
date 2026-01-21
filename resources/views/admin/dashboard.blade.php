@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white shadow rounded-2xl p-6 border-l-4 border-sky-500">
            <p class="text-sm text-gray-500">Total Students</p>
            <h2 class="text-4xl font-bold text-sky-600 mt-2">{{ $totalStudents }}</h2>
        </div>

        <div class="bg-white shadow rounded-2xl p-6 border-l-4 border-green-500">
            <p class="text-sm text-gray-500">Attendance Today</p>
            <h2 class="text-4xl font-bold text-green-600 mt-2">{{ $attendanceToday }}</h2>
        </div>

        <div class="bg-white shadow rounded-2xl p-6 border-l-4 border-indigo-500 flex justify-between items-center">
            <p class="text-sm text-gray-500">System Status</p>
            <h2 class="text-xl font-bold text-green-600">● ACTIVE</h2>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
        <div class="bg-white shadow rounded-2xl p-6 border border-gray-200">
            <h3 class="font-semibold text-indigo-700 mb-4">Attendance (Last 7 Days)</h3>
            <div id="attendanceLineChart"></div>
        </div>

        <div class="bg-white shadow rounded-2xl p-6 border border-gray-200">
            <h3 class="font-semibold text-indigo-700 mb-4">Today's Attendance</h3>
            <div id="attendanceDonutChart"></div>
        </div>
    </div>

    <div class="bg-white shadow rounded-2xl p-6 border border-gray-200 overflow-x-auto">
        <h3 class="font-semibold text-indigo-700 mb-4">Recent Attendance Logs</h3>
        <table class="w-full text-sm rounded-lg overflow-hidden">
            <thead class="bg-indigo-100 text-gray-700">
                <tr>
                    <th class="p-3 text-left">Student</th>
                    <th class="p-3 text-left">RFID</th>
                    <th class="p-3 text-left">Time In</th>
                    <th class="p-3 text-left">Time Out</th> <!-- Added -->
                </tr>
            </thead>
            <tbody>
                @foreach ($recentAttendances as $log)
                    <tr class="border-b hover:bg-indigo-50">
                        <td class="p-3">{{ $log->student->name }}</td>
                        <td class="p-3 font-mono">{{ $log->rfid_uid }}</td>
                        <td class="p-3">{{ \Carbon\Carbon::parse($log->time_in)->format('h:i A') }}</td>
                        <td class="p-3">
                            {{ $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('h:i A') : '—' }}</td>
                        <!-- Added -->
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection

@push('scripts')
    <script>
        new ApexCharts(document.querySelector("#attendanceLineChart"), {
            chart: {
                type: 'line',
                height: 300,
                toolbar: {
                    show: false
                }
            },
            series: [{
                name: 'Attendance',
                data: {!! json_encode($attendancePerDay->pluck('total')) !!}
            }],
            xaxis: {
                categories: {!! json_encode($attendancePerDay->pluck('date')) !!}
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            colors: ['#3B82F6']
        }).render();

        new ApexCharts(document.querySelector("#attendanceDonutChart"), {
            chart: {
                type: 'donut',
                height: 300
            },
            series: [{{ $presentToday }}, {{ $absentToday }}],
            labels: ['Present', 'Absent'],
            colors: ['#22C55E', '#EF4444']
        }).render();
    </script>
@endpush
