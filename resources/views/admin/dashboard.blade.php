@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
    
    :root {
        --primary: #6366f1;
        --primary-dark: #4f46e5;
        --success: #10b981;
        --warning: #f59e0b;
        --error: #ef4444;
        --info: #0ea5e9;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-900: #111827;
    }

    .dashboard-container {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    .metric-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
        background: linear-gradient(90deg, var(--card-color-start), var(--card-color-end));
    }

    .metric-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .metric-icon-wrapper {
        position: relative;
    }

    .metric-icon-wrapper::before {
        content: '';
        position: absolute;
        inset: -8px;
        border-radius: 50%;
        background: inherit;
        opacity: 0.1;
        animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse-ring {
        0%, 100% {
            transform: scale(1);
            opacity: 0.1;
        }
        50% {
            transform: scale(1.1);
            opacity: 0.05;
        }
    }

    .chart-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
    }

    .chart-card:hover {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .chart-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--gray-100);
    }

    .chart-icon {
        height: 40px;
        width: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--gray-50), var(--gray-100));
    }

    .status-pulse {
        animation: status-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes status-pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.7;
        }
    }

    .table-wrapper {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        overflow: hidden;
    }

    tbody tr {
        transition: all 0.15s ease;
    }

    tbody tr:hover {
        background-color: var(--gray-50);
        transform: translateX(4px);
    }

    .status-badge {
        font-weight: 600;
        letter-spacing: 0.025em;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .gradient-text {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .stats-number {
        font-variant-numeric: tabular-nums;
        line-height: 1;
    }

    /* Card-specific gradients */
    .card-sky {
        --card-color-start: #0ea5e9;
        --card-color-end: #06b6d4;
    }

    .card-green {
        --card-color-start: #10b981;
        --card-color-end: #059669;
    }

    .card-indigo {
        --card-color-start: #6366f1;
        --card-color-end: #8b5cf6;
    }

    .welcome-banner {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
        overflow: hidden;
    }

    .welcome-banner::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        opacity: 0.5;
    }

    .welcome-content {
        position: relative;
        z-index: 1;
    }
</style>

<div class="dashboard-container space-y-8">

    <!-- WELCOME BANNER -->
    <div class="welcome-banner rounded-2xl shadow-2xl p-8">
        <div class="welcome-content flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Welcome Back, Admin! 👋</h1>
                <p class="text-white/80 text-lg">Here's what's happening with your attendance system today</p>
            </div>
            <div class="flex items-center gap-3 bg-white/20 backdrop-blur-md rounded-xl px-5 py-3 border border-white/30">
                <i class="fa-solid fa-calendar-day text-white text-xl"></i>
                <div class="text-white">
                    <p class="text-sm font-medium opacity-90">Today</p>
                    <p class="font-bold text-lg">{{ now()->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- METRIC CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Total Students -->
        <div class="metric-card card-sky p-8">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-gray-600 text-sm font-semibold uppercase tracking-wide mb-2">Total Students</p>
                    <h2 class="stats-number text-5xl font-black text-sky-600 mb-1">{{ $totalStudents }}</h2>
                    <p class="text-sky-600/70 text-sm font-medium">Enrolled in system</p>
                </div>
                <div class="metric-icon-wrapper bg-sky-100 p-4 rounded-2xl text-sky-600">
                    <i class="fa-solid fa-user-graduate text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Attendance Today -->
        <div class="metric-card card-green p-8">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-gray-600 text-sm font-semibold uppercase tracking-wide mb-2">Attendance Today</p>
                    <h2 class="stats-number text-5xl font-black text-green-600 mb-1">{{ $attendanceToday }}</h2>
                    <p class="text-green-600/70 text-sm font-medium">Students checked in</p>
                </div>
                <div class="metric-icon-wrapper bg-green-100 p-4 rounded-2xl text-green-600">
                    <i class="fa-solid fa-calendar-check text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="metric-card card-indigo p-8">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-gray-600 text-sm font-semibold uppercase tracking-wide mb-2">System Status</p>
                    <div class="flex items-center gap-3 mt-3">
                        <div class="status-pulse h-4 w-4 rounded-full bg-green-500 shadow-lg shadow-green-500/50"></div>
                        <h2 class="text-2xl font-black text-green-600">ACTIVE</h2>
                    </div>
                    <p class="text-indigo-600/70 text-sm font-medium mt-2">All systems operational</p>
                </div>
                <div class="metric-icon-wrapper bg-indigo-100 p-4 rounded-2xl text-indigo-600">
                    <i class="fa-solid fa-circle-check text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- CHARTS GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Attendance Line Chart -->
        <div class="chart-card p-8">
            <div class="chart-header mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 mb-1">Weekly Attendance</h3>
                    <p class="text-gray-500 text-sm">Last 7 days overview</p>
                </div>
                <div class="chart-icon">
                    <i class="fa-solid fa-chart-line text-indigo-600 text-lg"></i>
                </div>
            </div>
            <div id="attendanceLineChart" class="h-64"></div>
        </div>

        <!-- Today's Attendance Donut Chart -->
        <div class="chart-card p-8">
            <div class="chart-header mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 mb-1">Today's Status</h3>
                    <p class="text-gray-500 text-sm">Present vs Absent breakdown</p>
                </div>
                <div class="chart-icon">
                    <i class="fa-solid fa-chart-pie text-green-600 text-lg"></i>
                </div>
            </div>
            <div id="attendanceDonutChart" class="h-64"></div>
        </div>

        <!-- Course Participation Radial Bar Chart -->
        <div class="chart-card p-8 lg:col-span-2">
            <div class="chart-header mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 mb-1">Course Participation</h3>
                    <p class="text-gray-500 text-sm">Student distribution across programs</p>
                </div>
                <div class="chart-icon">
                    <i class="fa-solid fa-graduation-cap text-purple-600 text-lg"></i>
                </div>
            </div>
            <div id="courseRadialChart" class="h-80"></div>
        </div>
    </div>

    <!-- RECENT ATTENDANCE TABLE -->
    <div class="table-wrapper">
        <div class="px-8 py-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 mb-1">Recent Activity</h3>
                    <p class="text-gray-600 text-sm">Latest attendance logs from students</p>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <i class="fa-solid fa-clock"></i>
                    <span>Real-time updates</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b-2 border-gray-200">
                    <tr>
                        <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Student</th>
                        <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">RFID Number</th>
                        <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Time In</th>
                        <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Time Out</th>
                        <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach ($recentAttendances as $log)
                    <tr class="group">
                        <td class="px-8 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold text-sm shadow-md">
                                    {{ strtoupper(substr($log->student->name, 0, 1)) }}
                                </div>
                                <span class="font-semibold text-gray-900">{{ $log->student->name }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-4">
                            <code class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-mono font-medium">
                                {{ $log->rfid_uid }}
                            </code>
                        </td>
                        <td class="px-8 py-4">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-clock text-gray-400 text-sm"></i>
                                <span class="text-gray-700 font-medium text-sm">
                                    {{ \Carbon\Carbon::parse($log->time_in)->format('h:i A') }}
                                </span>
                            </div>
                        </td>
                        <td class="px-8 py-4">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-clock text-gray-400 text-sm"></i>
                                <span class="text-gray-700 font-medium text-sm">
                                    {{ $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('h:i A') : '—' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-8 py-4">
                            @if($log->time_out)
                                <span class="status-badge inline-flex items-center px-3.5 py-1.5 rounded-full bg-green-100 text-green-700 text-xs">
                                    <i class="fa-solid fa-check-circle"></i>
                                    Completed
                                </span>
                            @else
                                <span class="status-badge inline-flex items-center px-3.5 py-1.5 rounded-full bg-amber-100 text-amber-700 text-xs">
                                    <i class="fa-solid fa-clock"></i>
                                    In Session
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Attendance Line Chart
    new ApexCharts(document.querySelector("#attendanceLineChart"), {
        chart: { 
            type: 'line', 
            height: 280, 
            toolbar: { show: false }, 
            zoom: { enabled: false },
            fontFamily: 'Inter, sans-serif',
            dropShadow: {
                enabled: true,
                top: 3,
                left: 0,
                blur: 4,
                opacity: 0.1
            }
        },
        series: [{ 
            name: 'Students', 
            data: {!! json_encode($attendancePerDay->pluck('total')) !!} 
        }],
        xaxis: { 
            categories: {!! json_encode($attendancePerDay->pluck('date')) !!},
            labels: {
                style: {
                    colors: '#6b7280',
                    fontSize: '12px',
                    fontWeight: 500
                }
            },
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            }
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#6b7280',
                    fontSize: '12px',
                    fontWeight: 500
                },
                formatter: function(val) {
                    return Math.floor(val);
                }
            }
        },
        stroke: { 
            curve: 'smooth', 
            width: 4
        },
        markers: { 
            size: 6, 
            colors: ['#6366f1'], 
            strokeWidth: 3, 
            strokeColors: '#fff',
            hover: {
                size: 8
            }
        },
        colors: ['#6366f1'],
        grid: {
            borderColor: '#f3f4f6',
            strokeDashArray: 3,
            xaxis: {
                lines: {
                    show: false
                }
            },
            yaxis: {
                lines: {
                    show: true
                }
            }
        },
        tooltip: {
            theme: 'light',
            x: {
                show: true
            },
            y: {
                formatter: function(val) {
                    return val + ' students';
                }
            },
            style: {
                fontSize: '12px',
                fontFamily: 'Inter, sans-serif'
            }
        },
        dataLabels: {
            enabled: false
        }
    }).render();

    // Today's Attendance Donut Chart
    new ApexCharts(document.querySelector("#attendanceDonutChart"), {
        chart: { 
            type: 'donut', 
            height: 280,
            fontFamily: 'Inter, sans-serif'
        },
        series: [{{ $presentToday }}, {{ $absentToday }}],
        labels: ['Present', 'Absent'],
        colors: ['#10b981', '#ef4444'],
        legend: { 
            position: 'bottom',
            fontSize: '14px',
            fontWeight: 600,
            labels: {
                colors: '#374151'
            },
            markers: {
                width: 12,
                height: 12,
                radius: 4
            }
        },
        dataLabels: { 
            style: { 
                colors: ['#fff'],
                fontSize: '16px',
                fontWeight: 700,
                fontFamily: 'Inter, sans-serif'
            },
            dropShadow: {
                enabled: false
            }
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        name: {
                            show: true,
                            fontSize: '14px',
                            fontWeight: 600,
                            color: '#6b7280'
                        },
                        value: {
                            show: true,
                            fontSize: '32px',
                            fontWeight: 800,
                            color: '#111827',
                            formatter: function(val) {
                                return val;
                            }
                        },
                        total: {
                            show: true,
                            label: 'Total',
                            fontSize: '14px',
                            fontWeight: 600,
                            color: '#6b7280',
                            formatter: function(w) {
                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                            }
                        }
                    }
                }
            }
        },
        stroke: {
            width: 0
        },
        tooltip: {
            theme: 'light',
            y: {
                formatter: function(val) {
                    return val + ' students';
                }
            },
            style: {
                fontSize: '12px',
                fontFamily: 'Inter, sans-serif'
            }
        }
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
        chart: { 
            height: 380, 
            type: 'radialBar',
            fontFamily: 'Inter, sans-serif'
        },
        plotOptions: {
            radialBar: {
                offsetY: 0,
                startAngle: 0,
                endAngle: 270,
                hollow: { 
                    size: '35%', 
                    background: 'transparent' 
                },
                dataLabels: {
                    name: { 
                        show: false 
                    },
                    value: { 
                        show: true, 
                        fontSize: '18px', 
                        fontWeight: 700,
                        color: '#111827'
                    }
                },
                barLabels: {
                    enabled: true,
                    useSeriesColors: true,
                    offsetX: -8,
                    fontSize: '15px',
                    fontWeight: 600,
                    formatter: function(seriesName, opts) {
                        return opts.w.config.labels[opts.seriesIndex] + ": " + opts.w.globals.series[opts.seriesIndex];
                    }
                },
                track: {
                    background: '#f3f4f6',
                    strokeWidth: '100%',
                    margin: 8
                }
            }
        },
        colors: ['#8b5cf6', '#0ea5e9', '#6366f1', '#ef4444', '#10b981'],
        labels: ['Tourism', 'Office', 'Computer Science', 'Criminology', 'Accountancy'],
        legend: {
            show: true,
            floating: false,
            fontSize: '14px',
            fontWeight: 600,
            position: 'bottom',
            offsetY: 10,
            labels: {
                colors: '#374151',
                useSeriesColors: false
            },
            markers: {
                width: 12,
                height: 12,
                radius: 4
            },
            itemMargin: {
                horizontal: 10,
                vertical: 5
            }
        },
        responsive: [{
            breakpoint: 480,
            options: { 
                chart: { height: 320 },
                legend: { 
                    show: true,
                    position: 'bottom'
                }
            }
        }]
    };

    new ApexCharts(document.querySelector("#courseRadialChart"), courseOptions).render();
</script>
@endpush