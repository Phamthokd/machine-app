<html xmlns:o="urn:schemas-microsoft-com:office:office"
    xmlns:x="urn:schemas-microsoft-com:office:excel"
    xmlns="http://www.w3.org/TR/REC-html40">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            white-space: normal;
        }

        th,
        td {
            border: 1px solid black;
            padding: 5px;
            vertical-align: middle;
            font-family: Arial, sans-serif;
            font-size: 11pt;
            mso-number-format: "\@";
        }

        .header {
            background-color: #d9e1f2;
            font-weight: bold;
            text-align: center;
        }

        .title-row td {
            background-color: #d9e1f2;
            font-weight: bold;
        }

        .danger {
            color: #e60000;
        }
    </style>
</head>

<body>
    <table>
        <!-- Header Row -->
        <tr class="title-row" style="height: 40px;">
            <td colspan="4">
                Bộ phận nhận kiểm tra: {{ $audit->template->department_name }}<br style="mso-data-placement:same-cell;">
                Người kiểm tra: {{ strtoupper($audit->auditor->name ?? 'N/A') }}
            </td>
            <td colspan="2">Ngày kiểm tra: {{ $audit->created_at->format('d/m/Y') }}</td>
            <td>Tỷ lệ tuân thủ: {{ $audit->score }}%</td>
        </tr>
        <!-- Table Column Headers -->
        <tr class="header">
            <th width="40">No</th>
            <th width="320">Hạng mục yêu cầu</th>
            <th width="260">Nội dung</th>
            <th width="70">Điểm quy định</th>
            <th width="80">Ảnh đi kèm</th>
            <th width="230">Nội dung đánh giá lần 2</th>
            <th width="80">Ảnh đánh giá lần 2</th>
        </tr>
        <!-- Data Rows -->
        @foreach($audit->results as $index => $result)
        @php
        $imgCount = !$result->is_passed && !empty($result->image_path) ? count((array)$result->image_path) : 0;
        $hasReviewImg = !empty($result->review_image_path);
        $maxImgs = max($imgCount, $hasReviewImg ? 1 : 0);
        $rowHeight = $maxImgs > 0 ? ($maxImgs * 70) : 35;
        @endphp
        <tr style="height: {{ $rowHeight }}px;">
            <td align="center" style="vertical-align: middle;">{{ $index + 1 }}</td>
            <td style="vertical-align: middle;">{{ $result->criterion ? $result->criterion->content : 'N/A' }}</td>
            <td class="{{ $result->is_passed ? '' : 'danger' }}" style="vertical-align: middle;">
                @if($result->is_passed)
                Đạt
                @else
                Không, {{ $result->note }}
                @if($result->root_cause)
                <br style="mso-data-placement:same-cell;"><b>NN gốc rễ:</b> {{ $result->root_cause }}
                <br style="mso-data-placement:same-cell;"><b>BP khắc phục:</b> {{ $result->corrective_action }}
                <br style="mso-data-placement:same-cell;"><b>Hạn:</b> {{ \Carbon\Carbon::parse($result->improvement_deadline)->format('d/m/Y') }}
                @if($result->improver_name)
                <br style="mso-data-placement:same-cell;"><b>Người cải thiện:</b> {{ $result->improver_name }}
                @endif
                @endif
                @endif
            </td>
            <td align="center" style="vertical-align: middle;">{{ $result->is_passed ? '1' : '0' }}</td>
            <td align="center" style="vertical-align: top; padding: 2px;">
                @if(!$result->is_passed && !empty($result->image_path))
                @foreach((array)$result->image_path as $path)
                <img src="{{ asset($path) }}" width="60" height="60"><br>
                @endforeach
                @endif
            </td>
            <td class="{{ $result->reviewer_name ? '' : 'danger' }}" style="vertical-align: middle;">
                @if($result->reviewer_name)
                @if($result->review_note)
                {{ $result->review_note }}
                @endif
                <br style="mso-data-placement:same-cell;"><b>Người ĐG:</b> {{ $result->reviewer_name }}
                <br style="mso-data-placement:same-cell;"><b>Thời gian:</b> {{ \Carbon\Carbon::parse($result->reviewed_at)->format('H:i d/m/Y') }}
                @endif
            </td>
            <td align="center" style="vertical-align: top; padding: 2px;">
                @if($result->review_image_path)
                @php
                $r_img = str_starts_with($result->review_image_path, 'public/')
                ? url(str_replace('public/', 'storage/', $result->review_image_path))
                : url(ltrim($result->review_image_path, '/'));
                @endphp
                <img src="{{ $r_img }}" width="60" height="60" style="display:block; margin: 2px auto;">
                @endif
            </td>
        </tr>
        @endforeach
    </table>
</body>

</html>