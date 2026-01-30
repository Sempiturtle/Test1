@extends('layouts.admin')
@section('title', 'Audit Trail')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 gap-6">
        <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-lg">
            <h3 class="text-sm font-black text-slate-900 tracking-widest uppercase mb-6 flex items-center gap-2">
                <i class="fa-solid fa-shield-halved text-indigo-600"></i>
                Security Logs
            </h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 text-[10px] uppercase tracking-widest text-slate-500">
                            <th class="p-4 font-bold">User</th>
                            <th class="p-4 font-bold">Action</th>
                            <th class="p-4 font-bold">Details</th>
                            <th class="p-4 font-bold">IP Address</th>
                            <th class="p-4 font-bold">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs font-medium text-slate-700">
                        @foreach($logs as $log)
                        <tr class="group hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-0">
                            <td class="p-4">
                                <span class="font-bold text-slate-900">{{ $log->user->name ?? 'System/Guest' }}</span>
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded bg-slate-100 border border-slate-200 text-[10px] font-black uppercase tracking-wider text-slate-600 group-hover:bg-indigo-50 group-hover:text-indigo-600 group-hover:border-indigo-100 transition-colors">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="p-4 text-slate-500 max-w-sm truncate" title="{{ $log->details }}">
                                {{ $log->details }}
                            </td>
                            <td class="p-4 font-mono text-slate-500 text-[10px]">
                                {{ $log->ip_address }}
                            </td>
                            <td class="p-4 text-slate-400">
                                {{ $log->created_at->format('M d, Y h:i A') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
