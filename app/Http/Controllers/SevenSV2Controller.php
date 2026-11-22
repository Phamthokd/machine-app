<?php

namespace App\Http\Controllers;

use App\Models\AuditTemplate;
use App\Models\SevenSChecklist;
use App\Models\SevenSV2Issue;
use App\Models\SevenSV2Record;
use App\Models\User;
use App\Support\FeatureAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SevenSV2Controller extends Controller
{
    private const HIDDEN_DEPARTMENTS = [
        'Xưởng 6 tầng 1',
        'Xưởng 6 tầng 2',
        'Xưởng 6 Tầng 1',
        'Xưởng 6 Tầng 2',
    ];

    private function getAvailableDepartments(): array
    {
        return SevenSChecklist::whereNotIn('department', self::HIDDEN_DEPARTMENTS)
            ->distinct()
            ->orderBy('department')
            ->pluck('department')
            ->toArray();
    }

    private function userManagedDepartments(User $user): array
    {
        return AuditTemplate::getDepartmentQueryNames($user->managedDepartments());
    }

    /**
     * Danh sách các đợt đánh giá 7S 2.0
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $departments = $this->getAvailableDepartments();

        $query = SevenSV2Record::with(['inspector', 'issues'])->orderByDesc('created_at');

        // Phân quyền theo tổ quản lý (nếu không phải Admin hoặc Senior Manager)
        if (!$user->isAdminUser() && !$user->hasRole('senior_manager')) {
            $managedDepts = $this->userManagedDepartments($user);
            $query->where(function ($q) use ($user, $managedDepts) {
                $q->where('inspector_id', $user->id);
                if (!empty($managedDepts)) {
                    $q->orWhereIn('department', $managedDepts);
                }
            });
        }

        // Lọc theo bộ phận
        if ($request->filled('department') && $request->department !== 'all') {
            $query->where('department', $request->department);
        }

        // Lọc theo ngày
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Lọc theo tình trạng lỗi
        if ($request->filled('filter_status')) {
            if ($request->filter_status === 'has_faults') {
                $query->where('total_penalty_points', '<', 0);
            } elseif ($request->filter_status === 'clean') {
                $query->where('total_penalty_points', 0);
            } elseif ($request->filter_status === 'pending_remedy') {
                $query->whereHas('issues', function ($iq) {
                    $iq->whereIn('status', ['pending', 'rejected']);
                });
            }
        }

        // Thống kê tổng hợp
        $statsQuery = clone $query;
        $allMatched = $statsQuery->get();
        $stats = [
            'total_inspections' => $allMatched->count(),
            'total_light'       => $allMatched->sum('light_fault_count'),
            'total_heavy'       => $allMatched->sum('heavy_fault_count'),
            'total_penalties'   => $allMatched->sum('total_penalty_points'),
            'total_pending'     => SevenSV2Issue::whereIn('record_id', $allMatched->pluck('id'))
                ->whereIn('status', ['pending', 'rejected'])
                ->count(),
        ];

        $records = $query->paginate(15)->withQueryString();

        return view('seven_s_v2.index', compact('records', 'departments', 'stats'));
    }

    /**
     * Màn hình tạo phiếu đánh giá 7S 2.0 mới
     */
    public function create(Request $request)
    {
        $departments = $this->getAvailableDepartments();
        $selectedDept = $request->query('department', $departments[0] ?? '');

        return view('seven_s_v2.create', compact('departments', 'selectedDept'));
    }

    /**
     * Lưu phiếu đánh giá 7S 2.0
     */
    public function store(Request $request)
    {
        $request->validate([
            'department'   => 'required|string',
            'general_note' => 'nullable|string',
            'issues'       => 'nullable|array',
            'issues.*.content'         => 'required_with:issues|string',
            'issues.*.severity'        => 'required_with:issues|in:light,heavy',
            'issues.*.location_detail' => 'nullable|string',
            'issues.*.images.*'        => 'nullable|image|max:12288',
        ], [
            'department.required' => 'Vui lòng chọn bộ phận được kiểm tra.',
            'issues.*.content.required_with' => 'Vui lòng điền nội dung lỗi vi phạm.',
        ]);

        $rawIssues = $request->input('issues', []);
        $lightCount = 0;
        $heavyCount = 0;

        foreach ($rawIssues as $item) {
            if (($item['severity'] ?? '') === 'heavy') {
                $heavyCount++;
            } else {
                $lightCount++;
            }
        }

        // Điểm trừ: Lỗi nhẹ -1, Lỗi nặng -2
        $totalPenalty = ($lightCount * -1) + ($heavyCount * -2);

        $record = SevenSV2Record::create([
            'department'           => $request->input('department'),
            'inspector_id'         => auth()->id(),
            'light_fault_count'    => $lightCount,
            'heavy_fault_count'    => $heavyCount,
            'total_penalty_points' => $totalPenalty,
            'general_note'         => $request->input('general_note'),
            'status'               => 'completed',
        ]);

        // Lưu từng lỗi và tải ảnh
        if (!empty($rawIssues)) {
            foreach ($rawIssues as $index => $item) {
                $severity = $item['severity'] ?? 'light';
                $points = $severity === 'heavy' ? -2 : -1;

                $imagePaths = [];
                if ($request->hasFile("issues.{$index}.images")) {
                    foreach ($request->file("issues.{$index}.images") as $file) {
                        if ($file && $file->isValid()) {
                            $filename = now()->format('Y-m-d-His-') . uniqid() . '.' . $file->extension();
                            $path = $file->storeAs('seven_s_v2', $filename, 'public');
                            $imagePaths[] = 'storage/' . $path;
                        }
                    }
                }

                SevenSV2Issue::create([
                    'record_id'       => $record->id,
                    'content'         => $item['content'],
                    'severity'        => $severity,
                    'penalty_points'  => $points,
                    'location_detail' => $item['location_detail'] ?? null,
                    'image_path'      => empty($imagePaths) ? null : $imagePaths,
                    'status'          => 'pending',
                ]);
            }
        }

        return redirect()->route('seven-s-v2.show', $record->id)
            ->with('success', 'Đã lưu đợt kiểm tra 7S 2.0 thành công! Tổng điểm trừ: ' . $totalPenalty . ' điểm.');
    }

    /**
     * Xem chi tiết đợt đánh giá 7S 2.0
     */
    public function show($id)
    {
        $record = SevenSV2Record::with(['inspector', 'issues.improver', 'issues.verifier'])->findOrFail($id);

        $user = auth()->user();
        $isInspectorOrAdmin = $user->isAdminUser() || $user->id === $record->inspector_id || $user->hasRole('senior_manager');
        
        $managedDepts = $this->userManagedDepartments($user);
        $isDepartmentManager = in_array($record->department, $managedDepts) || $user->isAdminUser();

        return view('seven_s_v2.show', compact('record', 'isInspectorOrAdmin', 'isDepartmentManager'));
    }

    /**
     * Bộ phận gửi minh chứng khắc phục
     */
    public function storeImprovement(Request $request, SevenSV2Issue $issue)
    {
        $request->validate([
            'improvement_note' => 'required|string',
            'images.*'         => 'nullable|image|max:12288',
        ], [
            'improvement_note.required' => 'Vui lòng nhập nội dung giải trình/khắc phục.',
        ]);

        $imagePaths = $issue->improvement_image_path ?? [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if ($file && $file->isValid()) {
                    $filename = now()->format('Y-m-d-His-') . uniqid() . '.' . $file->extension();
                    $path = $file->storeAs('seven_s_v2/improvements', $filename, 'public');
                    $imagePaths[] = 'storage/' . $path;
                }
            }
        }

        $issue->update([
            'improvement_note'       => $request->input('improvement_note'),
            'improvement_image_path' => empty($imagePaths) ? null : $imagePaths,
            'improver_id'            => auth()->id(),
            'improved_at'            => now(),
            'status'                 => 'improved', // Chờ nghiệm thu
        ]);

        return back()->with('success', 'Đã gửi báo cáo khắc phục thành công! Đang chờ người đánh giá nghiệm thu.');
    }

    /**
     * Người đánh giá nghiệm thu kết quả khắc phục
     */
    public function verifyImprovement(Request $request, SevenSV2Issue $issue)
    {
        $request->validate([
            'decision'         => 'required|in:verify,reject',
            'rejection_reason' => 'required_if:decision,reject|nullable|string',
        ]);

        $decision = $request->input('decision');

        if ($decision === 'verify') {
            $issue->update([
                'status'           => 'verified',
                'verifier_id'      => auth()->id(),
                'verified_at'      => now(),
                'rejection_reason' => null,
            ]);
            $msg = 'Đã nghiệm thu ĐẠT cho lỗi này.';
        } else {
            $issue->update([
                'status'           => 'rejected',
                'verifier_id'      => auth()->id(),
                'verified_at'      => now(),
                'rejection_reason' => $request->input('rejection_reason'),
            ]);
            $msg = 'Đã từ chối và yêu cầu bộ phận khắc phục lại.';
        }

        return back()->with('success', $msg);
    }

    /**
     * Xóa phiếu kiểm tra 7S 2.0
     */
    public function destroy($id)
    {
        $record = SevenSV2Record::findOrFail($id);
        $user = auth()->user();

        if (!$user->isAdminUser() && $user->id !== $record->inspector_id) {
            abort(403, 'Bạn không có quyền xóa đợt kiểm tra này.');
        }

        $record->delete();

        return redirect()->route('seven-s-v2.index')->with('success', 'Đã xóa đợt kiểm tra 7S 2.0.');
    }
}
