<?php

namespace App\Http\Controllers;

use App\Models\AuditTemplate;
use App\Models\EnvironmentReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EnvironmentReportController extends Controller
{
    private const TIME_SLOTS = ['0730', '1030', '1400', '1630'];
    private const ACTION_OPTIONS = ['A', 'B', 'C'];
    private const WEATHER_OPTIONS = ['Nắng', 'Râm', 'Mưa', 'Âm u'];

    public function index(Request $request)
    {
        $user = $request->user();
        $query = EnvironmentReport::with(['creator', 'entries']);

        if (!empty($user->managed_department)) {
            $mappedDepartment = AuditTemplate::normalizeDepartmentName($user->managed_department);
            $query->whereRaw('LOWER(TRIM(department_name)) = ?', [$mappedDepartment]);
        }

        if ($request->filled('report_year')) {
            $query->where('report_year', (int) $request->report_year);
        }

        if ($request->filled('report_month')) {
            $query->where('report_month', (int) $request->report_month);
        }

        $reports = $query
            ->orderByDesc('report_year')
            ->orderByDesc('report_month')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $currentYear = (int) now()->format('Y');
        $years = range($currentYear + 1, $currentYear - 3);

        return view('environment_reports.index', compact('reports', 'years'));
    }

    public function create(Request $request)
    {
        $departmentName = $request->string('department_name')->toString();
        $reportYear = (int) ($request->input('report_year') ?: now()->year);
        $reportMonth = (int) ($request->input('report_month') ?: now()->month);

        if (empty($departmentName)) {
            $departmentName = $request->user()->managed_department ?: 'QA';
        }

        $departmentName = $this->resolveDepartmentForUser($request->user(), $departmentName);
        $daysInMonth = Carbon::create($reportYear, $reportMonth, 1)->daysInMonth;
        $entries = $this->buildDefaultEntries($reportYear, $reportMonth, $daysInMonth);
        $departments = $this->availableDepartments();

        return view('environment_reports.create', [
            'departments' => $departments,
            'selectedDepartment' => $departmentName,
            'reportYear' => $reportYear,
            'reportMonth' => $reportMonth,
            'entries' => $entries,
            'weatherOptions' => self::WEATHER_OPTIONS,
            'actionOptions' => self::ACTION_OPTIONS,
            'timeSlots' => self::TIME_SLOTS,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateReport($request);
        $departmentName = $this->resolveDepartmentForUser($request->user(), $validated['department_name']);

        $report = EnvironmentReport::create([
            'department_name' => $departmentName,
            'report_year' => (int) $validated['report_year'],
            'report_month' => (int) $validated['report_month'],
            'creator_id' => $request->user()->id,
            'status' => $validated['status'],
            'note' => $validated['note'] ?? null,
        ]);

        $this->syncEntries($report, $validated['entries']);

        return redirect()
            ->route('environment-reports.show', $report)
            ->with('success', 'Đã tạo báo cáo môi trường thành công.');
    }

    public function show(EnvironmentReport $environmentReport)
    {
        $this->authorizeReportAccess($environmentReport);
        $environmentReport->load(['creator', 'entries']);

        return view('environment_reports.show', [
            'report' => $environmentReport,
            'timeSlots' => self::TIME_SLOTS,
        ]);
    }

    public function edit(EnvironmentReport $environmentReport)
    {
        $this->authorizeReportMutation($environmentReport);
        $environmentReport->load('entries');
        $departments = $this->availableDepartments();

        return view('environment_reports.edit', [
            'report' => $environmentReport,
            'departments' => $departments,
            'selectedDepartment' => $environmentReport->department_name,
            'reportYear' => $environmentReport->report_year,
            'reportMonth' => $environmentReport->report_month,
            'entries' => $environmentReport->entries->map(function ($entry) {
                return $entry->toArray();
            })->all(),
            'weatherOptions' => self::WEATHER_OPTIONS,
            'actionOptions' => self::ACTION_OPTIONS,
            'timeSlots' => self::TIME_SLOTS,
        ]);
    }

    public function update(Request $request, EnvironmentReport $environmentReport)
    {
        $this->authorizeReportMutation($environmentReport);
        $validated = $this->validateReport($request, $environmentReport->id);
        $departmentName = $this->resolveDepartmentForUser($request->user(), $validated['department_name']);

        $environmentReport->update([
            'department_name' => $departmentName,
            'report_year' => (int) $validated['report_year'],
            'report_month' => (int) $validated['report_month'],
            'status' => $validated['status'],
            'note' => $validated['note'] ?? null,
        ]);

        $this->syncEntries($environmentReport, $validated['entries']);

        return redirect()
            ->route('environment-reports.show', $environmentReport)
            ->with('success', 'Đã cập nhật báo cáo môi trường thành công.');
    }

    public function print(EnvironmentReport $environmentReport)
    {
        $this->authorizeReportAccess($environmentReport);
        $environmentReport->load(['creator', 'entries']);

        return view('environment_reports.print', [
            'report' => $environmentReport,
            'timeSlots' => self::TIME_SLOTS,
        ]);
    }

    private function validateReport(Request $request, ?int $reportId = null): array
    {
        $rules = [
            'department_name' => ['required', 'string', Rule::in($this->availableDepartments())],
            'report_year' => ['required', 'integer', 'between:2020,2100'],
            'report_month' => ['required', 'integer', 'between:1,12'],
            'status' => ['required', Rule::in(['draft', 'submitted'])],
            'note' => ['nullable', 'string', 'max:1000'],
            'entries' => ['required', 'array', 'min:28', 'max:31'],
            'entries.*.day_number' => ['required', 'integer', 'between:1,31'],
            'entries.*.weather' => ['nullable', Rule::in(self::WEATHER_OPTIONS)],
            'entries.*.checked_by' => ['nullable', 'string', 'max:100'],
        ];

        foreach (self::TIME_SLOTS as $slot) {
            $rules["entries.*.humidity_{$slot}"] = ['nullable', 'numeric', 'between:0,100'];
            $rules["entries.*.temperature_{$slot}"] = ['nullable', 'numeric', 'between:-10,80'];
            $rules["entries.*.action_{$slot}"] = ['nullable', Rule::in(self::ACTION_OPTIONS)];
        }

        $validated = $request->validate($rules, [
            'department_name.in' => 'Bộ phận không hợp lệ.',
            'entries.required' => 'Báo cáo phải có dữ liệu theo ngày.',
        ]);

        $exists = EnvironmentReport::query()
            ->where('department_name', $validated['department_name'])
            ->where('report_year', $validated['report_year'])
            ->where('report_month', $validated['report_month'])
            ->when($reportId, fn($query) => $query->where('id', '!=', $reportId))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'department_name' => 'Báo cáo của bộ phận này trong tháng đã tồn tại.',
            ]);
        }

        return $validated;
    }

    private function syncEntries(EnvironmentReport $report, array $entries): void
    {
        $report->entries()->delete();

        foreach ($entries as $entry) {
            $payload = [
                'day_number' => (int) $entry['day_number'],
                'report_date' => Carbon::create($report->report_year, $report->report_month, (int) $entry['day_number'])->toDateString(),
                'weather' => $entry['weather'] ?? null,
                'checked_by' => $entry['checked_by'] ?? null,
            ];

            foreach (self::TIME_SLOTS as $slot) {
                $payload["humidity_{$slot}"] = $entry["humidity_{$slot}"] ?? null;
                $payload["temperature_{$slot}"] = $entry["temperature_{$slot}"] ?? null;
                $payload["action_{$slot}"] = $entry["action_{$slot}"] ?? null;
            }

            $report->entries()->create($payload);
        }
    }

    private function authorizeReportAccess(EnvironmentReport $report): void
    {
        $user = auth()->user();

        if (empty($user->managed_department)) {
            return;
        }

        $userDepartment = AuditTemplate::normalizeDepartmentName($user->managed_department);
        $reportDepartment = AuditTemplate::normalizeDepartmentName($report->department_name);

        abort_unless($userDepartment === $reportDepartment, 403, 'Bạn không có quyền truy cập báo cáo này.');
    }

    private function authorizeReportMutation(EnvironmentReport $report): void
    {
        $this->authorizeReportAccess($report);

        $user = auth()->user();
        if ($user->isAdminUser()) {
            return;
        }

        if (!empty($user->managed_department)) {
            return;
        }

        abort_unless($report->creator_id === $user->id, 403, 'Bạn không có quyền chỉnh sửa báo cáo này.');
    }

    private function resolveDepartmentForUser($user, string $departmentName): string
    {
        if (!empty($user->managed_department)) {
            return $user->managed_department;
        }

        return $departmentName;
    }

    private function availableDepartments(): array
    {
        return [
            'Gần nhà ăn công nhân',
            'Kho cơ khí',
            'Xưởng 6 Tầng 1',
            'Xưởng 6 Tầng 2',
            'Xưởng 1',
            'Xưởng 2',
            'Xưởng 3',
            'Xưởng 5',
        ];
    }

    private function buildDefaultEntries(int $year, int $month, int $daysInMonth): array
    {
        $entries = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $entry = [
                'day_number' => $day,
                'report_date' => Carbon::create($year, $month, $day)->toDateString(),
                'weather' => null,
                'checked_by' => null,
            ];

            foreach (self::TIME_SLOTS as $slot) {
                $entry["humidity_{$slot}"] = null;
                $entry["temperature_{$slot}"] = null;
                $entry["action_{$slot}"] = null;
            }

            $entries[] = $entry;
        }

        return $entries;
    }
}
