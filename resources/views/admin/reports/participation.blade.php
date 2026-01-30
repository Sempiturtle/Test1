<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Guidance Participation Report</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 50px; border-bottom: 2px solid #6366f1; padding-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #6366f1; }
        .title { font-size: 18px; text-transform: uppercase; letter-spacing: 2px; margin-top: 10px; }
        .meta { font-size: 12px; color: #666; margin-top: 5px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f8fafc; color: #6366f1; text-align: left; font-size: 10px; text-transform: uppercase; padding: 12px; border-bottom: 1px solid #e2e8f0; }
        td { padding: 12px; font-size: 11px; border-bottom: 1px solid #f1f5f9; }
        
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .badge-low { background-color: #ecfdf5; color: #10b981; }
        .badge-medium { background-color: #fffbeb; color: #f59e0b; }
        .badge-high { background-color: #fef2f2; color: #ef4444; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">AISAT GUIDANCE COUNSELING</div>
        <div class="title">Official Participation & Case Summary</div>
        <div class="meta">Generated on {{ now()->format('F d, Y h:i A') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Full Name</th>
                <th>Academic Unit</th>
                <th>Session Time</th>
                <th>Category</th>
                <th>Priority</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr>
                <td>{{ $log->student->student_id ?? 'N/A' }}</td>
                <td style="font-weight: bold;">{{ $log->student->name ?? 'Unknown Entity' }}</td>
                <td>{{ $log->student->course ?? 'N/A' }}</td>
                <td>{{ $log->time_in ? $log->time_in->timezone('Asia/Manila')->format('M d, Y h:i A') : '—' }}</td>
                <td>{{ $log->category ?? 'Routine' }}</td>
                <td>
                    <span class="badge badge-{{ $log->severity }}">
                        {{ $log->severity }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Confidential Document &bull; Internal Academic Use Only &bull; Aisat Guidance Information System
    </div>
</body>
</html>
