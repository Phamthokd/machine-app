<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>In báo cáo môi trường</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #111827;
            margin: 16px;
        }

        .sheet-title {
            text-align: center;
            margin-bottom: 12px;
        }

        .sheet-title h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .sheet-title p {
            margin: 4px 0 0;
            font-size: 12px;
        }

        .meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 10px;
        }

        th, td {
            border: 1px solid #374151;
            padding: 4px;
            text-align: center;
            vertical-align: middle;
        }

        thead th {
            background: #f3f4f6;
        }

        .notes {
            margin-top: 12px;
            font-size: 11px;
        }

        @media print {
            body {
                margin: 0;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="sheet-title">
        <h1>Kiểm tra nhiệt độ, độ ẩm</h1>
        <p>Daily Humidity and Temperature Record</p>
    </div>

    <div class="meta">
        <div><strong>Vị trí:</strong> {{ $report->department_name }}</div>
        <div><strong>Tháng:</strong> {{ $report->period_label }}</div>
        <div><strong>Người tạo:</strong> {{ $report->creator->name ?? 'N/A' }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">Ngày</th>
                <th colspan="4">Độ ẩm (%)</th>
                <th colspan="4">Nhiệt độ (°C)</th>
                <th rowspan="2">Thời tiết</th>
                <th colspan="4">Cải thiện</th>
                <th rowspan="2">Người kiểm tra</th>
            </tr>
            <tr>
                @foreach($timeSlots as $slot)
                    <th>{{ substr($slot, 0, 2) . ':' . substr($slot, 2, 2) }}</th>
                @endforeach
                @foreach($timeSlots as $slot)
                    <th>{{ substr($slot, 0, 2) . ':' . substr($slot, 2, 2) }}</th>
                @endforeach
                @foreach($timeSlots as $slot)
                    <th>{{ substr($slot, 0, 2) . ':' . substr($slot, 2, 2) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($report->entries as $entry)
                <tr>
                    <td>{{ $entry->day_number }}</td>
                    @foreach($timeSlots as $slot)
                        <td>{{ $entry->{'humidity_' . $slot} }}</td>
                    @endforeach
                    @foreach($timeSlots as $slot)
                        <td>{{ $entry->{'temperature_' . $slot} }}</td>
                    @endforeach
                    <td>{{ $entry->weather }}</td>
                    @foreach($timeSlots as $slot)
                        <td>{{ $entry->{'action_' . $slot} }}</td>
                    @endforeach
                    <td>{{ $entry->checked_by }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="notes">
        <div><strong>Ghi chú chuẩn:</strong> Nhiệt độ 18-37°C, độ ẩm 40-65%.</div>
        @if($report->note)
            <div><strong>Ghi chú báo cáo:</strong> {{ $report->note }}</div>
        @endif
    </div>
</body>
</html>
