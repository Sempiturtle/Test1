@extends('layouts.admin')
@section('title', 'System Overview')

@section('actions')
<div class="flex items-center gap-3">
    <button class="px-5 py-2.5 bg-white hover:bg-slate-50 border border-slate-200 hover:border-indigo-300 rounded-xl text-xs font-bold uppercase tracking-wider text-slate-600 hover:text-indigo-600 transition-all flex items-center gap-2 shadow-sm">
        <i class="fa-solid fa-file-export text-[10px]"></i>
        Export Analytics
    </button>
    <a href="{{ route('admin.attendance.logs') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-xs font-bold uppercase tracking-wider text-white shadow-lg shadow-indigo-500/20 transition-all flex items-center gap-2">
        <i class="fa-solid fa-calendar-check text-[10px]"></i>
        Attendance Hub
    </a>
</div>
@endsection

@section('content')
<div class="space-y-8">
    
    <!-- TOP METRICS GRID -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-stat-card 
            title="Today's Attendance" 
            value="{{ $attendanceToday }}" 
            icon="fa-solid fa-calendar-check" 
            trend="{{ $attendanceRate }}%" 
            trendUp="{{ $attendanceRate > 50 }}"
            color="emerald"
        />
        
        <x-stat-card 
            title="Active Sessions" 
            value="{{ $activeStudentsNow }}" 
            icon="fa-solid fa-users-viewfinder" 
            trend="Live" 
            trendUp="true"
            color="indigo"
        />

        <x-stat-card 
            title="Weekly Velocity" 
            value="{{ $attendanceThisWeek }}" 
            icon="fa-solid fa-chart-line" 
            trend="{{ abs($weeklyChange) }}%" 
            trendUp="{{ $weeklyTrend === 'up' }}"
            color="cyan"
        />

        <x-stat-card 
            title="Total Registry" 
            value="{{ $totalStudents }}" 
            icon="fa-solid fa-database" 
            trend="+{{ $newStudentsThisMonth }}" 
            trendUp="true"
            color="amber"
        />
    </div>

    <!-- MAIN ANALYTICS ROW -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Attendance Trends Chart -->
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-3xl p-8 shadow-lg relative overflow-hidden">
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-indigo-50 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Attendance Trends</h3>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1">Registry utilization over time</p>
                </div>
                <div class="flex items-center gap-1 bg-slate-50 rounded-xl p-1 border border-slate-200">
                    <button class="px-3 py-1.5 bg-indigo-100 text-indigo-700 text-[9px] font-black uppercase rounded-lg border border-indigo-200">7 Days</button>
                    <button class="px-3 py-1.5 text-[9px] font-black uppercase text-slate-500 hover:text-slate-900 transition-colors">30 Days</button>
                </div>
            </div>
            
            <div id="mainTrendsChart" class="w-full h-80"></div>
        </div>

        <!-- Distribution Donut -->
        <div class="bg-white border border-slate-200 rounded-3xl p-8 shadow-lg">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider mb-8">Daily Status</h3>
            <div id="distributionChart" class="w-full h-64"></div>
            
            <div class="mt-8 space-y-4">
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-200">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div>
                        <span class="text-xs font-bold text-slate-600">Present Today</span>
                    </div>
                    <span class="text-sm font-black text-slate-900">{{ $presentToday }}</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-200">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.5)]"></div>
                        <span class="text-xs font-bold text-slate-600">Absent Today</span>
                    </div>
                    <span class="text-sm font-black text-slate-900">{{ $absentToday }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- RECENT ACTIVITY & TOP PARTICIPANTS -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Recent Logs Table -->
        <div class="space-y-4">
            <div class="flex items-center justify-between px-2">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-indigo-500 text-[10px]"></i>
                    Recent Registries
                </h3>
                <a href="{{ route('admin.attendance.logs') }}" class="text-[9px] font-black uppercase tracking-widest text-indigo-600 hover:text-indigo-700 transition-all">Full History</a>
            </div>
            
            <x-glass-table :headers="['Student', 'Time In', 'Status']">
                @foreach ($recentAttendances->take(5) as $attendance)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-100 border border-indigo-200 flex items-center justify-center text-xs font-black text-indigo-600 group-hover:scale-110 transition-transform">
                                    {{ strtoupper(substr($attendance->student->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900 leading-none">{{ $attendance->student->name }}</p>
                                    <p class="text-[10px] font-bold text-slate-500 mt-1 uppercase">{{ $attendance->student->course }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-[11px] font-black text-slate-600">{{ $attendance->time_in->format('g:i A') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <x-status-badge 
                                type="{{ $attendance->time_out ? 'success' : 'info' }}" 
                                label="{{ $attendance->time_out ? 'Completed' : 'Active' }}" 
                            />
                        </td>
                    </tr>
                @endforeach
            </x-glass-table>
        </div>

        <!-- Most Active Students -->
        <div class="space-y-4">
            <div class="flex items-center justify-between px-2">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-trophy text-amber-500 text-[10px]"></i>
                    Top Engagement
                </h3>
                <span class="text-[9px] font-black uppercase tracking-widest text-slate-500">Active Cycle</span>
            </div>
            
            <div class="grid grid-cols-1 gap-4">
                @foreach($mostActiveThisWeek->take(3) as $index => $record)
                    <div class="relative overflow-hidden bg-white border border-slate-200 rounded-2xl p-4 flex items-center justify-between hover:border-indigo-300 hover:shadow-md transition-all duration-300 group">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-cyan-400 flex items-center justify-center text-white font-black text-sm shadow-lg group-hover:rotate-6 transition-transform">
                                #{{ $index + 1 }}
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">{{ $record->student->name }}</h4>
                                <p class="text-[10px] font-bold text-slate-500 uppercase">{{ $record->student->course }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-black text-slate-900">{{ $record->attendance_count }}</p>
                            <p class="text-[9px] font-black uppercase tracking-widest text-indigo-600">Sessions</p>
                        </div>
                        <!-- Mini dynamic bar -->
                        <div class="absolute bottom-0 left-0 h-1 bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.5)] transition-all duration-1000" style="width: {{ ($record->attendance_count / max($mostActiveThisWeek->pluck('attendance_count')->toArray() ?: [1])) * 100 }}%"></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- PEAK HOUR ANALYTICS -->
    <div class="relative overflow-hidden bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-3xl p-8 flex flex-col md:flex-row items-center justify-between gap-8 animate-fade-in shadow-lg">
        <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(circle_at_30%_50%,rgba(99,102,241,0.05),transparent)] pointer-events-none"></div>
        
        <div class="flex items-center gap-6 relative z-10">
            <div class="w-16 h-16 rounded-3xl bg-indigo-100 border border-indigo-200 flex items-center justify-center text-indigo-600 text-3xl shadow-sm">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <p class="text-[10px] uppercase tracking-[0.3em] font-black text-indigo-600 mb-2">Statistical Insight</p>
                <h3 class="text-2xl font-black text-slate-900 tracking-tight leading-tight">Peak Activity: <span class="text-indigo-600 underline decoration-indigo-200">{{ $peakHour }}</span></h3>
            </div>
        </div>
        
        <div class="text-center md:text-right relative z-10">
            <p class="text-5xl font-black text-slate-900 leading-none mb-1">{{ $peakHourCount }}</p>
            <p class="text-[10px] uppercase tracking-widest font-black text-slate-500">Parallel Accesses Today</p>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Shared chart options for light theme
    const commonOptions = {
        theme: { mode: 'light' },
        chart: {
            background: 'transparent',
            toolbar: { show: false },
            fontFamily: '"Plus Jakarta Sans", sans-serif'
        },
        grid: {
            borderColor: '#e2e8f0',
            xaxis: { lines: { show: false } }
        },
        dataLabels: { enabled: false },
    };

    // Main Trends Area Chart
    new ApexCharts(document.querySelector("#mainTrendsChart"), {
        ...commonOptions,
        chart: { ...commonOptions.chart, type: 'area', height: 320 },
        series: [{
            name: 'Attendees',
            data: {!! json_encode($attendancePerDay->pluck('total')) !!}
        }],
        xaxis: {
            categories: {!! json_encode($attendancePerDay->pluck('date')) !!},
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: { style: { colors: '#64748b', fontSize: '10px', fontWeight: 600 } }
        },
        yaxis: {
            labels: { style: { colors: '#64748b', fontSize: '10px', fontWeight: 600 } }
        },
        colors: ['#6366f1'],
        stroke: { curve: 'smooth', width: 3 },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.05,
                stops: [0, 90, 100]
            }
        },
        tooltip: { theme: 'light', x: { show: false } }
    }).render();

    // Distribution Donut Chart
    new ApexCharts(document.querySelector("#distributionChart"), {
        ...commonOptions,
        chart: { ...commonOptions.chart, type: 'donut', height: 260 },
        series: [{{ $presentToday }}, {{ $absentToday }}],
        labels: ['Present', 'Absent'],
        colors: ['#10b981', '#f43f5e'],
        stroke: { show: false },
        plotOptions: {
            pie: {
                donut: {
                    size: '75%',
                    labels: {
                        show: true,
                        name: { show: false },
                        value: {
                            show: true,
                            fontSize: '24px',
                            fontWeight: 900,
                            color: '#0f172a',
                            offsetY: 8,
                            formatter: (v) => v
                        },
                        total: {
                            show: true,
                            label: 'Total',
                            color: '#64748b',
                            fontSize: '10px',
                            fontWeight: 700
                        }
                    }
                }
            }
        },
        legend: { show: false }
    }).render();
});
</script>
@endpush
@endsection