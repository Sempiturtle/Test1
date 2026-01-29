@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
    
    * {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    .metric-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--gradient);
    }

    .metric-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .chart-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        padding: 24px;
    }

    .pulse-dot {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .trend-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .quick-action-btn {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        padding: 16px;
        transition: all 0.2s ease;
        cursor: pointer;
        text-decoration: none;
        display: block;
    }

    .quick-action-btn:hover {
        border-color: #6366f1;
        background: #f5f3ff;
        transform: translateY(-2px);
    }
</style>

<div class="space-y-6">

    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-2xl p-8">
        <div class="flex items-center justify-between flex-wrap gap-4 text-white">
            <div>
                <h1 class="text-3xl font-bold mb-2">Dashboard Overview</h1>
                <p class="text-white/80 text-lg">Student attendance and counseling activity summary</p>
            </div>
            <div class="flex items-center gap-3 bg-white/20 backdrop-blur-md rounded-xl px-5 py-3">
                <i class="fa-solid fa-calendar-day text-xl"></i>
                <div>
                    <p class="text-sm font-medium opacity-90">Today</p>
                    <p class="font-bold text-lg">{{ now()->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Metrics Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Attendance Today -->
        <div class="metric-card p-6" style="--gradient: linear-gradient(90deg, #10b981, #059669);">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-gray-600 text-sm font-semibold uppercase tracking-wide">Today's Attendance</p>
                    <h2 class="text-5xl font-black text-green-600 mt-2">{{ $attendanceToday }}</h2>
                    <p class="text-sm text-green-600/70 font-medium mt-1">{{ $attendanceRate }}% attendance rate</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                    <i class="fa-solid fa-calendar-check text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="text-xs text-gray-500">{{ $presentToday }} present • {{ $absentToday }} absent</div>
        </div>

        <!-- Active Now -->
        <div class="metric-card p-6" style="--gradient: linear-gradient(90deg, #6366f1, #8b5cf6);">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-gray-600 text-sm font-semibold uppercase tracking-wide">Currently Active</p>
                    <h2 class="text-5xl font-black text-indigo-600 mt-2">{{ $activeStudentsNow }}</h2>
                    <p class="text-sm text-indigo-600/70 font-medium mt-1">Students in session</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center relative">
                    <i class="fa-solid fa-users text-indigo-600 text-xl"></i>
                    <div class="pulse-dot absolute -top-1 -right-1 w-3 h-3 bg-green-500 rounded-full"></div>
                </div>
            </div>
            <div class="text-xs text-gray-500">Not yet checked out</div>
        </div>

        <!-- Weekly Trend -->
        <div class="metric-card p-6" style="--gradient: linear-gradient(90deg, #0ea5e9, #06b6d4);">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-gray-600 text-sm font-semibold uppercase tracking-wide">This Week</p>
                    <h2 class="text-5xl font-black text-cyan-600 mt-2">{{ $attendanceThisWeek }}</h2>
                    <div class="mt-1">
                        @if($weeklyTrend === 'up')
                            <span class="trend-badge bg-green-100 text-green-700">
                                <i class="fa-solid fa-arrow-up"></i> {{ abs($weeklyChange) }}%
                            </span>
                        @elseif($weeklyTrend === 'down')
                            <span class="trend-badge bg-red-100 text-red-700">
                                <i class="fa-solid fa-arrow-down"></i> {{ abs($weeklyChange) }}%
                            </span>
                        @else
                            <span class="trend-badge bg-gray-100 text-gray-700">
                                <i class="fa-solid fa-minus"></i> No change
                            </span>
                        @endif
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-cyan-100 flex items-center justify-center">
                    <i class="fa-solid fa-chart-line text-cyan-600 text-xl"></i>
                </div>
            </div>
            <div class="text-xs text-gray-500">vs {{ $attendanceLastWeek }} last week</div>
        </div>

        <!-- Total Students -->
        <div class="metric-card p-6" style="--gradient: linear-gradient(90deg, #f59e0b, #d97706);">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-gray-600 text-sm font-semibold uppercase tracking-wide">Total Students</p>
                    <h2 class="text-5xl font-black text-amber-600 mt-2">{{ $totalStudents }}</h2>
                    <p class="text-sm text-amber-600/70 font-medium mt-1">Enrolled in system</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                    <i class="fa-solid fa-user-graduate text-amber-600 text-xl"></i>
                </div>
            </div>
            <div class="text-xs text-gray-500">+{{ $newStudentsThisMonth }} this month</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="chart-card">
        <div class="flex items-center gap-2 mb-4">
            <i class="fa-solid fa-bolt text-indigo-600 text-lg"></i>
            <h3 class="text-lg font-bold text-gray-900">Quick Actions</h3>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('admin.students.index') }}" class="quick-action-btn">
                <div class="flex flex-col items-center gap-2 text-center">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center">
                        <i class="fa-solid fa-user-plus text-indigo-600 text-xl"></i>
                    </div>
                    <p class="font-semibold text-sm text-gray-900">Add Student</p>
                </div>
            </a>
            <a href="{{ route('admin.attendance.logs') }}" class="quick-action-btn">
                <div class="flex flex-col items-center gap-2 text-center">
                    <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                        <i class="fa-solid fa-clipboard-list text-green-600 text-xl"></i>
                    </div>
                    <p class="font-semibold text-sm text-gray-900">View Logs</p>
                </div>
            </a>
            <a href="{{ route('admin.analytics.index') }}" class="quick-action-btn">
                <div class="flex flex-col items-center gap-2 text-center">
                    <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center">
                        <i class="fa-solid fa-chart-bar text-purple-600 text-xl"></i>
                    </div>
                    <p class="font-semibold text-sm text-gray-900">Analytics</p>
                </div>
            </a>
            <a href="#" class="quick-action-btn" onclick="alert('Export feature coming soon!'); return false;">
                <div class="flex flex-col items-center gap-2 text-center">
                    <div class="w-12 h-12 rounded-xl bg-cyan-100 flex items-center justify-center">
                        <i class="fa-solid fa-file-export text-cyan-600 text-xl"></i>
                    </div>
                    <p class="font-semibold text-sm text-gray-900">Export Report</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Students Needing Attention -->
    @if($studentsNeedingAttention->count() > 0)
    <div class="chart-card border-l-4 border-red-500">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-exclamation-triangle text-red-600 text-lg"></i>
                <h3 class="text-lg font-bold text-gray-900">Students Needing Attention</h3>
            </div>
            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-semibold">
                {{ $studentsNeedingAttention->count() }} Students
            </span>
        </div>
        <div class="space-y-3">
            @foreach($studentsNeedingAttention as $student)
                <div class="flex items-center gap-4 p-3 bg-red-50 rounded-xl border border-red-100">
                    <div class="w-10 h-10 rounded-full bg-red-200 flex items-center justify-center text-red-700 font-bold flex-shrink-0">
                        {{ substr($student['name'], 0, 1) }}
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900">{{ $student['name'] }}</p>
                        <p class="text-sm text-gray-600">{{ $student['course'] }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-red-600">{{ $student['last_attendance'] }}</p>
                        <p class="text-xs text-gray-500">{{ $student['days_ago'] }} days ago</p>
                    </div>
                    <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold transition" onclick="alert('Follow-up feature coming soon for: {{ $student['name'] }}')">
                        Follow-up
                    </button>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Weekly Attendance Trend -->
        <div class="chart-card">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Weekly Attendance</h3>
                    <p class="text-sm text-gray-500">Last 7 days overview</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                    <i class="fa-solid fa-chart-line text-indigo-600"></i>
                </div>
            </div>
            <div id="weeklyChart" style="height: 280px;"></div>
        </div>

        <!-- Today's Status -->
        <div class="chart-card">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Today's Status</h3>
                    <p class="text-sm text-gray-500">Present vs Absent</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                    <i class="fa-solid fa-chart-pie text-green-600"></i>
                </div>
            </div>
            <div id="statusChart" style="height: 280px;"></div>
        </div>

    </div>

    <!-- Most Active Students & Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Most Active This Week -->
        <div class="chart-card">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Most Active This Week</h3>
                    <p class="text-sm text-gray-500">Top 5 students</p>
                </div>
                <i class="fa-solid fa-star text-yellow-500 text-xl"></i>
            </div>
            <div class="space-y-3">
                @foreach($mostActiveThisWeek as $index => $record)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold text-sm">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">{{ $record->student->name }}</p>
                            <p class="text-xs text-gray-500">{{ $record->student->course }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-indigo-600">{{ $record->attendance_count }}</p>
                            <p class="text-xs text-gray-500">sessions</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="chart-card">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Recent Activity</h3>
                    <p class="text-sm text-gray-500">Latest check-ins</p>
                </div>
                <i class="fa-solid fa-clock-rotate-left text-gray-400 text-xl"></i>
            </div>
            <div class="space-y-2 max-h-80 overflow-y-auto">
                @foreach($recentAttendances->take(8) as $attendance)
                    <div class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg transition">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center text-white font-bold text-xs">
                            {{ substr($attendance->student->name, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-sm text-gray-900">{{ $attendance->student->name }}</p>
                            <p class="text-xs text-gray-500">{{ $attendance->student->course }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-medium text-gray-900">{{ $attendance->time_in->format('g:i A') }}</p>
                            @if($attendance->time_out)
                                <span class="text-xs px-2 py-0.5 bg-green-100 text-green-700 rounded-full">Completed</span>
                            @else
                                <span class="text-xs px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full">In Session</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- Peak Hour Info -->
    <div class="chart-card bg-gradient-to-br from-indigo-50 to-purple-50 border-2 border-indigo-100">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-indigo-600 flex items-center justify-center">
                    <i class="fa-solid fa-clock text-white text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600 font-medium">Peak Hour Today</p>
                    <p class="text-3xl font-black text-indigo-600">{{ $peakHour }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-4xl font-black text-indigo-600">{{ $peakHourCount }}</p>
                <p class="text-sm text-gray-600">students checked in</p>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
// Weekly Attendance Chart
new ApexCharts(document.querySelector("#weeklyChart"), {
    chart: { type: 'area', height: 280, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
    series: [{
        name: 'Students',
        data: {!! json_encode($attendancePerDay->pluck('total')) !!}
    }],
    xaxis: {
        categories: {!! json_encode($attendancePerDay->pluck('date')) !!}
    },
    colors: ['#6366f1'],
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.7,
            opacityTo: 0.2
        }
    },
    stroke: { curve: 'smooth', width: 3 },
    dataLabels: { enabled: false },
    grid: { borderColor: '#f3f4f6' }
}).render();

// Status Donut Chart
new ApexCharts(document.querySelector("#statusChart"), {
    chart: { type: 'donut', height: 280, fontFamily: 'Inter, sans-serif' },
    series: [{{ $presentToday }}, {{ $absentToday }}],
    labels: ['Present', 'Absent'],
    colors: ['#10b981', '#ef4444'],
    legend: { position: 'bottom', fontSize: '14px' },
    plotOptions: {
        pie: {
            donut: {
                size: '70%',
                labels: {
                    show: true,
                    total: {
                        show: true,
                        label: 'Total',
                        fontSize: '14px',
                        formatter: function(w) {
                            return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                        }
                    }
                }
            }
        }
    }
}).render();
</script>
@endpush
@endsection