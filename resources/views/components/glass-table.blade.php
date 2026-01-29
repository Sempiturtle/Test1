@props(['headers'])

<div class="relative overflow-hidden bg-white border border-slate-200 rounded-2xl shadow-sm">
    <!-- Header accent -->
    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-600 via-indigo-500 to-indigo-400"></div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    @foreach($headers as $header)
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-slate-600">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>
