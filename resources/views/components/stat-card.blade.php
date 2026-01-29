@props(['title', 'value', 'icon', 'trend' => null, 'trendUp' => true, 'color' => 'indigo'])

@php
    $colors = [
        'indigo' => 'from-indigo-600 to-indigo-500 text-indigo-600 bg-indigo-50 border-indigo-100',
        'emerald' => 'from-emerald-600 to-emerald-500 text-emerald-600 bg-emerald-50 border-emerald-100',
        'rose' => 'from-rose-600 to-rose-500 text-rose-600 bg-rose-50 border-rose-100',
        'amber' => 'from-amber-600 to-amber-500 text-amber-600 bg-amber-50 border-amber-100',
        'cyan' => 'from-cyan-600 to-cyan-500 text-cyan-600 bg-cyan-50 border-cyan-100',
    ];
    $currentColor = $colors[$color] ?? $colors['indigo'];
@endphp

<div {{ $attributes->merge(['class' => 'relative group overflow-hidden bg-white border border-slate-200 rounded-2xl p-6 hover:shadow-lg transition-all duration-300']) }}>
    
    <div class="flex items-start justify-between mb-4">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ explode(' ', $currentColor)[0] }} {{ explode(' ', $currentColor)[1] }} flex items-center justify-center text-white shadow-md group-hover:scale-110 transition-transform duration-300">
            <i class="{{ $icon }} text-lg"></i>
        </div>
        
        @if($trend)
            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider {{ $trendUp ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                <i class="fa-solid {{ $trendUp ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
                {{ $trend }}
            </div>
        @endif
    </div>

    <div>
        <p class="text-[10px] uppercase tracking-wider font-semibold text-slate-500 mb-1">{{ $title }}</p>
        <h3 class="text-3xl font-bold text-slate-900 tracking-tight">{{ $value }}</h3>
    </div>

    <!-- Accent Bar -->
    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r {{ explode(' ', $currentColor)[0] }} {{ explode(' ', $currentColor)[1] }} opacity-10 rounded-b-2xl"></div>
</div>
