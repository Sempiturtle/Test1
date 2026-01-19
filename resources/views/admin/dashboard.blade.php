@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white/5 backdrop-blur p-6 rounded-2xl border border-white/10">
            <p class="text-sm text-gray-400">Total Students</p>
            <h2 class="text-4xl font-bold text-indigo-400 mt-2">{{ $totalStudents }}</h2>
        </div>
        <div class="bg-white/5 backdrop-blur p-6 rounded-2xl border border-white/10">
            <p class="text-sm text-gray-400">Attendance Today</p>
            <h2 class="text-4xl font-bold text-green-400 mt-2">{{ $attendanceToday }}</h2>
        </div>
        <div class="bg-white/5 backdrop-blur p-6 rounded-2xl border border-white/10">
            <p class="text-sm text-gray-400">System Status</p>
            <h2 class="text-xl font-bold text-green-400 mt-4">● ACTIVE</h2>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
        <!-- Line Chart -->
        <div class="bg-white/5 backdrop-blur p-6 rounded-2xl border border-white/10">
            <h3 class="font-semibold mb-4">Attendance (Last 7 Days)</h3>
            <div id="attendanceLineChart"></div>
        </div>

        <!-- Donut Chart -->
        <div class="bg-white/5 backdrop-blur p-6 rounded-2xl border border-white/10">
            <h3 class="font-semibold mb-4">Today's Attendance</h3>
            <div id="attendanceDonutChart"></div>
        </div>
    </div>

    <div class="bg-white/5 backdrop-blur p-6 rounded-2xl border border-white/10">
        <h3 class="font-semibold mb-4">Recent Attendance Logs</h3>
        <table class="w-full text-sm">
            <thead class="text-gray-400 border-b border-white/10">
                <tr>
                    <th class="text-left py-2">Student</th>
                    <th class="text-left py-2">RFID</th>
                    <th class="text-left py-2">Time In</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($recentAttendances as $log)
                    <tr class="border-b border-white/5 hover:bg-gray-800">
                        <td class="py-2">{{ $log->student->name }}</td>
                        <td class="py-2 font-mono">{{ $log->rfid_uid }}</td>
                        <td class="py-2">{{ $log->time_in }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection

@push('scripts')
    <script>
        /* LINE CHART */
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
            colors: ['#6366F1']
        }).render();

        /* DONUT CHART */
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
