<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>{{ __('messages.candidate_form_title') }} — {{ $candidate->full_name }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Times New Roman', serif; font-size: 11pt; color: #000; background: white; }

        .page { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 12mm 14mm; }

        /* Header */
        .form-header { text-align: center; border: 1.5px solid #000; margin-bottom: 4px; padding: 6px 4px; }
        .header-row { display: flex; align-items: stretch; }
        .header-logo { width: 70px; border-right: 1px solid #000; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 4px; }
        .logo-box { font-size: 18pt; font-weight: 900; letter-spacing: .05em; border: 2px solid #000; padding: 2px 8px; }
        .logo-sub { font-size: 6pt; margin-top: 2px; text-align: center; }
        .header-main { flex: 1; padding: 4px 8px; }
        .company-name { font-size: 7.5pt; }
        .form-title { font-size: 16pt; font-weight: 900; letter-spacing: .05em; margin: 2px 0; }
        .form-title-zh { font-size: 11pt; }
        .form-notice { font-size: 7pt; color: #333; margin-top: 4px; text-align: left; }
        .header-type { width: 80px; border-left: 1px solid #000; padding: 4px; font-size: 8pt; display: flex; flex-direction: column; gap: 4px; justify-content: center; }
        .type-item { display: flex; align-items: center; gap: 4px; }
        .checkbox { width: 12px; height: 12px; border: 1px solid #000; display: inline-flex; align-items: center; justify-content: center; font-size: 10pt; }

        /* Table cells */
        table { border-collapse: collapse; width: 100%; font-size: 9.5pt; }
        td, th { border: 1px solid #000; padding: 3px 5px; vertical-align: middle; }
        .label-cell { background: #f5f5f5; font-size: 8.5pt; white-space: nowrap; width: 90px; }
        .label-zh { font-size: 7pt; display: block; color: #555; }
        .value-cell { min-height: 22px; }
        .value-cell.tall { min-height: 18px; }
        .section-header { background: #e8e8e8; font-weight: bold; font-size: 9pt; }

        /* Photo */
        .photo-cell { width: 85px; border-left: 1px solid #000; text-align: center; vertical-align: middle; }
        .photo-cell img { max-width: 80px; max-height: 105px; object-fit: cover; }
        .photo-placeholder { width: 80px; height: 105px; border: 1px dashed #999; display: flex; align-items: center; justify-content: center; font-size: 8pt; color: #999; }

        /* Checkbox inline */
        .opt { display: inline-flex; align-items: center; gap: 3px; margin-right: 10px; font-size: 8.5pt; }
        .opt .box { width: 11px; height: 11px; border: 1px solid #000; display: inline-flex; align-items: center; justify-content: center; font-size: 9pt; line-height: 1; }

        /* Experience table */
        .exp-table th { background: #e8e8e8; font-size: 8pt; text-align: center; white-space: nowrap; }
        .exp-table td { font-size: 8.5pt; min-height: 20px; }

        /* Commitment */
        .commitment { margin-top: 8px; font-size: 8.5pt; border: 1px solid #000; padding: 6px; }
        .commitment-zh { font-size: 7.5pt; color: #444; }

        /* Sign area */
        .sign-area { display: flex; justify-content: space-between; margin-top: 8px; font-size: 9pt; }

        /* Salary */
        .salary-area { margin-top: 6px; font-size: 9pt; }

        /* Print */
        @media print {
            .no-print { display: none !important; }
            .page { padding: 10mm 12mm; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }

        .print-btn {
            position: fixed; top: 16px; right: 16px; z-index: 999;
            background: #1a3a5c; color: white; border: none; padding: 10px 20px;
            border-radius: 8px; cursor: pointer; font-family: sans-serif; font-weight: 700;
        }
    </style>
</head>
<body>

<button class="print-btn no-print" onclick="window.print()">🖨️ In phiếu</button>

<div class="page">
    {{-- Header --}}
    <div class="header-row" style="border:1.5px solid #000;margin-bottom:4px;">
        <div class="header-logo" style="width:70px;border-right:1px solid #000;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:6px;">
            <div class="logo-box">VIVA</div>
            <div class="logo-sub">富华制衣产品有限公司</div>
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
                <span style="width:12px;height:12px;border:1px solid #000;display:inline-block;text-align:center;line-height:12px">□</span> Ngân hàn
            </div>
            <div style="display:flex;align-items:center;gap:4px;">
                <span style="width:12px;height:12px;border:1px solid #000;display:inline-block;text-align:center;line-height:12px;background:#000;color:white">✓</span> Chính thức
            </div>
        </div>
    </div>

    {{-- Personal Info Table --}}
    <table>
        <tr>
            <td class="label-cell" rowspan="5" style="width:90px">Ảnh<br><span class="label-zh">照片</span></td>
            <td class="photo-cell" rowspan="5" style="width:90px;text-align:center;vertical-align:middle;">
                @if($candidate->photo_path)
                    <img src="{{ public_path($candidate->photo_path) }}" alt="Ảnh">
                @else
                    <div class="photo-placeholder">Ảnh / 照片</div>
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
            <td class="label-cell">Vị trí ứng tuyển<br><span class="label-zh">招聘职位</span></td>
            <td class="value-cell"><strong>{{ $candidate->position_applied }}</strong></td>
            <td class="label-cell">Điện thoại liên hệ<br><span class="label-zh">联系电话</span></td>
            <td colspan="3" class="value-cell">{{ $candidate->phone }}</td>
        </tr>
        <tr>
            <td class="label-cell">Địa chỉ nhà<br><span class="label-zh">家庭地址</span></td>
            <td colspan="6" class="value-cell">{{ $candidate->address }}</td>
        </tr>
        <tr>
            <td class="label-cell">STK Vietinbank<br><span class="label-zh">银行账户</span></td>
            <td colspan="8" class="value-cell">{{ $candidate->bank_account }}</td>
        </tr>

        {{-- Marital --}}
        <tr>
            <td class="label-cell">Tình trạng hôn nhân<br><span class="label-zh">婚姻状况</span></td>
            <td colspan="8">
                <span class="opt"><span class="box">{{ $candidate->marital_status === 'married' ? '✓' : '' }}</span> Đã kết hôn 已婚</span>
                <span class="opt"><span class="box">{{ $candidate->marital_status === 'single' ? '✓' : '' }}</span> Chưa kết hôn 未婚</span>
                <span class="opt"><span class="box">{{ $candidate->marital_status === 'divorced' ? '✓' : '' }}</span> Ly hôn 离婚</span>
            </td>
        </tr>

        {{-- Children --}}
        <tr>
            <td class="label-cell">Số con<br><span class="label-zh">子女数量</span></td>
            <td colspan="8">
                @php $children = array_filter($candidate->children_dob ?? []); @endphp
                @foreach([0,1,2] as $i)
                <span class="opt"><span class="box">{{ isset($children[$i]) && $children[$i] ? '✓' : '' }}</span> Năm sinh con {{ $i+1 }}: {{ $children[$i] ?? '________' }}</span>
                @endforeach
            </td>
        </tr>

        {{-- Referral Source --}}
        @php
            $sources = $candidate->referral_source ?? [];
            $srcLabels = ['zalo'=>'Zalo','facebook'=>'Facebook','web'=>'Web tuyển dụng','banner'=>'Bảng zôn/Băng rôn','internal'=>'Người trong công ty giới thiệu','phone'=>'Điện thoại'];
        @endphp
        <tr>
            <td class="label-cell">Được biết về tin tuyển dụng ở đâu?<br><span class="label-zh">招聘信息获得途径</span></td>
            <td colspan="8" style="font-size:8.5pt">
                @foreach($srcLabels as $key => $lbl)
                <span class="opt"><span class="box">{{ in_array($key, $sources) ? '✓' : '' }}</span> {{ $lbl }}</span>
                @endforeach
            </td>
        </tr>

        {{-- Referral Person --}}
        <tr>
            <td class="label-cell">Người giới thiệu đang làm tại Cty<br><span class="label-zh">公司内部人士介绍上班</span></td>
            <td>Họ tên: {{ $candidate->referral_name }}</td>
            <td colspan="3">Bộ phận: {{ $candidate->referral_department }}</td>
            <td colspan="4">Quan hệ: {{ $candidate->referral_relation }}</td>
        </tr>

        {{-- Emergency Contact --}}
        <tr>
            <td class="label-cell" rowspan="2">Người liên hệ trong trường hợp khẩn cấp<br><span class="label-zh">紧急联系人</span></td>
            <td colspan="5">Họ tên: {{ $candidate->emergency_name }}</td>
            <td colspan="2">Quan hệ: {{ $candidate->emergency_relation }}</td>
            <td>ĐT: {{ $candidate->emergency_phone }}</td>
        </tr>
        <tr>
            <td colspan="8">Địa chỉ: {{ $candidate->emergency_address }}</td>
        </tr>
    </table>

    {{-- Work Experience --}}
    <table class="exp-table" style="margin-top:4px">
        <tr>
            <th rowspan="2" style="width:80px">Kinh nghiệm làm việc<br><span style="font-weight:normal">工作经历</span></th>
            <th style="width:100px">Thời gian / 起止日期</th>
            <th>Tên công ty / 工作单位</th>
            <th style="width:90px">Chức vụ / 职位</th>
            <th style="width:80px">Lương/tháng / 薪资/月</th>
            <th>Nguyên nhân nghỉ việc / 离职原因</th>
        </tr>
        @forelse($candidate->work_experiences ?? [] as $exp)
        <tr>
            <td style="text-align:center">{{ ($exp['start_date'] ?? '') . ' → ' . ($exp['end_date'] ?? '') }}</td>
            <td>{{ $exp['company'] ?? '' }}</td>
            <td>{{ $exp['position'] ?? '' }}</td>
            <td style="text-align:center">{{ $exp['salary'] ?? '' }}</td>
            <td>{{ $exp['reason_leaving'] ?? '' }}</td>
        </tr>
        @empty
        @for($i = 0; $i < 3; $i++)
        <tr>
            <td style="height:22px"></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        @endfor
        @endforelse
        {{-- Fill blank rows to total 4 --}}
        @for($i = count($candidate->work_experiences ?? []); $i < 4; $i++)
        <tr><td style="height:20px"></td><td></td><td></td><td></td><td></td></tr>
        @endfor
    </table>

    {{-- Commitment --}}
    <div class="commitment">
        <strong>Tôi xin cam kết những thông tin cung cấp ở trên là chính xác, nếu sai tôi xin chịu hoàn toàn trách nhiệm.</strong><br>
        <span class="commitment-zh">我承诺以上提供的信息是正确的，若有错误，我将承担全部责任.</span>
    </div>

    {{-- Sign --}}
    <div class="sign-area">
        <div>
            Người điền biểu/填写人：<strong>{{ $candidate->full_name }}</strong>
            &nbsp;&nbsp;&nbsp;
            Thời gian điền phiếu/填写日期：<strong>{{ $candidate->created_at->format('d/m/Y') }}</strong>
        </div>
    </div>

    <div class="salary-area">
        Mức lương mong muốn/要求的工资：<strong>{{ $candidate->expected_salary }}</strong>
    </div>

    <div style="font-size:7pt;text-align:right;margin-top:8px;color:#777">VT-B8NS/24-015</div>
</div>

</body>
</html>
