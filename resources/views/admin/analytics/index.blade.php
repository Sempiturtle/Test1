@extends('layouts.admin')
@section('title', 'Analytics & Reports')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
    
    .analytics-container {
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
        background: var(--card-gradient);
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

    .engagement-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-high { background: #d1fae5; color: #065f46; }
    .badge-medium { background: #fef3c7; color: #92400e; }
    .badge-low { background: #fee2e2; color: #991b1b; }
    .badge-critical { background: #fee2e2; color: #7f1d1d; }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 800;
        line-height: 1;
        background: var(--card-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
</style>

<div class="analytics-container space-y-6">

    <!-- Page Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-2xl p-8">
        <div class="flex items-center justify-between text-white flex-wrap gap-4">
            <div>
                <h1 class="text-3xl font-bold mb-2">Analytics & Reports</h1>
                <p class="text-white/80 text-lg">Comprehensive participation and engagement insights</p>
            </div>
            <div class="flex gap-3">
                <button onclick="exportData('pdf')" class="px-5 py-2.5 bg-white/20 backdrop-blur-md rounded-xl hover:bg-white/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-file-pdf"></i>
                    Export PDF
                </button>
                <button onclick="exportData('excel')" class="px-5 py-2.5 bg-white/20 backdrop-blur-md rounded-xl hover:bg-white/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-file-excel"></i>
                    Export Excel
                </button>
            </div>
        </div>
    </div>

    <!-- Overview Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="metric-card p-6" style="--card-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-gray-600 text-sm font-semibold uppercase tracking-wide">Total Students</p>
                    <p class="stat-number mt-2">{{ $totalStudents }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center">
                    <i class="fa-solid fa-user-graduate text-indigo-600 text-xl"></i>
                </div>
            </div>
            <p class="text-sm text-gray-500">Enrolled in system</p>
        </div>

        <div class="metric-card p-6" style="--card-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-gray-600 text-sm font-semibold uppercase tracking-wide">Total Attendance</p>
                    <p class="stat-number mt-2">{{ $totalAttendances }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
                    <i class="fa-solid fa-check-double text-green-600 text-xl"></i>
                </div>
            </div>
            <p class="text-sm text-gray-500">Recorded check-ins</p>
        </div>

        <div class="metric-card p-6" style="--card-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-gray-600 text-sm font-semibold uppercase tracking-wide">Unique Attendees</p>
                    <p class="stat-number mt-2">{{ $uniqueAttendees }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-cyan-100 flex items-center justify-center">
                    <i class="fa-solid fa-users text-cyan-600 text-xl"></i>
                </div>
            </div>
            <p class="text-sm text-gray-500">Different students</p>
        </div>

        <div class="metric-card p-6" style="--card-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-gray-600 text-sm font-semibold uppercase tracking-wide">Attendance Rate</p>
                    <p class="stat-number mt-2">{{ $averageAttendanceRate }}%</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-pink-100 flex items-center justify-center">
                    <i class="fa-solid fa-chart-pie text-pink-600 text-xl"></i>
                </div>
            </div>
            <p class="text-sm text-gray-500">Overall participation</p>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Attendance Trends -->
        <div class="chart-card">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Attendance Trends</h3>
                    <p class="text-sm text-gray-500">Last 30 days overview</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                    <i class="fa-solid fa-chart-line text-indigo-600"></i>
                </div>
            </div>
            <div id="trendsChart" style="height: 300px;"></div>
        </div>

        <!-- Peak Times -->
        <div class="chart-card">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Peak Attendance Times</h3>
                    <p class="text-sm text-gray-500">By hour of day</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center">
                    <i class="fa-solid fa-clock text-purple-600"></i>
                </div>
            </div>
            <div id="peakTimesChart" style="height: 300px;"></div>
        </div>

    </div>

    <!-- Charts Row 2 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Course Participation -->
        <div class="chart-card">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Course Participation</h3>
                    <p class="text-sm text-gray-500">By program</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                    <i class="fa-solid fa-graduation-cap text-green-600"></i>
                </div>
            </div>
            <div id="courseChart" style="height: 300px;"></div>
        </div>

        <!-- Engagement Distribution -->
        <div class="chart-card">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Student Engagement</h3>
                    <p class="text-sm text-gray-500">Distribution by level</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-cyan-100 flex items-center justify-center">
                    <i class="fa-solid fa-chart-pie text-cyan-600"></i>
                </div>
            </div>
            <div id="engagementChart" style="height: 300px;"></div>
        </div>

    </div>

    <!-- No-Show Analysis -->
    <div class="chart-card">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Activity Analysis</h3>
                <p class="text-sm text-gray-500">Student activity overview</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-center">
                    <p class="text-3xl font-bold text-red-600">{{ $noShowAnalysis['inactive_rate'] }}%</p>
                    <p class="text-xs text-gray-500">Inactive Rate</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
                    <i class="fa-solid fa-user-xmark text-red-600"></i>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-sm text-gray-600 mb-1">Total Students</p>
                <p class="text-2xl font-bold text-gray-900">{{ $noShowAnalysis['total_students'] }}</p>
            </div>
            <div class="bg-green-50 rounded-xl p-4">
                <p class="text-sm text-gray-600 mb-1">Active This Month</p>
                <p class="text-2xl font-bold text-green-600">{{ $noShowAnalysis['active_this_month'] }}</p>
            </div>
            <div class="bg-red-50 rounded-xl p-4">
                <p class="text-sm text-gray-600 mb-1">Inactive</p>
                <p class="text-2xl font-bold text-red-600">{{ $noShowAnalysis['inactive_count'] }}</p>
            </div>
            <div class="bg-orange-50 rounded-xl p-4">
                <p class="text-sm text-gray-600 mb-1">Never Attended</p>
                <p class="text-2xl font-bold text-orange-600">{{ $noShowAnalysis['never_attended'] }}</p>
            </div>
        </div>
    </div>

    <!-- Top Engaged Students -->
    <div class="chart-card">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Top Engaged Students</h3>
                <p class="text-sm text-gray-500">Highest participation rates</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-yellow-100 flex items-center justify-center">
                <i class="fa-solid fa-star text-yellow-600"></i>
            </div>
        </div>

        <div class="space-y-3">
            @foreach($topEngagedStudents as $index => $student)
                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold flex-shrink-0">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900">{{ $student['name'] }}</p>
                        <p class="text-sm text-gray-500">{{ $student['course'] }} • Last: {{ $student['last_attendance'] }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-indigo-600">{{ $student['attendance_count'] }}</p>
                        <p class="text-xs text-gray-500">sessions</p>
                    </div>
                    <div>
                        <span class="engagement-badge badge-{{ $student['engagement_score'] >= 70 ? 'high' : ($student['engagement_score'] >= 40 ? 'medium' : 'low') }}">
                            {{ $student['engagement_score'] }}% Engaged
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- At-Risk Students -->
    <div class="chart-card border-l-4 border-red-500">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-xl font-bold text-gray-900">At-Risk Students</h3>
                <p class="text-sm text-gray-500">Requiring immediate attention</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-semibold">
                    {{ $atRiskStudents->count() }} Students
                </span>
                <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
                    <i class="fa-solid fa-exclamation-triangle text-red-600"></i>
                </div>
            </div>
        </div>

        @if($atRiskStudents->count() > 0)
            <div class="space-y-3">
                @foreach($atRiskStudents->take(10) as $student)
                    <div class="flex items-center gap-4 p-4 bg-red-50 rounded-xl border border-red-100">
                        <div class="w-10 h-10 rounded-full bg-red-200 flex items-center justify-center text-red-700 font-bold text-sm flex-shrink-0">
                            {{ strtoupper(substr($student['name'], 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">{{ $student['name'] }}</p>
                            <p class="text-sm text-gray-600">{{ $student['course'] }} • Last: {{ $student['last_attendance'] }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-red-600">{{ $student['days_since_last_attendance'] ?? 'Never' }}</p>
                            <p class="text-xs text-gray-500">{{ $student['days_since_last_attendance'] ? 'days ago' : '' }}</p>
                        </div>
                        <div>
                            <span class="engagement-badge badge-{{ strtolower($student['risk_level']) }}">
                                <i class="fa-solid fa-exclamation-circle"></i>
                                {{ $student['risk_level'] }} Risk
                            </span>
                        </div>
                        <button onclick="alert('Follow-up feature coming soon for: {{ $student['name'] }}')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold transition">
                            Follow-up
                        </button>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <i class="fa-solid fa-check-circle text-6xl text-green-500 mb-4"></i>
                <p class="text-lg font-semibold text-gray-900">All Students Engaged!</p>
                <p class="text-gray-500">No students require immediate attention</p>
            </div>
        @endif
    </div>

</div>

<script>
// Attendance Trends Chart
new ApexCharts(document.querySelector("#trendsChart"), {
    chart: { type: 'area', height: 300, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
    series: [{
        name: 'Attendance',
        data: {!! json_encode($attendanceTrends->pluck('count')) !!}
    }],
    xaxis: {
        categories: {!! json_encode($attendanceTrends->pluck('formatted_date')) !!}
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
    dataLabels: { enabled: false }
}).render();

// Peak Times Chart
new ApexCharts(document.querySelector("#peakTimesChart"), {
    chart: { type: 'bar', height: 300, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
    series: [{
        name: 'Attendance',
        data: {!! json_encode($peakTimes['by_hour']->pluck('count')) !!}
    }],
    xaxis: {
        categories: {!! json_encode($peakTimes['by_hour']->pluck('hour')) !!}
    },
    colors: ['#8b5cf6'],
    plotOptions: {
        bar: {
            borderRadius: 8,
            distributed: false
        }
    },
    dataLabels: { enabled: false }
}).render();

// Course Participation Chart
new ApexCharts(document.querySelector("#courseChart"), {
    chart: { type: 'bar', height: 300, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
    series: [{
        name: 'Students',
        data: {!! json_encode($courseParticipation->pluck('unique_students')) !!}
    }, {
        name: 'Total Attendance',
        data: {!! json_encode($courseParticipation->pluck('total_attendance')) !!}
    }],
    xaxis: {
        categories: {!! json_encode($courseParticipation->pluck('course')) !!}
    },
    colors: ['#6366f1', '#10b981'],
    plotOptions: {
        bar: {
            borderRadius: 8,
            columnWidth: '60%'
        }
    },
    dataLabels: { enabled: false },
    legend: { position: 'top' }
}).render();

// Engagement Distribution Chart
new ApexCharts(document.querySelector("#engagementChart"), {
    chart: { type: 'donut', height: 300, fontFamily: 'Inter, sans-serif' },
    series: [
        {{ $engagementScores['distribution']['high'] }},
        {{ $engagementScores['distribution']['medium'] }},
        {{ $engagementScores['distribution']['low'] }}
    ],
    labels: ['High Engagement', 'Medium Engagement', 'Low Engagement'],
    colors: ['#10b981', '#f59e0b', '#ef4444'],
    legend: { 
        position: 'bottom',
        fontSize: '14px'
    },
    plotOptions: {
        pie: {
            donut: {
                size: '65%'
            }
        }
    }
}).render();

// Export functions
function exportData(type) {
    window.location.href = `/admin/analytics/export?type=${type}`;
}
</script>
@endsection