@extends('layouts.app-simple', ['maxWidth' => '900px'])
@section('title', $candidate->full_name . ' — ' . __('messages.candidates'))

@section('content')
<style>
    .detail-card { background: white; border-radius: 1.25rem; box-shadow: 0 4px 20px rgba(0,0,0,.08); overflow: hidden; }
    .detail-header { background: linear-gradient(135deg, #1a3a5c 0%, #2563eb 100%); color: white; padding: 2rem; }
    .info-row { display: grid; grid-template-columns: 160px 1fr; gap: .25rem 1rem; padding: .6rem 0; border-bottom: 1px solid #f1f5f9; font-size: .9rem; }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: #6b7280; font-weight: 600; font-size: .78rem; text-transform: uppercase; letter-spacing: .05em; align-self: center; }
    .info-value { color: #111827; font-weight: 500; }
    .section-badge { display: inline-flex; align-items: center; gap: .4rem; background: #eff6ff; color: #1d4ed8; border-radius: .5rem; padding: .35rem .75rem; font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; margin: 1.25rem 0 .75rem; }
    .exp-item { background: #f9fafb; border-radius: .6rem; padding: .75rem 1rem; margin-bottom: .4rem; border-left: 3px solid #2563eb; }
    .exp-item .exp-company { font-weight: 700; color: #111827; }
    .exp-item .exp-meta { font-size: .8rem; color: #6b7280; }
    .tag { display: inline-block; background: #f1f5f9; border-radius: .4rem; padding: .15rem .5rem; font-size: .78rem; color: #374151; margin: .15rem; }
    .photo-thumb { width: 100px; height: 130px; object-fit: cover; border-radius: .5rem; border: 2px solid #e5e7eb; }
</style>

<a href="{{ route('candidates.index') }}" class="d-inline-flex align-items-center gap-2 text-muted fw-600 text-decoration-none mb-4" style="font-weight:600">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m15 18-6-6 6-6"/></svg>
    {{ __('messages.back') }}
</a>

@if(session('success'))
<div class="alert alert-success rounded-3 mb-4">{{ session('success') }}</div>
@endif

<div class="detail-card">
    {{-- Header --}}
    <div class="detail-header d-flex align-items-center gap-4">
        @if($candidate->photo_path)
            <img src="/{{ $candidate->photo_path }}" alt="{{ $candidate->full_name }}" class="photo-thumb" style="border-color: rgba(255,255,255,.4)">
        @else
            <div style="width:100px;height:130px;background:rgba(255,255,255,.15);border-radius:.5rem;display:flex;align-items:center;justify-content:center;font-size:3rem;color:rgba(255,255,255,.7);">
                {{ mb_substr($candidate->full_name, 0, 1) }}
            </div>
        @endif
        <div>
            <div style="font-size:.8rem;opacity:.7;text-transform:uppercase;letter-spacing:.1em" class="mb-1">{{ __('messages.candidate_form_title') }}</div>
            <h2 class="fw-bold mb-1" style="font-size:1.6rem">{{ $candidate->full_name }}</h2>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span style="background:rgba(255,255,255,.2);border-radius:.4rem;padding:.2rem .6rem;font-size:.82rem">
                    {{ $candidate->gender === 'male' ? '♂ ' . __('messages.gender_male') : '♀ ' . __('messages.gender_female') }}
                </span>
                <span style="background:rgba(255,255,255,.2);border-radius:.4rem;padding:.2rem .6rem;font-size:.82rem">
                    {{ $candidate->position_applied }}
                </span>
                <span style="opacity:.7;font-size:.82rem">{{ $candidate->created_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>
        <div class="ms-auto d-flex flex-column gap-2">
            <a href="{{ route('candidates.print', $candidate->id) }}" target="_blank" class="btn btn-light rounded-3 fw-bold d-flex align-items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                {{ __('messages.print') }}
            </a>
            @if(auth()->user()->isAdminUser())
            <form action="{{ route('candidates.destroy', $candidate->id) }}" method="POST"
                  onsubmit="return confirm('{{ __('messages.candidate_delete_confirm') }}')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger rounded-3 fw-bold w-100 d-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                    {{ __('messages.delete') }}
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="p-4">
        {{-- Personal Info --}}
        <div class="section-badge">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            {{ __('messages.candidate_personal_info') }}
        </div>

        <div class="info-row"><div class="info-label">{{ __('messages.dob') }}</div><div class="info-value">{{ $candidate->dob ? $candidate->dob->format('d/m/Y') : '—' }}</div></div>
        <div class="info-row"><div class="info-label">{{ __('messages.id_number') }}</div><div class="info-value">{{ $candidate->id_number ?: '—' }}</div></div>
        <div class="info-row"><div class="info-label">{{ __('messages.phone') }}</div><div class="info-value"><a href="tel:{{ $candidate->phone }}">{{ $candidate->phone }}</a></div></div>
        <div class="info-row"><div class="info-label">{{ __('messages.address') }}</div><div class="info-value">{{ $candidate->address ?: '—' }}</div></div>
        <div class="info-row"><div class="info-label">{{ __('messages.education') }}</div><div class="info-value">{{ $candidate->education ?: '—' }}</div></div>
        <div class="info-row"><div class="info-label">{{ __('messages.language_skills') }}</div><div class="info-value">{{ $candidate->language_skills ?: '—' }}</div></div>
        <div class="info-row"><div class="info-label">{{ __('messages.bank_account') }}</div><div class="info-value">{{ $candidate->bank_account ?: '—' }}</div></div>

        {{-- Marital --}}
        <div class="section-badge">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            {{ __('messages.marital_status') }}
        </div>
        <div class="info-row"><div class="info-label">{{ __('messages.marital_status') }}</div><div class="info-value">{{ $candidate->marital_label }}</div></div>
        @if(!empty($candidate->children_dob))
        <div class="info-row">
            <div class="info-label">{{ __('messages.children_dob_label') }}</div>
            <div class="info-value">
                @foreach(array_filter($candidate->children_dob) as $i => $yr)
                <span class="tag">{{ __('messages.child_n', ['n' => $i+1]) }}: {{ $yr }}</span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Referral --}}
        <div class="section-badge">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            {{ __('messages.referral_source_label') }}
        </div>
        @php
            $sourceLabels = [
                'zalo'     => 'Zalo',
                'facebook' => 'Facebook',
                'web'      => __('messages.ref_web'),
                'banner'   => __('messages.ref_banner'),
                'internal' => __('messages.ref_internal'),
                'phone'    => __('messages.ref_phone'),
            ];
        @endphp
        @if(!empty($candidate->referral_source))
        <div class="info-row">
            <div class="info-label">{{ __('messages.referral_source_label') }}</div>
            <div class="info-value">
                @foreach($candidate->referral_source as $src)
                <span class="tag">{{ $sourceLabels[$src] ?? $src }}</span>
                @endforeach
            </div>
        </div>
        @endif
        @if($candidate->referral_name)
        <div class="info-row"><div class="info-label">{{ __('messages.full_name') }}</div><div class="info-value">{{ $candidate->referral_name }}</div></div>
        <div class="info-row"><div class="info-label">{{ __('messages.department') }}</div><div class="info-value">{{ $candidate->referral_department ?: '—' }}</div></div>
        <div class="info-row"><div class="info-label">{{ __('messages.relation') }}</div><div class="info-value">{{ $candidate->referral_relation ?: '—' }}</div></div>
        @endif

        {{-- Emergency --}}
        <div class="section-badge">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.93 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.77 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 17z"/></svg>
            {{ __('messages.emergency_contact') }}
        </div>
        <div class="info-row"><div class="info-label">{{ __('messages.full_name') }}</div><div class="info-value">{{ $candidate->emergency_name ?: '—' }}</div></div>
        <div class="info-row"><div class="info-label">{{ __('messages.phone') }}</div><div class="info-value">{{ $candidate->emergency_phone ?: '—' }}</div></div>
        <div class="info-row"><div class="info-label">{{ __('messages.relation') }}</div><div class="info-value">{{ $candidate->emergency_relation ?: '—' }}</div></div>
        <div class="info-row"><div class="info-label">{{ __('messages.address') }}</div><div class="info-value">{{ $candidate->emergency_address ?: '—' }}</div></div>

        {{-- Work Experience --}}
        @if(!empty($candidate->work_experiences))
        <div class="section-badge">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            {{ __('messages.work_experience') }}
        </div>
        @foreach($candidate->work_experiences as $exp)
        <div class="exp-item">
            <div class="exp-company">{{ $exp['company'] ?? '—' }}</div>
            <div class="exp-meta d-flex flex-wrap gap-3 mt-1">
                <span>📅 {{ $exp['start_date'] ?? '' }} → {{ $exp['end_date'] ?? '' }}</span>
                <span>💼 {{ $exp['position'] ?? '' }}</span>
                @if(!empty($exp['salary']))
                <span>💰 {{ $exp['salary'] }}</span>
                @endif
                @if(!empty($exp['reason_leaving']))
                <span>🚪 {{ $exp['reason_leaving'] }}</span>
                @endif
            </div>
        </div>
        @endforeach
        @endif

        {{-- Expected Salary --}}
        @if($candidate->expected_salary)
        <div class="section-badge mt-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            {{ __('messages.expected_salary') }}
        </div>
        <div class="info-row"><div class="info-label">{{ __('messages.expected_salary') }}</div><div class="info-value fw-bold text-success">{{ $candidate->expected_salary }}</div></div>
        @endif

        {{-- ===== NHẬN XÉT CỦA QUẢN LÝ CAO CẤP ===== --}}
        @php
            $currentUserReview = $candidate->seniorManagers->firstWhere('id', auth()->id());
            $isSeniorManager   = auth()->user()->hasRole('senior_manager');
            $isAssigned        = $currentUserReview !== null;
        @endphp

        {{-- Form nhận xét: chỉ hiển thị cho senior_manager đã được chuyển đơn --}}
        @if($isSeniorManager && $isAssigned)
        <div class="card border-0 rounded-4 mt-4 shadow-sm overflow-hidden">
            <div class="p-3 d-flex align-items-center gap-2" style="background:linear-gradient(135deg,#1a3a5c,#2563eb);">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <span class="fw-bold text-white" style="font-size:.95rem;">✏️ Nhận xét của bạn</span>
                @if($currentUserReview->pivot->reviewed_at)
                    <span class="badge ms-auto" style="background:rgba(255,255,255,0.2);font-size:.75rem;">
                        Đã nhận xét lúc {{ \Carbon\Carbon::parse($currentUserReview->pivot->reviewed_at)->format('H:i d/m/Y') }}
                    </span>
                @endif
            </div>
            <div class="p-4">
                @if(session('success'))
                <div class="alert alert-success rounded-3 mb-3 py-2" style="font-size:.88rem;">{{ session('success') }}</div>
                @endif

                {{-- Hiển thị nhận xét đã có (nếu có) --}}
                @if($currentUserReview->pivot->review_note)
                <div class="mb-3 p-3 rounded-3" style="background:#f8fafc;border-left:4px solid
                    @if($currentUserReview->pivot->review_result === 'approved') #16a34a
                    @elseif($currentUserReview->pivot->review_result === 'rejected') #dc2626
                    @else #f59e0b @endif;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge rounded-pill px-3
                            @if($currentUserReview->pivot->review_result === 'approved') bg-success
                            @elseif($currentUserReview->pivot->review_result === 'rejected') bg-danger
                            @else bg-warning text-dark @endif">
                            @if($currentUserReview->pivot->review_result === 'approved') ✅ Đồng ý tuyển dụng
                            @elseif($currentUserReview->pivot->review_result === 'rejected') ❌ Không tuyển dụng
                            @else ⏳ Chờ xem xét @endif
                        </span>
                        <span class="text-muted small">Nhận xét hiện tại:</span>
                    </div>
                    <div style="font-size:.9rem;white-space:pre-line;">{{ $currentUserReview->pivot->review_note }}</div>
                </div>
                @endif

                <form method="POST" action="{{ route('candidates.review', $candidate->id) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Nội dung nhận xét <span class="text-danger">*</span></label>
                        <textarea name="review_note" rows="4" class="form-control rounded-3" required
                            placeholder="Nhận xét về ứng viên, năng lực, thái độ, phù hợp với vị trí..."
                            style="font-size:.9rem;border-color:#e5e7eb;">{{ old('review_note', $currentUserReview->pivot->review_note) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size:.85rem;">Kết quả đánh giá <span class="text-danger">*</span></label>
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach([
                                ['value'=>'approved','label'=>'✅ Đồng ý tuyển dụng','color'=>'#16a34a','bg'=>'#dcfce7'],
                                ['value'=>'rejected','label'=>'❌ Không tuyển dụng','color'=>'#dc2626','bg'=>'#fee2e2'],
                                ['value'=>'pending', 'label'=>'⏳ Chờ xem xét','color'=>'#d97706','bg'=>'#fef9c3'],
                            ] as $opt)
                            <label style="cursor:pointer;flex:1;min-width:130px;">
                                <input type="radio" name="review_result" value="{{ $opt['value'] }}" class="d-none review-radio"
                                    {{ old('review_result', $currentUserReview->pivot->review_result ?? 'pending') === $opt['value'] ? 'checked' : '' }}>
                                <div class="text-center rounded-3 border py-2 px-2 fw-semibold review-radio-label"
                                     style="font-size:.82rem;border-color:#e5e7eb;transition:all .15s;"
                                     data-active-bg="{{ $opt['bg'] }}" data-active-color="{{ $opt['color'] }}" data-active-border="{{ $opt['color'] }}">
                                    {{ $opt['label'] }}
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- ===== 5 TRƯỜNG BỔ SUNG ===== --}}
                    <hr class="my-3" style="border-color:#e5e7eb;">
                    <div class="fw-semibold mb-3" style="font-size:.85rem;color:#1a3a5c;">📋 Thông tin tuyển dụng (nếu đồng ý)</div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:.85rem;">💰 Mức lương đề xuất</label>
                            <input type="text" name="proposed_salary" class="form-control rounded-3"
                                placeholder="VD: 5,000,000 VNĐ / tháng"
                                style="font-size:.9rem;border-color:#e5e7eb;"
                                value="{{ old('proposed_salary', $currentUserReview->pivot->proposed_salary) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:.85rem;">📅 Ngày bắt đầu làm việc</label>
                            <input type="date" name="start_date" class="form-control rounded-3"
                                style="font-size:.9rem;border-color:#e5e7eb;"
                                value="{{ old('start_date', $currentUserReview->pivot->start_date ? \Carbon\Carbon::parse($currentUserReview->pivot->start_date)->format('Y-m-d') : '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:.85rem;">⏱️ Thời gian thử việc</label>
                            <input type="text" name="probation_period" class="form-control rounded-3"
                                placeholder="VD: 2 tháng"
                                style="font-size:.9rem;border-color:#e5e7eb;"
                                value="{{ old('probation_period', $currentUserReview->pivot->probation_period) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="font-size:.85rem;">🏢 Bộ phận / Vị trí phân công</label>
                            <input type="text" name="assigned_department" class="form-control rounded-3"
                                placeholder="VD: Phòng Kế toán / Nhân viên kinh doanh"
                                style="font-size:.9rem;border-color:#e5e7eb;"
                                value="{{ old('assigned_department', $currentUserReview->pivot->assigned_department) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size:.85rem;">📝 Ghi chú bổ sung</label>
                            <textarea name="extra_note" rows="3" class="form-control rounded-3"
                                placeholder="Các điều kiện hoặc ghi chú thêm..."
                                style="font-size:.9rem;border-color:#e5e7eb;">{{ old('extra_note', $currentUserReview->pivot->extra_note) }}</textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn fw-bold rounded-3 px-4 py-2" style="background:linear-gradient(135deg,#1a3a5c,#2563eb);color:white;">
                        💾 Lưu nhận xét
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Danh sách nhận xét của tất cả senior manager: Admin và HR xem --}}
        @if(auth()->user()->hasAnyRole(['admin', 'hr']) && $candidate->seniorManagers->count() > 0)
        <div class="mt-4">
            <div class="section-badge">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Nhận xét từ Quản lý cao cấp
            </div>
            @foreach($candidate->seniorManagers as $sm)
            <div class="mb-3 rounded-3 border overflow-hidden" style="font-size:.88rem;">
                <div class="d-flex align-items-center gap-2 px-3 py-2 bg-light border-bottom">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold"
                         style="width:30px;height:30px;font-size:.8rem;flex-shrink:0;">
                        {{ mb_substr($sm->name, 0, 1) }}
                    </div>
                    <span class="fw-semibold">{{ $sm->name }}</span>
                    @if($sm->pivot->reviewed_at)
                        <span class="text-muted small ms-1">— {{ \Carbon\Carbon::parse($sm->pivot->reviewed_at)->format('H:i d/m/Y') }}</span>
                        <span class="badge ms-auto
                            @if($sm->pivot->review_result === 'approved') bg-success
                            @elseif($sm->pivot->review_result === 'rejected') bg-danger
                            @else bg-warning text-dark @endif">
                            @if($sm->pivot->review_result === 'approved') ✅ Đồng ý
                            @elseif($sm->pivot->review_result === 'rejected') ❌ Không tuyển
                            @else ⏳ Chờ xem @endif
                        </span>
                    @else
                        <span class="badge bg-secondary ms-auto">Chưa nhận xét</span>
                    @endif
                </div>
                <div class="px-3 py-2" style="color:#374151;">
                    @if($sm->pivot->review_note)
                    <div style="white-space:pre-line;margin-bottom:.5rem;">{{ $sm->pivot->review_note }}</div>
                    @else
                    <span class="text-muted">—</span>
                    @endif
                    @if($sm->pivot->proposed_salary || $sm->pivot->start_date || $sm->pivot->probation_period || $sm->pivot->assigned_department)
                    <div class="d-flex flex-wrap gap-2 mt-2" style="font-size:.8rem;">
                        @if($sm->pivot->proposed_salary)
                        <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-2 py-1">💰 {{ $sm->pivot->proposed_salary }}</span>
                        @endif
                        @if($sm->pivot->start_date)
                        <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle px-2 py-1">📅 {{ \Carbon\Carbon::parse($sm->pivot->start_date)->format('d/m/Y') }}</span>
                        @endif
                        @if($sm->pivot->probation_period)
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">⏱️ {{ $sm->pivot->probation_period }}</span>
                        @endif
                        @if($sm->pivot->assigned_department)
                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-1">🏢 {{ $sm->pivot->assigned_department }}</span>
                        @endif
                    </div>
                    @endif
                    @if($sm->pivot->extra_note)
                    <div class="mt-2 p-2 rounded-2" style="background:#f8fafc;font-size:.82rem;color:#475569;white-space:pre-line;">📝 {{ $sm->pivot->extra_note }}</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Section chuyển đơn: HR & Admin --}}
        @if(auth()->user()->hasAnyRole(['admin', 'hr']))
        <div class="card border-0 bg-light rounded-4 p-4 mt-4 shadow-sm">
            <h5 class="fw-bold mb-2">📢 Chuyển đơn ứng tuyển tới quản lý cao cấp</h5>
            <p class="text-secondary small mb-3">Chọn các Quản lý cao cấp sẽ được xem và theo dõi hồ sơ này.</p>

            <form method="POST" action="{{ route('candidates.route', $candidate->id) }}">
                @csrf
                <div class="mb-3">
                    <div class="dropdown w-100">
                        <button class="btn btn-white bg-white border w-100 text-start d-flex justify-content-between align-items-center dropdown-toggle py-3 px-3 rounded-3" type="button" id="seniorManagerDropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                            <span id="selectedManagersLabel">Chọn quản lý cao cấp...</span>
                        </button>
                        <div class="dropdown-menu p-3 w-100 shadow-sm border-0 rounded-3 mt-1" aria-labelledby="seniorManagerDropdown" style="max-height: 250px; overflow-y: auto; min-width: 250px;">
                            @forelse($seniorManagers as $sm)
                            <div class="form-check py-1">
                                <label class="form-check-label d-flex align-items-center gap-2 w-100" style="cursor: pointer;">
                                    <input class="form-check-input sm-checkbox" type="checkbox" name="senior_manager_ids[]" value="{{ $sm->id }}" data-name="{{ $sm->name }}"
                                        @checked($candidate->seniorManagers->contains($sm->id))>
                                    <span class="fw-medium text-dark">{{ $sm->name }}</span>
                                </label>
                            </div>
                            @empty
                            <div class="text-muted small py-1">Không tìm thấy Quản lý cao cấp nào trong hệ thống.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                @if($seniorManagers->count() > 0)
                <button type="submit" class="btn btn-primary rounded-3 fw-bold px-4 py-2 mt-2">
                    💾 Cập nhật chuyển đơn
                </button>
                @endif
            </form>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const checkboxes = document.querySelectorAll('.sm-checkbox');
                const label = document.getElementById('selectedManagersLabel');

                function updateLabel() {
                    const selectedNames = [];
                    checkboxes.forEach(cb => {
                        if (cb.checked) {
                            selectedNames.push(cb.getAttribute('data-name'));
                        }
                    });
                    if (selectedNames.length > 0) {
                        label.textContent = "Đã chọn: " + selectedNames.join(', ');
                    } else {
                        label.textContent = "Chọn quản lý cao cấp...";
                    }
                }

                checkboxes.forEach(cb => cb.addEventListener('change', updateLabel));
                updateLabel(); // Initial call
            });
        </script>
        @endif

        {{-- Review radio styling script --}}
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const radios = document.querySelectorAll('.review-radio');
            function updateStyles() {
                radios.forEach(r => {
                    const lbl = r.nextElementSibling;
                    if (r.checked) {
                        lbl.style.background = lbl.dataset.activeBg;
                        lbl.style.color = lbl.dataset.activeColor;
                        lbl.style.borderColor = lbl.dataset.activeBorder;
                    } else {
                        lbl.style.background = '';
                        lbl.style.color = '';
                        lbl.style.borderColor = '#e5e7eb';
                    }
                });
            }
            radios.forEach(r => r.addEventListener('change', updateStyles));
            updateStyles();
        });
        </script>

        @if($candidate->submitter)
        <div class="mt-4 p-3 bg-light rounded-3 small text-muted">
            {{ __('messages.submitted_by') }}: <strong>{{ $candidate->submitter->name }}</strong> — {{ $candidate->created_at->format('H:i d/m/Y') }}
        </div>
        @else
        <div class="mt-4 p-3 bg-light rounded-3 small text-muted">
            {{ __('messages.submitted_by_candidate') }} — {{ $candidate->created_at->format('H:i d/m/Y') }}
        </div>
        @endif
    </div>
</div>
@endsection
