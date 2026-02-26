<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style>
        table { border-collapse: collapse; width: 100%; white-space: normal; }
        th, td { border: 1px solid black; padding: 5px; vertical-align: middle; font-family: Arial, sans-serif; font-size: 11pt; mso-number-format:"\@"; }
        .header { background-color: #d9e1f2; font-weight: bold; text-align: center; }
        .title-row td { background-color: #d9e1f2; font-weight: bold; }
        .danger { color: #e60000; }
    </style>
</head>
<body>
    <table>
        <!-- Header Row -->
        <tr class="title-row" style="height: 40px;">
            <td colspan="3">
                Bộ phận nhận kiểm tra: {{ $audit->template->department_name }}<br style="mso-data-placement:same-cell;">
                Người kiểm tra: {{ strtoupper($audit->auditor->name ?? 'N/A') }}
            </td>
            <td>Ngày kiểm tra: {{ $audit->created_at->format('d/m/Y') }}</td>
            <td>Tỷ lệ tuân thủ: {{ $audit->score }}%</td>
        </tr>
        <!-- Table Column Headers -->
        <tr class="header">
            <th width="50">No</th>
            <th width="400">Hạng mục yêu cầu</th>
            <th width="300">Nội dung</th>
            <th width="100">Điểm quy định</th>
            <th width="120">Ảnh đi kèm</th>
        </tr>
        <!-- Data Rows -->
        @foreach($audit->results as $index => $result)
        <tr style="height: {{ !$result->is_passed && $result->image_path ? '120px' : '30px' }};">
            <td align="center">{{ $index + 1 }}</td>
            <td>{{ $result->criterion ? $result->criterion->content : 'N/A' }}</td>
            <td class="{{ $result->is_passed ? '' : 'danger' }}">
                @if($result->is_passed)
                    Đạt
                @else
                    Không, {{ $result->note }}
                    @if($result->root_cause)
                        <br style="mso-data-placement:same-cell;"><b>NN gốc rễ:</b> {{ $result->root_cause }}
                        <br style="mso-data-placement:same-cell;"><b>BP khắc phục:</b> {{ $result->corrective_action }}
                        <br style="mso-data-placement:same-cell;"><b>Hạn:</b> {{ \Carbon\Carbon::parse($result->improvement_deadline)->format('d/m/Y') }}
                    @endif
                @endif
            </td>
            <td align="center">{{ $result->is_passed ? '1' : '0' }}</td>
            <td align="center">
                @if(!$result->is_passed && $result->image_path)
                    <img src="{{ asset($result->image_path) }}" width="100" height="100">
                @endif
            </td>
        </tr>
        @endforeach
    </table>
</body>
</html>
