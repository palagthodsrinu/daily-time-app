<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>BBIS Timesheet Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        .header { margin-bottom: 15px; }
        .meta { font-size: 11px; color: #6b7280; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #d1d5db; padding: 6px; }
        th { background: #f3f4f6; }
        .total-row { font-weight: bold; background: #f9fafb; }
    </style>
</head>
<body>

<h2>BBIS Timesheet Report</h2>

<div class="meta">
    Generated: {{ now()->format('Y-m-d') }}<br>
    @if($from) From: {{ $from }}<br> @endif
    @if($to)   To: {{ $to }}<br>   @endif
</div>

<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>User</th>
            <th>Employee Type</th>
            <th>Project</th>
            <th>Description</th>
            <th>Hours</th>
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
                <td>{{ $entry->user->first_name ?? '' }} {{ $entry->user->last_name ?? '' }}</td>
                <td>{{ $entry->user->employee_type ?? '-' }}</td>
                <td>{{ $entry->project->name ?? '-' }}</td>
                <td>{{ $entry->description }}</td>
                <td>{{ $entry->hours }}h {{ $entry->minutes }}m</td>
                <td>{{ ucfirst($entry->status) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7">No records found.</td>
            </tr>
        @endforelse
        
        @if($entries->count() > 0)
            @php
                $totalHours = floor($totalMinutes / 60);
                $remainingMinutes = $totalMinutes % 60;
            @endphp
            <tr class="total-row">
                <td colspan="5" style="text-align: right;">Total Hours Worked:</td>
                <td>{{ $totalHours }}h {{ $remainingMinutes }}m</td>
                <td></td>
            </tr>
        @endif
    </tbody>
</table>

</body>
</html>