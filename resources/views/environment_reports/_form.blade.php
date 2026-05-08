@php
    $managedDepartments = auth()->check() ? auth()->user()->managedDepartments() : [];
    $isManagedDepartmentUser = auth()->check() && !empty($managedDepartments);
    $isSingleManagedDepartmentUser = $isManagedDepartmentUser && count($managedDepartments) === 1;
    $departmentOptions = $isManagedDepartmentUser ? $managedDepartments : $departments;
    $currentReport = $report ?? null;
@endphp

<style>
    .report-shell {
        display: grid;
        gap: 1.5rem;
    }

    .report-card {
        background: white;
        border-radius: 18px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        border: 1px solid rgba(148, 163, 184, 0.18);
    }

    .report-grid-table {
        min-width: 1700px;
        font-size: 0.9rem;
    }

    .report-grid-table th {
        background: #eff6ff;
        border-color: #dbeafe;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }

    .report-grid-table td {
        vertical-align: middle;
    }

    .report-grid-table .sticky-col {
        position: sticky;
        left: 0;
        background: white;
        z-index: 1;
    }

    .report-grid-table thead .sticky-col {
        background: #e0f2fe;
        z-index: 2;
    }

    .report-grid-table .signal-high {
        background: #fff7ed;
    }

    .report-grid-table .signal-danger {
        background: #fef2f2;
    }
</style>

<div class="report-shell">
    <div class="report-card p-4">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Vá»‹ trÃ­</label>
                <select name="department_name" class="form-select" @disabled($isSingleManagedDepartmentUser)>
                    @foreach($departmentOptions as $department)
                        <option value="{{ $department }}" @selected(old('department_name', $selectedDepartment) === $department)>{{ $department }}</option>
                    @endforeach
                </select>
                @if($isSingleManagedDepartmentUser)
                    <input type="hidden" name="department_name" value="{{ old('department_name', $selectedDepartment) }}">
                @endif
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">ThÃ¡ng</label>
                <input type="number" min="1" max="12" name="report_month" class="form-control" value="{{ old('report_month', $reportMonth) }}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">NÄƒm</label>
                <input type="number" min="2020" max="2100" name="report_year" class="form-control" value="{{ old('report_year', $reportYear) }}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Tráº¡ng thÃ¡i</label>
                <select name="status" class="form-select">
                    <option value="draft" @selected(old('status', $currentReport?->status ?? 'draft') === 'draft')>NhÃ¡p</option>
                    <option value="submitted" @selected(old('status', $currentReport?->status ?? 'draft') === 'submitted')>ÄÃ£ chá»‘t</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Má»‘c giá»</label>
                <div class="form-control bg-light d-flex align-items-center">7:30 / 10:30 / 14:00 / 16:30</div>
            </div>
            <div class="col-12">
                <label class="form-label fw-bold">Ghi chÃº chung</label>
                <textarea name="note" rows="2" class="form-control" placeholder="Ghi chÃº chung cho bÃ¡o cÃ¡o thÃ¡ng">{{ old('note', $currentReport?->note ?? '') }}</textarea>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-0">
            <div class="fw-bold mb-2">Dá»¯ liá»‡u chÆ°a há»£p lá»‡</div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="report-card">
        <div class="p-4 border-bottom">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h3 class="h5 fw-bold mb-1">Báº£ng ghi nhiá»‡t Ä‘á»™ vÃ  Ä‘á»™ áº©m</h3>
                    <div class="text-muted small">Nháº­p theo tá»«ng ngÃ y. Há»‡ thá»‘ng giá»¯ Ä‘Ãºng cáº¥u trÃºc 4 má»‘c giá» cá»§a phiáº¿u giáº¥y.</div>
                </div>
                <div class="small text-muted">Khuyáº¿n nghá»‹: nhiá»‡t Ä‘á»™ 18-37Â°C, Ä‘á»™ áº©m 40-65%</div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered report-grid-table mb-0">
                <thead>
                    <tr>
                        <th rowspan="2" class="sticky-col">NgÃ y</th>
                        <th colspan="4">Äá»™ áº©m (%)</th>
                        <th colspan="4">Nhiá»‡t Ä‘á»™ (Â°C)</th>
                        <th rowspan="2">Thá»i tiáº¿t</th>
                        <th colspan="4">HÃ¬nh thá»©c cáº£i thiá»‡n</th>
                        <th rowspan="2">NgÆ°á»i kiá»ƒm tra</th>
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
                    @foreach(old('entries', $entries) as $index => $entry)
                        @php
                            $day = data_get($entry, 'day_number');
                        @endphp
                        <tr>
                            <td class="sticky-col fw-bold text-center">
                                {{ $day }}
                                <input type="hidden" name="entries[{{ $index }}][day_number]" value="{{ $day }}">
                                <input type="hidden" name="entries[{{ $index }}][report_date]" value="{{ data_get($entry, 'report_date') }}">
                            </td>

                            @foreach($timeSlots as $slot)
                                @php
                                    $value = data_get($entry, "humidity_{$slot}");
                                    $signalClass = ($value !== null && $value !== '' && ((float) $value < 40 || (float) $value > 65)) ? 'signal-danger' : '';
                                @endphp
                                <td class="{{ $signalClass }}">
                                    <input type="number" step="0.1" min="0" max="100" name="entries[{{ $index }}][humidity_{{ $slot }}]" class="form-control form-control-sm" value="{{ $value }}">
                                </td>
                            @endforeach

                            @foreach($timeSlots as $slot)
                                @php
                                    $value = data_get($entry, "temperature_{$slot}");
                                    $signalClass = ($value !== null && $value !== '' && ((float) $value < 18 || (float) $value > 37)) ? 'signal-high' : '';
                                @endphp
                                <td class="{{ $signalClass }}">
                                    <input type="number" step="0.1" min="-10" max="80" name="entries[{{ $index }}][temperature_{{ $slot }}]" class="form-control form-control-sm" value="{{ $value }}">
                                </td>
                            @endforeach

                            <td>
                                <select name="entries[{{ $index }}][weather]" class="form-select form-select-sm">
                                    <option value="">--</option>
                                    @foreach($weatherOptions as $weather)
                                        <option value="{{ $weather }}" @selected(data_get($entry, 'weather') === $weather)>{{ $weather }}</option>
                                    @endforeach
                                </select>
                            </td>

                            @foreach($timeSlots as $slot)
                                <td>
                                    <select name="entries[{{ $index }}][action_{{ $slot }}]" class="form-select form-select-sm">
                                        <option value="">--</option>
                                        @foreach($actionOptions as $action)
                                            <option value="{{ $action }}" @selected(data_get($entry, "action_{$slot}") === $action)>{{ $action }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            @endforeach

                            <td>
                                <input type="text" name="entries[{{ $index }}][checked_by]" class="form-control form-control-sm" value="{{ data_get($entry, 'checked_by') }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-top bg-light rounded-bottom-4 d-flex flex-wrap justify-content-between gap-3 small text-muted">
            <div>A/B/C: dÃ¹ng cho hÃ nh Ä‘á»™ng kháº¯c phá»¥c theo quy trÃ¬nh thá»±c táº¿ cá»§a nhÃ  mÃ¡y.</div>
            <div>GiÃ¡ trá»‹ vÆ°á»£t ngÆ°á»¡ng sáº½ Ä‘Æ°á»£c tÃ´ ná»n Ä‘á»ƒ ngÆ°á»i nháº­p dá»… nháº­n biáº¿t.</div>
        </div>
    </div>

    <div class="sticky-actions d-flex flex-wrap justify-content-end gap-2">
        <a href="{{ route('environment-reports.index') }}" class="btn btn-light px-4">Quay láº¡i</a>
        <button type="submit" class="btn btn-primary px-4">LÆ°u bÃ¡o cÃ¡o</button>
    </div>
</div>
