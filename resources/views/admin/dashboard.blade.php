@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<div class="space-y-10">

    <!-- METRIC CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Students -->
        <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition p-6 flex items-center gap-4 border-l-4 border-sky-500">
            <div class="bg-sky-100 p-3 rounded-full text-sky-600">
                <i class="fa-solid fa-user-graduate text-xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Total Students</p>
                <h2 class="text-3xl font-bold text-sky-600 mt-1">{{ $totalStudents }}</h2>
            </div>
        </div>

        <!-- Attendance Today -->
        <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition p-6 flex items-center gap-4 border-l-4 border-green-500">
            <div class="bg-green-100 p-3 rounded-full text-green-600">
                <i class="fa-solid fa-calendar-check text-xl"></i>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Attendance Today</p>
                <h2 class="text-3xl font-bold text-green-600 mt-1">{{ $attendanceToday }}</h2>
            </div>
        </div>

        <!-- System Status -->
        <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition p-6 flex items-center gap-4 border-l-4 border-indigo-500">
            <div class="bg-indigo-100 p-3 rounded-full text-indigo-600">
                <i class="fa-solid fa-circle-check text-xl"></i>
            </div>
            <div class="flex justify-between w-full items-center">
                <div>
                    <p class="text-gray-500 text-sm">System Status</p>
                    <h2 class="text-lg font-bold text-green-600">● ACTIVE</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- CHARTS GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <!-- Attendance Line Chart -->
        <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-700 font-semibold">Attendance (Last 7 Days)</h3>
                <i class="fa-solid fa-chart-line text-gray-400"></i>
            </div>
            <div id="attendanceLineChart" class="h-64"></div>
        </div>

        <!-- Today's Attendance Donut Chart -->
        <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-700 font-semibold">Today's Attendance</h3>
                <i class="fa-solid fa-chart-pie text-gray-400"></i>
            </div>
            <div id="attendanceDonutChart" class="h-64"></div>
        </div>

        <!-- Course Participation Radial Bar Chart -->
        <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition p-6 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-gray-700 font-semibold">Course Participation</h3>
                <i class="fa-solid fa-chart-pie text-gray-400"></i>
            </div>
            <div id="courseRadialChart" class="h-80"></div>
        </div>
    </div>

    <!-- RECENT ATTENDANCE TABLE -->
    <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition p-6 overflow-x-auto">
        <h3 class="text-gray-700 font-semibold mb-4">Recent Attendance Logs</h3>
        <table class="w-full text-sm text-gray-800 rounded-lg">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="p-3 text-left">Student</th>
                    <th class="p-3 text-left">RFID</th>
                    <th class="p-3 text-left">Time In</th>
                    <th class="p-3 text-left">Time Out</th>
                    <th class="p-3 text-left">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($recentAttendances as $log)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="p-3">{{ $log->student->name }}</td>
                    <td class="p-3 font-mono">{{ $log->rfid_uid }}</td>
                    <td class="p-3">{{ \Carbon\Carbon::parse($log->time_in)->format('h:i A') }}</td>
                    <td class="p-3">{{ $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('h:i A') : '—' }}</td>
                    <td class="p-3">
                        @if($log->time_out)
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Completed</span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">Pending</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Attendance Line Chart
    new ApexCharts(document.querySelector("#attendanceLineChart"), {
        chart: { type: 'line', height: 300, toolbar: { show: false }, zoom: { enabled: false } },
        series: [{ name: 'Attendance', data: {!! json_encode($attendancePerDay->pluck('total')) !!} }],
        xaxis: { categories: {!! json_encode($attendancePerDay->pluck('date')) !!} },
        stroke: { curve: 'smooth', width: 3 },
        markers: { size: 4, colors: ['#0ea5e9'], strokeWidth: 2, strokeColors: '#fff' },
        colors: ['#0ea5e9']
    }).render();

    // Today's Attendance Donut Chart
    new ApexCharts(document.querySelector("#attendanceDonutChart"), {
        chart: { type: 'donut', height: 300 },
        series: [{{ $presentToday }}, {{ $absentToday }}],
        labels: ['Present', 'Absent'],
        colors: ['#22c55e', '#ef4444'],
        legend: { position: 'bottom' },
        dataLabels: { style: { colors: ['#000'] } }
    }).render();

    // Course Participation Radial Chart
    var courseOptions = {
        series: [
            {{ $courseParticipation['BT'] ?? 0 }},
            {{ $courseParticipation['BO'] ?? 0 }},
            {{ $courseParticipation['BSCS'] ?? 0 }},
            {{ $courseParticipation['BCrim'] ?? 0 }},
            {{ $courseParticipation['BAcct'] ?? 0 }}
        ],
        chart: { height: 390, type: 'radialBar' },
        plotOptions: {
            radialBar: {
                offsetY: 0,
                startAngle: 0,
                endAngle: 270,
                hollow: { size: '30%', background: 'transparent' },
                dataLabels: {
                    name: { show: false },
                    value: { show: true, fontSize: '16px', color: '#111' }
                },
                barLabels: {
                    enabled: true,
                    useSeriesColors: true,
                    offsetX: -8,
                    fontSize: '14px',
                    formatter: function(seriesName, opts) {
                        return opts.w.config.labels[opts.seriesIndex] + ": " + opts.w.globals.series[opts.seriesIndex];
                    }
                },
            }
        },
        colors: ['#f59e0b', '#0ea5e9', '#6366f1', '#ef4444', '#22c55e'],
        labels: ['Tourism', 'Office', 'BSCS', 'Crim', 'Accounting'],
        responsive: [{
            breakpoint: 480,
            options: { chart: { height: 300 }, legend: { show: false } }
        }]
    };

    new ApexCharts(document.querySelector("#courseRadialChart"), courseOptions).render();
</script>
@endpush
