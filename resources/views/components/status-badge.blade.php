@props(['type' => 'info', 'label'])

@php
    $types = [
        'success' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20 shadow-emerald-500/10',
        'error' => 'bg-rose-500/10 text-rose-400 border-rose-500/20 shadow-rose-500/10',
        'warning' => 'bg-amber-500/10 text-amber-400 border-amber-500/20 shadow-amber-500/10',
        'info' => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20 shadow-indigo-500/10',
        'cyan' => 'bg-cyan-500/10 text-cyan-400 border-cyan-500/20 shadow-cyan-500/10',
    ];
    $currentType = $types[$type] ?? $types['info'];
    
    $icons = [
        'success' => 'fa-circle-check',
        'error' => 'fa-circle-xmark',
        'warning' => 'fa-triangle-exclamation',
        'info' => 'fa-circle-info',
        'cyan' => 'fa-bolt',
    ];
    $icon = $icons[$type] ?? $icons['info'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full border text-[10px] font-black uppercase tracking-wider shadow-inner transition-all ' . $currentType]) }}>
    <i class="fa-solid {{ $icon }}"></i>
    {{ $label }}
</span>
