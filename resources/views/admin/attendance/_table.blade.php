<div class="bg-white/90 backdrop-blur-xl border border-gray-200 shadow-xl rounded-2xl overflow-hidden">

    <!-- Header -->
    <div class="px-6 py-4 border-b flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Attendance Records</h3>
            <p class="text-sm text-gray-500">Recent student activity</p>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wide">
                <tr>
                    <th class="px-6 py-4 text-left">Student</th>
                    <th class="px-6 py-4 text-left">RFID</th>
                    <th class="px-6 py-4 text-left">Time In</th>
                    <th class="px-6 py-4 text-left">Time Out</th>
                    <th class="px-6 py-4 text-left">Date</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @foreach ($logs as $log)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $log->student->name }}
                        </td>

                        <td class="px-6 py-4 font-mono text-gray-700">
                            {{ $log->rfid_uid }}
                        </td>

                        <td class="px-6 py-4 text-gray-700">
                            {{ $log->time_in ? $log->time_in->format('h:i A') : '—' }}
                        </td>

                        <td class="px-6 py-4 text-gray-700">
                            {{ $log->time_out ? $log->time_out->format('h:i A') : '—' }}
                        </td>

                        <td class="px-6 py-4 text-gray-600">
                            {{ $log->created_at->format('M d, Y') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
