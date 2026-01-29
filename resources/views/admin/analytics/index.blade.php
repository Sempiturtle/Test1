@extends('layouts.admin')
@section('title', 'Intelligence Reports')

@section('actions')
<div class="flex items-center gap-3">
    <div class="flex items-center bg-white border border-slate-200 rounded-xl px-4 py-2 shadow-sm">
        <i class="fa-solid fa-calendar text-slate-500 text-[10px] mr-3"></i>
        <span class="text-[10px] text-slate-600 font-black uppercase tracking-widest">Active Academic Cycle</span>
    </div>
    <a href="{{ route('admin.analytics.export') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 rounded-xl text-xs font-bold uppercase tracking-wider text-white shadow-lg shadow-indigo-500/20 transition-all flex items-center gap-2">
        <i class="fa-solid fa-file-pdf text-[10px]"></i>
        Export Analysis
    </a>
</div>
@endsection

@section('content')
<div class="space-y-8">
    
    <!-- TOP LEVEL ANALYTICS -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Attendance Velocity -->
        <div class="bg-white border border-slate-200 rounded-3xl p-8 shadow-lg relative overflow-hidden">
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-indigo-50 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Participation Velocity</h3>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1">7-Day Engagement Trend</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo-100 border border-indigo-200 flex items-center justify-center text-indigo-600">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
            
            <div id="velocityChart" class="w-full h-72"></div>
        </div>

        <!-- Peak Utilization Heatmap -->
        <div class="bg-white border border-slate-200 rounded-3xl p-8 shadow-lg">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Utilization Density</h3>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1">Hourly engagement distribution</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-cyan-100 border border-cyan-200 flex items-center justify-center text-cyan-600">
                    <i class="fa-solid fa-fire"></i>
                </div>
            </div>
            
            <div id="utilizationChart" class="w-full h-72"></div>
        </div>
    </div>

    <!-- MIDDLE ROW: AT-RISK STUDENTS -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- At-Risk Monitoring -->
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-3xl p-8 shadow-lg border-l-4 border-l-rose-500">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-sm font-black text-rose-600 uppercase tracking-wider">At-Risk Student Monitoring</h3>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1">Requires immediate guidance intervention</p>
                </div>
                <span class="px-3 py-1 bg-rose-50 text-rose-600 border border-rose-200 rounded-full text-[10px] font-black uppercase tracking-wider">
                    {{ count($atRiskStudents) }} Critical Entities
                </span>
            </div>

            <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                @foreach($atRiskStudents->take(5) as $student)
                    <div class="flex items-center justify-between p-4 bg-slate-50 border border-slate-200 rounded-2xl hover:bg-slate-100 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-rose-100 border border-rose-200 flex items-center justify-center text-rose-600 font-black">
                                {{ strtoupper(substr($student['name'], 0, 1)) }}
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">{{ $student['name'] }}</h4>
                                <p class="text-[9px] font-black text-slate-500 uppercase">{{ $student['course'] }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-6">
                            <div class="text-right">
                                <p class="text-xs font-black text-rose-600">{{ $student['days_since_last_attendance'] ?? '∞' }} Days</p>
                                <p class="text-[9px] font-bold text-slate-500 uppercase">Since last sync</p>
                            </div>
                            <button 
                                onclick="triggerFollowUp({{ $student['id'] }}, '{{ $student['name'] }}')"
                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg shadow-indigo-600/20">
                                Protocol
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Metric Cards Container -->
        <div class="space-y-6">
            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 border border-indigo-200 rounded-3xl p-8 shadow-lg relative group">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-indigo-600 mb-6">Retention Velocity</p>
                <div class="flex items-end justify-between">
                    <div>
                        <h4 class="text-4xl font-black text-slate-900 leading-none">{{ $retentionVelocity }}<span class="text-lg ml-1 text-slate-500">Days</span></h4>
                        <p class="text-xs font-bold text-slate-600 mt-2 uppercase">Mean Return Interval</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-500 rounded-2xl flex items-center justify-center text-white text-xl shadow-lg group-hover:rotate-12 transition-transform">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 border border-emerald-200 rounded-3xl p-8 shadow-lg relative group">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-emerald-600 mb-6">Wellness Resilience</p>
                <div class="flex items-end justify-between">
                    <div>
                        <h4 class="text-4xl font-black text-slate-900 leading-none">{{ $wellnessResilience }}%</h4>
                        <p class="text-xs font-bold text-slate-600 mt-2 uppercase">Positive Outcome Ratio</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-500 rounded-2xl flex items-center justify-center text-white text-xl shadow-lg group-hover:rotate-12 transition-transform">
                        <i class="fa-solid fa-shield-heart"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DATA TABLE SUMMARY -->
    <div class="space-y-4">
        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-3">
            <i class="fa-solid fa-ranking-star text-amber-500 text-[10px]"></i>
            Student Engagement Metrics
        </h3>
        
        <x-glass-table :headers="['Student Entity', 'Programs', 'Session Accumulation', 'Status']">
            @foreach($studentEngagement->take(10) as $student)
                <tr class="hover:bg-slate-50 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-indigo-100 border border-indigo-200 flex items-center justify-center text-xs font-black text-indigo-600">
                                {{ strtoupper(substr($student->name, 0, 1)) }}
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">{{ $student->name }}</h4>
                                <p class="text-[10px] font-bold text-slate-500 uppercase">{{ $student->course }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs font-bold text-slate-600">Default Program</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-black text-slate-900">{{ $student->attendances_count }}</span>
                            <div class="flex-1 max-w-[100px] h-1.5 bg-slate-200 rounded-full overflow-hidden">
                                <div class="h-full bg-indigo-500" style="width: {{ ($student->attendances_count / max($studentEngagement->pluck('attendances_count')->toArray() ?: [1])) * 100 }}%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <x-status-badge type="info" label="Active Sync" />
                    </td>
                </tr>
            @endforeach
        </x-glass-table>
    </div>

</div>

<!-- TOAST -->
<div id="toast" class="fixed bottom-8 right-8 z-[200] hidden animate-fade-in">
    <div class="flex items-center gap-4 bg-slate-900 border border-white/10 rounded-2xl px-6 py-4 shadow-2xl relative overflow-hidden">
        <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-indigo-500 shadow-[0_0_15px_rgba(99,102,241,0.5)]"></div>
        <div class="w-10 h-10 rounded-xl bg-indigo-500/20 text-indigo-400 border border-indigo-500/20 flex items-center justify-center text-xl">
            <i class="fa-solid fa-satellite-dish"></i>
        </div>
        <div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-500 mb-0.5">Transmission Successful</p>
            <p id="toast-msg" class="text-sm font-bold text-white"></p>
        </div>
    </div>
</div>

@push('scripts')
<script>
function triggerFollowUp(studentId, name) {
    fetch(`/admin/analytics/follow-up/${studentId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(res => {
        const toast = document.getElementById('toast');
        const msg = document.getElementById('toast-msg');
        msg.textContent = res.message;
        toast.classList.remove('hidden');
        setTimeout(() => toast.classList.add('hidden'), 4000);
    });
}

document.addEventListener('DOMContentLoaded', () => {
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

    // Attendance Velocity
    new ApexCharts(document.querySelector("#velocityChart"), {
        ...commonOptions,
        chart: { ...commonOptions.chart, type: 'area', height: 280 },
        series: [{
            name: 'Attendees',
            data: {!! json_encode($attendanceTrends->pluck('count')) !!}
        }],
        xaxis: {
            categories: {!! json_encode($attendanceTrends->pluck('date')) !!},
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
        }
    }).render();

    // Utilization Columns
    new ApexCharts(document.querySelector("#utilizationChart"), {
        ...commonOptions,
        chart: { ...commonOptions.chart, type: 'bar', height: 280 },
        series: [{
            name: 'Avg Count',
            data: {!! json_encode($peakUtilization->pluck('count')) !!}
        }],
        xaxis: {
            categories: {!! json_encode($peakUtilization->pluck('hour')->map(fn($h) => $h.':00')) !!},
            labels: { style: { colors: '#64748b', fontSize: '10px', fontWeight: 600 } }
        },
        colors: ['#22d3ee'],
        plotOptions: {
            bar: {
                borderRadius: 4,
                columnWidth: '60%',
                colors: {
                    ranges: [{ from: 0, to: 100, color: '#22d3ee' }]
                }
            }
        },
    }).render();
});
</script>
@endpush
@endsection