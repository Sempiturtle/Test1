<div class="bg-white shadow-lg rounded-xl overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-indigo-50 text-gray-700">
            <tr>
                <th class="p-3 text-left">Student</th>
                <th class="p-3">RFID</th>
                <th class="p-3">Time In</th>
                <th class="p-3">Time Out</th>
                <th class="p-3">Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $log)
                <tr class="border-t hover:bg-indigo-50">
                    <td class="p-3">{{ $log->student->name }}</td>
                    <td class="p-3 font-mono">{{ $log->rfid_uid }}</td>
                    <td class="p-3">{{ optional($log->time_in)->format('h:i A') }}</td>
                    <td class="p-3">{{ $log->time_out ? $log->time_out->format('h:i A') : '-' }} ddd</td>
                    <td class="p-3">{{ $log->created_at->format('M d, Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
