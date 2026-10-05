<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <title>{{ __('messages.candidate_form_title') }} — {{ $candidate->full_name }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 11pt;
            color: #000;
            background: white;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 12mm 14mm;
        }

        /* Header */
        .form-header {
            text-align: center;
            border: 1.5px solid #000;
            margin-bottom: 4px;
            padding: 6px 4px;
        }

        .header-row {
            display: flex;
            align-items: stretch;
        }

        .header-logo {
            width: 70px;
            border-right: 1px solid #000;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 4px;
        }

        .logo-box {
            font-size: 18pt;
            font-weight: 900;
            letter-spacing: .05em;
            border: 2px solid #000;
            padding: 2px 8px;
        }

        .logo-sub {
            font-size: 6pt;
            margin-top: 2px;
            text-align: center;
        }

        .header-main {
            flex: 1;
            padding: 4px 8px;
        }

        .company-name {
            font-size: 7.5pt;
        }

        .form-title {
            font-size: 16pt;
            font-weight: 900;
            letter-spacing: .05em;
            margin: 2px 0;
        }

        .form-title-zh {
            font-size: 11pt;
        }

        .form-notice {
            font-size: 7pt;
            color: #333;
            margin-top: 4px;
            text-align: left;
        }

        .header-type {
            width: 80px;
            border-left: 1px solid #000;
            padding: 4px;
            font-size: 8pt;
            display: flex;
            flex-direction: column;
            gap: 4px;
            justify-content: center;
        }

        .type-item {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .checkbox {
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 10pt;
        }

        /* Table cells */
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 9.5pt;
        }

        td,
        th {
            border: 1px solid #000;
            padding: 3px 5px;
            vertical-align: middle;
        }

        .label-cell {
            background: #f5f5f5;
            font-size: 8.5pt;
            white-space: nowrap;
            width: 90px;
        }

        .label-zh {
            font-size: 7pt;
            display: block;
            color: #555;
        }

        .value-cell {
            min-height: 22px;
        }

        .value-cell.tall {
            min-height: 18px;
        }

        .section-header {
            background: #e8e8e8;
            font-weight: bold;
            font-size: 9pt;
        }

        /* Photo */
        .photo-cell {
            width: 85px;
            border-left: 1px solid #000;
            text-align: center;
            vertical-align: middle;
        }

        .photo-cell img {
            max-width: 80px;
            max-height: 105px;
            object-fit: cover;
        }

        .photo-placeholder {
            width: 80px;
            height: 105px;
            border: 1px dashed #999;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8pt;
            color: #999;
        }

        /* Checkbox inline */
        .opt {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            margin-right: 10px;
            font-size: 8.5pt;
        }

        .opt .box {
            width: 11px;
            height: 11px;
            border: 1px solid #000;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 9pt;
            line-height: 1;
        }

        /* Experience table */
        .exp-table th {
            background: #e8e8e8;
            font-size: 8pt;
            text-align: center;
            white-space: nowrap;
        }

        .exp-table td {
            font-size: 8.5pt;
            min-height: 20px;
        }

        /* Commitment */
        .commitment {
            margin-top: 8px;
            font-size: 8.5pt;
            border: 1px solid #000;
            padding: 6px;
        }

        .commitment-zh {
            font-size: 7.5pt;
            color: #444;
        }

        /* Sign area */
        .sign-area {
            display: flex;
            justify-content: space-between;
            margin-top: 8px;
            font-size: 9pt;
        }

        /* Salary */
        .salary-area {
            margin-top: 6px;
            font-size: 9pt;
        }

        /* Print */
        @media print {
            .no-print {
                display: none !important;
            }

            .page {
                padding: 10mm 12mm;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        .print-btn {
            position: fixed;
            top: 16px;
            right: 16px;
            z-index: 999;
            background: #1a3a5c;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-family: sans-serif;
            font-weight: 700;
        }
    </style>
</head>

<body>

    <button class="print-btn no-print" onclick="window.print()">🖨️ In phiếu</button>

    <div class="page">
        {{-- Header --}}
        <div class="header-row" style="border:1.5px solid #000;margin-bottom:4px;">
            <div class="header-logo" style="width:70px;border-right:1px solid #000;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:6px;">

            </div>
            <div style="flex:1;padding:4px 10px;">
                <div style="font-size:7.5pt;text-align:center">CÔNG TY TNHH MAY MẶC VIỆT THIÊN 富华制衣产品有限公司</div>
                <div style="font-size:15pt;font-weight:900;text-align:center;letter-spacing:.05em">PHIẾU PHỎNG VẤN</div>
                <div style="font-size:10pt;text-align:center">应征登记表</div>
                <div style="font-size:6.5pt;margin-top:4px;color:#333">
                    <i>Chú ý: Công ty hoặc nhân viên tuyển dụng không được phép thu bất kỳ khoản chi phí nào của người ứng tuyển.</i><br>
                    <i>注意：本公司或其他人员不准许收取任何应聘者任何费用.</i>
                </div>
            </div>
            <div style="width:80px;border-left:1px solid #000;padding:4px;font-size:8pt;display:flex;flex-direction:column;gap:4px;justify-content:center;">
                <div style="display:flex;align-items:center;gap:4px;">
                    <span style="width:12px;height:12px;border:1px solid #000;display:inline-block;text-align:center;line-height:12px">□</span> Ngắn hạn
                </div>
                <div style="display:flex;align-items:center;gap:4px;">
                    <span style="width:12px;height:12px;border:1px solid #000;display:inline-block;text-align:center;line-height:12px;background:#000;color:white">✓</span> Chính thức
                </div>
            </div>
        </div>

        {{-- Personal Info Table --}}
        <table>
            <tr>
                <td class="photo-cell" rowspan="5" style="width:105px;text-align:center;vertical-align:middle;padding:4px;">
                    @if($candidate->photo_path)
                    <img src="{{ asset($candidate->photo_path) }}" alt="Ảnh" style="max-width: 200px; max-height: 200px; object-fit: cover; display: block; margin: 0 auto;">
                    @else
                    <div class="photo-placeholder" style="width:120px;height:120px;margin:0 auto;">Ảnh 3x4<br><span class="label-zh">照片</span></div>
                    @endif
                </td>
                <td class="label-cell" style="width:100px">Họ tên / 姓名</td>
                <td colspan="3" class="value-cell" style="font-weight:bold;font-size:11pt">{{ $candidate->full_name }}</td>
                <td class="label-cell" style="width:70px">Giới tính<br><span class="label-zh">性别</span></td>
                <td class="value-cell">
                    <span class="opt"><span class="box">{{ $candidate->gender === 'male' ? '✓' : '' }}</span> Nam 男</span>
                    <span class="opt"><span class="box">{{ $candidate->gender === 'female' ? '✓' : '' }}</span> Nữ 女</span>
                </td>
            </tr>
            <tr>
                <td class="label-cell">Ngày sinh<br><span class="label-zh">出生日期</span></td>
                <td class="value-cell">{{ $candidate->dob ? $candidate->dob->format('d/m/Y') : '' }}</td>
                <td class="label-cell">Số CCCD<br><span class="label-zh">身份证号码</span></td>
                <td colspan="3" class="value-cell">{{ $candidate->id_number }}</td>
            </tr>
            <tr>
                <td class="label-cell">Trình độ văn hóa<br><span class="label-zh">文化程度及专业</span></td>
                <td class="value-cell">{{ $candidate->education }}</td>
                <td class="label-cell">Thành thạo ngoại ngữ<br><span class="label-zh">语言能力</span></td>
                <td colspan="3" class="value-cell">{{ $candidate->language_skills }}</td>
            </tr>
            <tr>
                <td class="label-cell">Bộ phận ứng tuyển<br><span class="label-zh">申请部门</span></td>
                <td class="value-cell"><strong>{{ $candidate->department_applied ?: '—' }}</strong></td>
                <td class="label-cell">Vị trí ứng tuyển<br><span class="label-zh">招聘职位</span></td>
                <td class="value-cell"><strong>{{ $candidate->position_applied }}</strong></td>
                <td class="label-cell">Điện thoại<br><span class="label-zh">联系电话</span></td>
                <td class="value-cell">{{ $candidate->phone }}</td>
            </tr>
            <tr>
                <td class="label-cell">Địa chỉ thường trú<br><span class="label-zh">永久居住地址</span></td>
                <td colspan="6" class="value-cell">{{ $candidate->address }}</td>
            </tr>
            <tr>
                <td class="label-cell">STK Vietinbank<br><span class="label-zh">银行账户</span></td>
                <td colspan="6" class="value-cell">{{ $candidate->bank_account }}</td>
            </tr>

            {{-- Marital --}}
            <tr>
                <td class="label-cell">Tình trạng hôn nhân<br><span class="label-zh">婚姻状况</span></td>
                <td colspan="6">
                    <span class="opt"><span class="box">{{ $candidate->marital_status === 'married' ? '✓' : '' }}</span> Đã kết hôn 已婚</span>
                    <span class="opt"><span class="box">{{ $candidate->marital_status === 'single' ? '✓' : '' }}</span> Chưa kết hôn 未婚</span>
                    <span class="opt"><span class="box">{{ $candidate->marital_status === 'divorced' ? '✓' : '' }}</span> Ly hôn 离婚</span>
                </td>
            </tr>

            {{-- Children --}}
            <tr>
                <td class="label-cell">Số con<br><span class="label-zh">子女数量</span></td>
                <td colspan="6">
                    @php $children = array_filter($candidate->children_dob ?? []); @endphp
                    @foreach([0,1,2,3,4,5] as $i)
                    <span class="opt"><span class="box">{{ isset($children[$i]) && $children[$i] ? '✓' : '' }}</span> Năm sinh con {{ $i+1 }}: {{ $children[$i] ?? '________' }}</span>
                    @endforeach
                </td>
            </tr>

            {{-- Referral Source --}}
            @php
            $sources = $candidate->referral_source ?? [];
            $srcLabels = ['zalo'=>'Zalo','facebook'=>'Facebook','tiktok'=>'TikTok','web'=>'Web tuyển dụng','banner'=>'Bảng zôn/Băng rôn','internal'=>'Người trong công ty giới thiệu','phone'=>'Điện thoại','other'=>'Khác'];
            @endphp
            <tr>
                <td class="label-cell">Được biết về tin tuyển dụng ở đâu?<br><span class="label-zh">招聘信息获得途径</span></td>
                <td colspan="6" style="font-size:8.5pt">
                    @foreach($srcLabels as $key => $lbl)
                    <span class="opt"><span class="box">{{ in_array($key, $sources) ? '✓' : '' }}</span> {{ $lbl }}</span>
                    @endforeach
                </td>
            </tr>

            {{-- Referral Person --}}
            <tr>
                <td class="label-cell">Người giới thiệu đang làm tại Cty<br><span class="label-zh">公司内部人士介绍上班</span></td>
                <td colspan="2">Họ tên: {{ $candidate->referral_name }}</td>
                <td colspan="2">Bộ phận: {{ $candidate->referral_department }}</td>
                <td colspan="2">Quan hệ: {{ $candidate->referral_relation }}</td>
            </tr>

            {{-- Emergency Contact --}}
            <tr>
                <td class="label-cell" rowspan="2">Người liên hệ trong trường hợp khẩn cấp<br><span class="label-zh">紧急联系人</span></td>
                <td colspan="3">Họ tên: {{ $candidate->emergency_name }}</td>
                <td colspan="2">Quan hệ: {{ $candidate->emergency_relation }}</td>
                <td>ĐT: {{ $candidate->emergency_phone }}</td>
            </tr>
            <tr>
                <td colspan="6">Địa chỉ thường trú: {{ $candidate->emergency_address }}</td>
            </tr>
        </table>

        {{-- Work Experience --}}
        @php
        $exps = $candidate->work_experiences ?? [];
        $totalExpRows = max(4, count($exps));
        @endphp
        <table class="exp-table" style="margin-top:4px">
            <tr>
                <td class="label-cell" rowspan="{{ $totalExpRows + 1 }}" style="width:105px;text-align:center;font-weight:bold;vertical-align:middle;">
                    Kinh nghiệm làm việc<br><span class="label-zh">工作经历</span>
                </td>
                <th style="width:110px">Thời gian / 起止日期</th>
                <th>Tên công ty / 工作单位</th>
                <th style="width:90px">Chức vụ / 职位</th>
                <th style="width:90px">Lương/tháng / 薪资/月</th>
                <th style="width:130px">Nguyên nhân nghỉ việc / 离职原因</th>
            </tr>
            @for($i = 0; $i < $totalExpRows; $i++)
                @php $exp=$exps[$i] ?? null; @endphp
                <tr>
                <td style="height:22px;text-align:center">
                    {{ $exp ? (($exp['start_date'] ?? '') . ($exp['start_date'] && $exp['end_date'] ? ' → ' : '') . ($exp['end_date'] ?? '')) : '' }}
                </td>
                <td>{{ $exp['company'] ?? '' }}</td>
                <td style="text-align:center">{{ $exp['position'] ?? '' }}</td>
                <td style="text-align:center">
                    {{ $exp && isset($exp['salary']) && is_numeric($exp['salary']) ? number_format((float)$exp['salary']) : ($exp['salary'] ?? '') }}
                </td>
                <td>{{ $exp['reason_leaving'] ?? '' }}</td>
                </tr>
                @endfor
        </table>

        {{-- Commitment --}}
        <div class="commitment">
            <strong>Tôi xin cam kết những thông tin cung cấp ở trên là chính xác, nếu sai tôi xin chịu hoàn toàn trách nhiệm.</strong><br>
            <span class="commitment-zh">我承诺以上提供的信息是正确的，若有错误，我将承担全部责任.</span>
        </div>

        {{-- Sign & Salary --}}
        <table style="margin-top:4px;width:100%;">
            <tr>
                <td style="width:50%;padding:4px 8px;font-size:8.5pt;">
                    <strong>Mức lương mong muốn / 要求的工资：</strong> {{ $candidate->expected_salary }}
                </td>
                <td style="width:50%;padding:4px 8px;font-size:8.5pt;">
                    <strong>Người điền biểu / 填写人：</strong> {{ $candidate->full_name }} &nbsp;&nbsp;
                    <strong>Ngày / 日期：</strong> {{ $candidate->created_at->format('d/m/Y') }}
                </td>
            </tr>
        </table>

        {{-- HR Assessment --}}
        <table style="margin-top:4px;width:100%;">
            <tr>
                <td class="label-cell" style="width:105px;text-align:center;font-weight:bold;vertical-align:middle;">
                    Nhận xét của Nhân sự<br><span class="label-zh">人事部评价</span>
                </td>
                <td style="padding:5px 8px;font-size:8.5pt;vertical-align:top;">
                    <div style="min-height:28px;white-space:pre-line;">{{ $candidate->hr_notes ?: '................................................................................................................................................................' }}</div>
                    @if($candidate->hr_photo_path)
                    <div style="margin-top:4px;">
                        <img src="{{ asset($candidate->hr_photo_path) }}" alt="HR Photo" style="max-height:85px;max-width:180px;object-fit:cover;border:1px solid #999;border-radius:3px;">
                    </div>
                    @endif
                </td>
            </tr>
        </table>

        {{-- Department Head / Senior Manager Assessment --}}
        @php
        $reviewedManagers = $candidate->seniorManagers->filter(fn($m) => !empty($m->pivot->reviewed_at) || !empty($m->pivot->review_note) || !empty($m->pivot->review_result));
        @endphp

        @if($reviewedManagers->isNotEmpty())
        @foreach($reviewedManagers as $sm)
        <table style="margin-top:4px;width:100%;">
            <tr>
                <td class="label-cell" rowspan="3" style="width:105px;text-align:center;font-weight:bold;vertical-align:middle;">
                    Ý kiến của Chủ quản/Quản lý cấp cao<br><span class="label-zh">部门主管/高级经理意见</span>
                </td>
                <td colspan="5" style="padding:4px 8px;font-size:8.5pt;">
                    <strong>Kết quả / 结果:</strong>
                    <span class="opt" style="margin-left:10px;">
                        <span class="box">{{ $sm->pivot->review_result === 'approved' ? '✓' : '' }}</span> Đồng ý tuyển dụng / 同意录用
                    </span>
                    <span class="opt">
                        <span class="box">{{ $sm->pivot->review_result === 'rejected' ? '✓' : '' }}</span> Không tuyển / 不录用
                    </span>
                    <span class="opt">
                        <span class="box">{{ $sm->pivot->review_result === 'pending' || !$sm->pivot->review_result ? '✓' : '' }}</span>Chờ xem xét / 待审核
                    </span>
                </td>
            </tr>
            <tr>
                <td colspan="5" style="padding:5px 8px;font-size:8.5pt;min-height:30px;vertical-align:top;">
                    <strong>Nhận xét / 评语:</strong>
                    <div style="margin-top:2px;white-space:pre-line;">{{ $sm->pivot->review_note ?: '—' }}</div>
                    @if($sm->pivot->extra_note)
                    <div style="margin-top:3px;font-style:italic;color:#333;">Ghi chú thêm: {{ $sm->pivot->extra_note }}</div>
                    @endif
                </td>
            </tr>
            <tr style="font-size:8pt;">
                <td style="width:20%;padding:3px 5px;"><strong>Lương đề xuất:</strong><br>{{ $sm->pivot->proposed_salary ?: '—' }}</td>
                <td style="width:20%;padding:3px 5px;"><strong>Ngày bắt đầu:</strong><br>{{ $sm->pivot->start_date ? \Carbon\Carbon::parse($sm->pivot->start_date)->format('d/m/Y') : '—' }}</td>
                <td style="width:16%;padding:3px 5px;"><strong>Thử việc:</strong><br>{{ $sm->pivot->probation_period ?: '—' }}</td>
                <td style="width:22%;padding:3px 5px;"><strong>Bộ phận:</strong><br>{{ $sm->pivot->assigned_department ?: '—' }}</td>
                <td style="width:22%;padding:3px 5px;">
                    <strong>Chủ quản:</strong> {{ $sm->name }}<br>
                    <span style="font-size:7pt;color:#555;">{{ $sm->pivot->reviewed_at ? \Carbon\Carbon::parse($sm->pivot->reviewed_at)->format('d/m/Y H:i') : '' }}</span>
                </td>
            </tr>
        </table>
        @endforeach
        @else
        <table style="margin-top:4px;width:100%;">
            <tr>
                <td class="label-cell" rowspan="3" style="width:105px;text-align:center;font-weight:bold;vertical-align:middle;">
                    Ý kiến của Chủ quản<br><span class="label-zh">部门主管意见</span>
                </td>
                <td colspan="5" style="padding:4px 8px;font-size:8.5pt;">
                    <strong>Kết quả / 结果:</strong>
                    <span class="opt" style="margin-left:10px;"><span class="box"></span> Đồng ý tuyển dụng / 同意录用</span>
                    <span class="opt"><span class="box"></span> Không tuyển / 不录用</span>
                    <span class="opt"><span class="box"></span> Xem xét / 待定</span>
                </td>
            </tr>
            <tr>
                <td colspan="5" style="padding:5px 8px;font-size:8.5pt;height:32px;vertical-align:top;">
                    <strong>Nhận xét / 评语:</strong>
                </td>
            </tr>
            <tr style="font-size:8pt;">
                <td style="width:20%;padding:3px 5px;"><strong>Lương đề xuất:</strong><br>...................</td>
                <td style="width:20%;padding:3px 5px;"><strong>Ngày bắt đầu:</strong><br>...................</td>
                <td style="width:16%;padding:3px 5px;"><strong>Thử việc:</strong><br>...........</td>
                <td style="width:22%;padding:3px 5px;"><strong>Bộ phận:</strong><br>...................</td>
                <td style="width:22%;padding:3px 5px;"><strong>Chữ ký chủ quản:</strong></td>
            </tr>
        </table>
        @endif

        <div style="font-size:7pt;text-align:right;margin-top:6px;color:#777">VT-B8NS/24-015</div>
    </div>

</body>

</html>