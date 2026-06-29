<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ __('messages.candidate_form_title') }} — VIVA</title>
    <meta name="description" content="{{ __('messages.candidate_form_title') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand: #1a3a5c;
            --brand-light: #e8f0fe;
            --accent: #d4362a;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f0f4ff 0%, #fafafa 100%);
            min-height: 100vh;
        }

        .form-header {
            background: linear-gradient(135deg, var(--brand) 0%, #2563eb 100%);
            color: white;
            padding: 2rem;
            border-radius: 1.5rem 1.5rem 0 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .form-header::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 200px; height: 200px;
            background: rgba(255,255,255,0.06);
            border-radius: 50%;
        }
        .form-header h1 { font-size: 1.5rem; font-weight: 800; letter-spacing: -0.02em; margin-bottom: .25rem; }
        .form-header .subtitle { font-size: 1rem; opacity: .8; font-style: italic; }
        .form-header .company { font-size: .85rem; opacity: .7; margin-bottom: .75rem; }
        .viva-logo { display: inline-block; background: white; color: var(--brand); font-weight: 900; font-size: 1.4rem; padding: .3rem .9rem; border-radius: .5rem; margin-bottom: .8rem; letter-spacing: .05em; }

        .form-wrapper {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 20px 60px rgba(0,0,0,.1);
            overflow: hidden;
            max-width: 860px;
            margin: 2rem auto;
        }

        .form-body { padding: 2rem; }

        .section-title {
            display: flex; align-items: center; gap: .5rem;
            font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em;
            color: var(--brand); background: var(--brand-light);
            padding: .5rem 1rem; border-radius: .5rem; margin: 1.5rem 0 1rem;
        }
        .section-title svg { flex-shrink: 0; }

        .form-label { font-size: .82rem; font-weight: 600; color: #374151; margin-bottom: .3rem; }
        .form-control, .form-select {
            border: 1.5px solid #e5e7eb; border-radius: .6rem;
            font-size: .9rem; padding: .5rem .75rem;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.12);
        }

        /* Photo upload */
        .photo-upload-area {
            border: 2px dashed #d1d5db; border-radius: .75rem;
            padding: 1rem; text-align: center; cursor: pointer;
            transition: border-color .2s; min-height: 120px;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
        }
        .photo-upload-area:hover { border-color: #2563eb; }
        .photo-preview { max-height: 120px; max-width: 100%; border-radius: .5rem; object-fit: cover; }

        /* Checkbox / Radio cards */
        .check-card {
            display: flex; align-items: center; gap: .5rem;
            padding: .5rem .75rem; border: 1.5px solid #e5e7eb; border-radius: .6rem;
            cursor: pointer; transition: all .2s; font-size: .85rem; white-space: nowrap;
        }
        .check-card input { accent-color: var(--brand); flex-shrink: 0; width: 16px; height: 16px; }
        .check-card:has(input:checked) { border-color: var(--brand); background: var(--brand-light); }

        /* Work experience table */
        .exp-row { background: #f9fafb; border-radius: .5rem; padding: .75rem; margin-bottom: .5rem; position: relative; }
        .exp-row .remove-exp { position: absolute; top: .5rem; right: .5rem; cursor: pointer; color: #ef4444; background: none; border: none; padding: 0; }

        .btn-add-row {
            border: 1.5px dashed #2563eb; border-radius: .6rem; color: #2563eb;
            background: transparent; padding: .4rem 1rem; font-size: .85rem; font-weight: 600;
            transition: all .2s; display: flex; align-items: center; gap: .4rem;
        }
        .btn-add-row:hover { background: var(--brand-light); }

        .btn-submit {
            background: linear-gradient(135deg, var(--brand) 0%, #2563eb 100%);
            color: white; border: none; border-radius: .75rem; padding: .85rem 2rem;
            font-weight: 700; font-size: 1rem; transition: all .2s; width: 100%;
        }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(37,99,235,.3); }

        .notice {
            background: #fff7ed; border: 1px solid #fed7aa; border-radius: .6rem;
            font-size: .78rem; color: #92400e; padding: .6rem .9rem;
        }

        @media (max-width: 576px) {
            .form-body { padding: 1rem; }
            .form-header { padding: 1.5rem 1rem; }
        }
    </style>
</head>
<body>

<div class="form-wrapper">
    {{-- Header --}}
    <div class="form-header">
        <div class="viva-logo">VIVA</div>
        <div class="company">CÔNG TY TNHH MAY MẶC VIỆT THIÊN 富华制衣产品有限公司</div>
        <h1>{{ __('messages.candidate_form_title') }}</h1>
        <div class="subtitle">应征登记表</div>
    </div>

    <div class="form-body">

        @if(session('success'))
        <div class="alert alert-success rounded-3 mb-4 d-flex align-items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger rounded-3 mb-4">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li style="font-size:.85rem">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="notice mb-3">
            ⚠️ {{ __('messages.candidate_form_notice') }}
        </div>

        <form method="POST"
              action="{{ isset($isAdmin) && $isAdmin ? route('candidates.store') : route('candidates.store_public') }}"
              enctype="multipart/form-data" id="candidateForm">
            @csrf

            {{-- === THÔNG TIN CÁ NHÂN === --}}
            <div class="section-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                {{ __('messages.candidate_personal_info') }}
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">{{ __('messages.full_name') }} / 姓名 <span class="text-danger">*</span></label>
                    <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('messages.gender') }} / 性别 <span class="text-danger">*</span></label>
                    <div class="d-flex gap-2 mt-1">
                        <label class="check-card flex-grow-1">
                            <input type="radio" name="gender" value="male" {{ old('gender','male') === 'male' ? 'checked' : '' }} required>
                            {{ __('messages.gender_male') }} 男
                        </label>
                        <label class="check-card flex-grow-1">
                            <input type="radio" name="gender" value="female" {{ old('gender') === 'female' ? 'checked' : '' }}>
                            {{ __('messages.gender_female') }} 女
                        </label>
                    </div>
                </div>
                <div class="col-md-3">
                    {{-- Photo --}}
                    <label class="form-label">{{ __('messages.candidate_photo') }} / 照片</label>
                    <div class="photo-upload-area" id="photoArea" onclick="document.getElementById('photoInput').click()">
                        <img id="photoPreview" class="photo-preview d-none" src="" alt="preview">
                        <div id="photoPlaceholder">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            <div style="font-size:.75rem;color:#9ca3af;margin-top:.4rem">{{ __('messages.click_to_upload') }}</div>
                        </div>
                    </div>
                    <input type="file" id="photoInput" name="photo" accept="image/*" class="d-none">
                </div>

                <div class="col-md-4">
                    <label class="form-label">{{ __('messages.dob') }} / 出生日期</label>
                    <input type="date" name="dob" class="form-control" value="{{ old('dob') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('messages.id_number') }} / 身份证号码</label>
                    <input type="text" name="id_number" class="form-control" value="{{ old('id_number') }}" placeholder="012345678910">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('messages.phone') }} / 联系电话 <span class="text-danger">*</span></label>
                    <input type="tel" name="phone" class="form-control" value="{{ old('phone') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">{{ __('messages.education') }} / 文化程度</label>
                    <input type="text" name="education" class="form-control" value="{{ old('education') }}" placeholder="{{ __('messages.education_placeholder') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('messages.language_skills') }} / 语言能力</label>
                    <input type="text" name="language_skills" class="form-control" value="{{ old('language_skills') }}" placeholder="{{ __('messages.language_skills_placeholder') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">{{ __('messages.position_applied') }} / 招聘职位 <span class="text-danger">*</span></label>
                    <input type="text" name="position_applied" class="form-control" value="{{ old('position_applied') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('messages.bank_account') }} / 银行账户 (Vietinbank)</label>
                    <input type="text" name="bank_account" class="form-control" value="{{ old('bank_account') }}">
                </div>

                <div class="col-12">
                    <label class="form-label">{{ __('messages.address') }} / 家庭地址</label>
                    <input type="text" name="address" class="form-control" value="{{ old('address') }}">
                </div>
            </div>

            {{-- === HÔN NHÂN === --}}
            <div class="section-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                {{ __('messages.marital_status') }} / 婚姻状况
            </div>

            <div class="d-flex flex-wrap gap-2 mb-3">
                <label class="check-card">
                    <input type="radio" name="marital_status" value="married" {{ old('marital_status') === 'married' ? 'checked' : '' }}>
                    {{ __('messages.marital_married') }} 已婚
                </label>
                <label class="check-card">
                    <input type="radio" name="marital_status" value="single" {{ old('marital_status','single') === 'single' ? 'checked' : '' }}>
                    {{ __('messages.marital_single') }} 未婚
                </label>
                <label class="check-card">
                    <input type="radio" name="marital_status" value="divorced" {{ old('marital_status') === 'divorced' ? 'checked' : '' }}>
                    {{ __('messages.marital_divorced') }} 离婚
                </label>
            </div>

            <label class="form-label">{{ __('messages.children_dob_label') }} / 子女出生年份</label>
            <div class="row g-2">
                @for($i = 0; $i < 3; $i++)
                <div class="col-md-4">
                    <input type="text" name="children_dob[{{ $i }}]"
                           class="form-control"
                           placeholder="{{ __('messages.child_n', ['n' => $i+1]) }}: {{ __('messages.birth_year') }}"
                           value="{{ old("children_dob.$i") }}"
                           maxlength="4">
                </div>
                @endfor
            </div>

            {{-- === NGUỒN BIẾT ĐẾN === --}}
            <div class="section-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                {{ __('messages.referral_source_label') }} / 招聘信息获得途径
            </div>

            @php
                $sources = [
                    'zalo'     => 'Zalo',
                    'facebook' => 'Facebook',
                    'web'      => __('messages.ref_web'),
                    'banner'   => __('messages.ref_banner'),
                    'internal' => __('messages.ref_internal'),
                    'phone'    => __('messages.ref_phone'),
                ];
                $oldSources = old('referral_source', []);
            @endphp

            <div class="d-flex flex-wrap gap-2 mb-3">
                @foreach($sources as $key => $label)
                <label class="check-card">
                    <input type="checkbox" name="referral_source[]" value="{{ $key }}"
                           {{ in_array($key, $oldSources) ? 'checked' : '' }}>
                    {{ $label }}
                </label>
                @endforeach
            </div>

            {{-- === NGƯỜI GIỚI THIỆU === --}}
            <div class="section-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m9 11 3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                {{ __('messages.referral_info') }} / 介绍人信息
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">{{ __('messages.full_name') }} / 姓名</label>
                    <input type="text" name="referral_name" class="form-control" value="{{ old('referral_name') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('messages.department') }} / 工作部门</label>
                    <input type="text" name="referral_department" class="form-control" value="{{ old('referral_department') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('messages.relation') }} / 关系</label>
                    <input type="text" name="referral_relation" class="form-control" value="{{ old('referral_relation') }}">
                </div>
            </div>

            {{-- === LIÊN HỆ KHẨN CẤP === --}}
            <div class="section-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.93 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.77 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 17z"/></svg>
                {{ __('messages.emergency_contact') }} / 紧急联系人
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">{{ __('messages.full_name') }} / 姓名</label>
                    <input type="text" name="emergency_name" class="form-control" value="{{ old('emergency_name') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('messages.relation') }} / 关系</label>
                    <input type="text" name="emergency_relation" class="form-control" value="{{ old('emergency_relation') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('messages.phone') }} / 电话</label>
                    <input type="tel" name="emergency_phone" class="form-control" value="{{ old('emergency_phone') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">{{ __('messages.address') }} / 地址</label>
                    <input type="text" name="emergency_address" class="form-control" value="{{ old('emergency_address') }}">
                </div>
            </div>

            {{-- === KINH NGHIỆM LÀM VIỆC === --}}
            <div class="section-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                {{ __('messages.work_experience') }} / 工作经历
            </div>

            <div id="expContainer">
                <div class="exp-row" data-index="0">
                    <button type="button" class="remove-exp" onclick="removeExp(this)" title="Xóa">✕</button>
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:.75rem">{{ __('messages.work_start') }} / 开始日期</label>
                            <input type="text" name="work_experiences[0][start_date]" class="form-control form-control-sm" placeholder="MM/YYYY">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:.75rem">{{ __('messages.work_end') }} / 结束日期</label>
                            <input type="text" name="work_experiences[0][end_date]" class="form-control form-control-sm" placeholder="MM/YYYY">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:.75rem">{{ __('messages.company') }} / 公司</label>
                            <input type="text" name="work_experiences[0][company]" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" style="font-size:.75rem">{{ __('messages.position') }} / 职位</label>
                            <input type="text" name="work_experiences[0][position]" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:.75rem">{{ __('messages.salary_month') }} / 薪资/月</label>
                            <input type="text" name="work_experiences[0][salary]" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label" style="font-size:.75rem">{{ __('messages.reason_leaving') }} / 离职原因</label>
                            <input type="text" name="work_experiences[0][reason_leaving]" class="form-control form-control-sm">
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" class="btn-add-row mt-2 mb-3" onclick="addExpRow()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                {{ __('messages.add_work_experience') }}
            </button>

            {{-- === MONG MUỐN === --}}
            <div class="section-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                {{ __('messages.expected_salary') }} / 要求的工资
            </div>

            <div class="col-md-5">
                <input type="text" name="expected_salary" class="form-control" value="{{ old('expected_salary') }}" placeholder="VD: 8,000,000">
            </div>

            {{-- === CAM KẾT === --}}
            <div class="mt-4 p-3 bg-light rounded-3" style="font-size:.82rem;color:#374151;font-style:italic">
                <strong>{{ __('messages.candidate_commitment') }}</strong><br>
                <span style="color:#6b7280">我承诺以上提供的信息是正确的，若有错误，我将承担全部责任.</span>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn-submit">
                    {{ __('messages.candidate_submit_btn') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Photo preview
    document.getElementById('photoInput').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('photoPreview').src = e.target.result;
            document.getElementById('photoPreview').classList.remove('d-none');
            document.getElementById('photoPlaceholder').classList.add('d-none');
        };
        reader.readAsDataURL(file);
    });

    // Work experience rows
    let expIndex = 1;

    function addExpRow() {
        const container = document.getElementById('expContainer');
        const template = `
        <div class="exp-row" data-index="${expIndex}">
            <button type="button" class="remove-exp" onclick="removeExp(this)" title="Xóa">✕</button>
            <div class="row g-2">
                <div class="col-md-3"><label class="form-label" style="font-size:.75rem">{{ __('messages.work_start') }}</label>
                    <input type="text" name="work_experiences[${expIndex}][start_date]" class="form-control form-control-sm" placeholder="MM/YYYY"></div>
                <div class="col-md-3"><label class="form-label" style="font-size:.75rem">{{ __('messages.work_end') }}</label>
                    <input type="text" name="work_experiences[${expIndex}][end_date]" class="form-control form-control-sm" placeholder="MM/YYYY"></div>
                <div class="col-md-6"><label class="form-label" style="font-size:.75rem">{{ __('messages.company') }}</label>
                    <input type="text" name="work_experiences[${expIndex}][company]" class="form-control form-control-sm"></div>
                <div class="col-md-4"><label class="form-label" style="font-size:.75rem">{{ __('messages.position') }}</label>
                    <input type="text" name="work_experiences[${expIndex}][position]" class="form-control form-control-sm"></div>
                <div class="col-md-3"><label class="form-label" style="font-size:.75rem">{{ __('messages.salary_month') }}</label>
                    <input type="text" name="work_experiences[${expIndex}][salary]" class="form-control form-control-sm"></div>
                <div class="col-md-5"><label class="form-label" style="font-size:.75rem">{{ __('messages.reason_leaving') }}</label>
                    <input type="text" name="work_experiences[${expIndex}][reason_leaving]" class="form-control form-control-sm"></div>
            </div>
        </div>`;
        container.insertAdjacentHTML('beforeend', template);
        expIndex++;
    }

    function removeExp(btn) {
        const row = btn.closest('.exp-row');
        const container = document.getElementById('expContainer');
        if (container.querySelectorAll('.exp-row').length > 1) {
            row.remove();
        }
    }
</script>
</body>
</html>
