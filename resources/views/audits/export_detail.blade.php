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
                {{ __('messages.dept_being_audited') }}: {{ $audit->template->department_name }}<br style="mso-data-placement:same-cell;">
                {{ __('messages.auditor_label') }}: {{ strtoupper($audit->auditor->name ?? 'N/A') }}
            </td>
            <td colspan="2">{{ __('messages.audit_date_label') }}: {{ $audit->created_at->format('d/m/Y') }}</td>
            <td>{{ __('messages.compliance_rate_label') }}: {{ $audit->score }}%</td>
        </tr>
        <!-- Table Column Headers -->
        <tr class="header">
            <th width="40">No</th>
            <th width="320">{{ __('messages.audit_criterion_header') }}</th>
            <th width="260">{{ __('messages.content_header') }}</th>
            <th width="70">{{ __('messages.standard_score_header') }}</th>
            <th width="80">{{ __('messages.attached_image_header') }}</th>
            <th width="260">{{ __('messages.improvement_content_header') }}</th>
            <th width="80">{{ __('messages.improvement_image_header') }}</th>
            <th width="230">{{ __('messages.second_round_eval_header') }}</th>
            <th width="80">{{ __('messages.second_round_image_header') }}</th>
        </tr>
        <!-- Data Rows -->
        @foreach($audit->results as $index => $result)
        @php
        $imgCount = !$result->is_passed && !empty($result->image_path) ? count((array)$result->image_path) : 0;
        $compImgCount = $result->is_completed && !empty($result->completion_image_path) ? count((array)$result->completion_image_path) : 0;
        $hasReviewImg = !empty($result->review_image_path);
        
        $maxImgs = max($imgCount, $compImgCount, $hasReviewImg ? 1 : 0);
        $rowHeight = $maxImgs > 0 ? ($maxImgs * 70) : 35;
        @endphp
        <tr style="height: {{ $rowHeight }}px;">
            <td align="center" style="vertical-align: middle;">{{ $index + 1 }}</td>
            <td style="vertical-align: middle;">{{ $result->criterion ? __($result->criterion->content) : 'N/A' }}</td>
            <td class="{{ $result->is_passed ? '' : 'danger' }}" style="vertical-align: middle;">
                @if($result->is_passed)
                {{ __('messages.audit_pass_short') }}
                @else
                {{ __('messages.audit_fail_short') }}, {{ $result->note }}
                @if($result->root_cause)
                <br style="mso-data-placement:same-cell;"><b>{{ __('messages.root_cause_label') }}:</b> {{ $result->root_cause }}
                <br style="mso-data-placement:same-cell;"><b>{{ __('messages.corrective_action_label') }}:</b> {{ $result->corrective_action }}
                <br style="mso-data-placement:same-cell;"><b>{{ __('messages.deadline_label') }}:</b> {{ \Carbon\Carbon::parse($result->improvement_deadline)->format('d/m/Y') }}
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
            <td style="vertical-align: middle;">
                @if($result->is_completed)
                {{ $result->completion_note }}
                @if($result->completed_at)
                <br style="mso-data-placement:same-cell;"><b>{{ __('messages.time') }}:</b> {{ \Carbon\Carbon::parse($result->completed_at)->format('H:i d/m/Y') }}
                @endif
                @if($result->improver_name)
                <br style="mso-data-placement:same-cell;"><b>{{ __('messages.improver_label') }}:</b> {{ $result->improver_name }}
                @endif
                @endif
            </td>
            <td align="center" style="vertical-align: top; padding: 2px;">
                @if($result->is_completed && !empty($result->completion_image_path))
                @foreach((array)$result->completion_image_path as $p_path)
                <img src="{{ asset($p_path) }}" width="60" height="60"><br>
                @endforeach
                @endif
            </td>
            <td class="{{ $result->reviewer_name ? '' : 'danger' }}" style="vertical-align: middle;">
                @if($result->reviewer_name)
                @if($result->review_note)
                {{ $result->review_note }}
                @endif
                <br style="mso-data-placement:same-cell;"><b>{{ __('messages.auditor_short_label') }}:</b> {{ $result->reviewer_name }}
                <br style="mso-data-placement:same-cell;"><b>{{ __('messages.time') }}:</b> {{ \Carbon\Carbon::parse($result->reviewed_at)->format('H:i d/m/Y') }}
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