<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Timesheet Report - Supervisor</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        .header { margin-bottom: 15px; text-align: center; }
        .meta { font-size: 11px; color: #6b7280; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #d1d5db; padding: 6px; }
        th { background: #f3f4f6; }
        .total-row { font-weight: bold; background: #f9fafb; }
    </style>
</head>
<body>

<div class="header">
    <h2>Timesheet Report - Supervisor</h2>
    <div class="meta">
        Generated: {{ now()->format('Y-m-d H:i') }}<br>
        Supervisor: {{ $user->first_name }} {{ $user->last_name }}<br>
        @if(request('from')) From: {{ request('from') }}<br> @endif
        @if(request('to')) To: {{ request('to') }}<br> @endif
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Employee</th>
            <th>Project</th>
            <th>Time</th>
            <th>Description</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalMinutes = 0;
        @endphp
        
        @forelse($entries as $entry)
            @php
                $entryMinutes = ($entry->hours * 60) + $entry->minutes;
                $totalMinutes += $entryMinutes;
            @endphp
            <tr>
                <td>{{ \Carbon\Carbon::parse($entry->date)->format('d-m-Y') }}</td>
                <td>{{ $entry->employee->first_name }} {{ $entry->employee->last_name }}</td>
                <td>{{ $entry->project->name ?? '-' }}</td>
                <td>{{ $entry->hours }}h {{ $entry->minutes }}m</td>
                <td>{{ $entry->description }}</td>
                <td>{{ ucfirst($entry->status) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6">No timesheet entries found.</td>
            </tr>
        @endforelse
        
        @if($entries->count() > 0)
            @php
                $totalHours = floor($totalMinutes / 60);
                $remainingMinutes = $totalMinutes % 60;
            @endphp
            <tr class="total-row">
                <td colspan="3" style="text-align: right;">Total Hours Worked:</td>
                <td>{{ $totalHours }}h {{ $remainingMinutes }}m</td>
                <td colspan="2"></td>
            </tr>
        @endif
    </tbody>
</table>

</body>
</html>