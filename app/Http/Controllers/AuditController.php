<?php

namespace App\Http\Controllers;

use App\Models\AuditCriterion;
use App\Models\AuditRecord;
use App\Models\AuditResult;
use App\Models\AuditTemplate;
use App\Models\User;
use App\Notifications\AuditStatusChangedNotification;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        // Get list of active templates for creating new audits
        $templates = AuditTemplate::where('is_active', true)->get();
        $user = auth()->user();

        $query = AuditRecord::with('template', 'auditor', 'results');

        // 1. Authorization/Base Filter: Filter by managed department if the user has one
        if (!empty($user->managed_department)) {
            $mappedDept = $user->managed_department === 'Bán thành phẩm' ? 'BTP' : $user->managed_department;
            $query->whereHas('template', function ($q) use ($mappedDept) {
                $q->where('department_name', $mappedDept);
            });
        }

        // 2. User Filters for History
        if ($request->filled('history_dept') && $request->history_dept !== 'all') {
            $query->whereHas('template', function ($q) use ($request) {
                $q->where('department_name', $request->history_dept);
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $audits = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('audits.index', compact('templates', 'audits'));
    }

    public function create(Request $request)
    {
        abort_unless(empty(auth()->user()->managed_department), 403, 'Bạn không có quyền thực hiện chức năng này vì đã được phân công bộ phận.');

        $templateId = $request->query('template_id');
        abort_unless($templateId, 400, 'Thiếu ID bộ đánh giá');

        $template = AuditTemplate::with('criteria')->findOrFail($templateId);

        return view('audits.create', compact('template'));
    }

    public function store(Request $request)
    {
        abort_unless(empty(auth()->user()->managed_department), 403, 'Bạn không có quyền thực hiện chức năng này vì đã được phân công bộ phận.');

        $templateId = $request->input('audit_template_id');
        $template = AuditTemplate::findOrFail($templateId);

        $results = $request->input('results', []);

        // Validate results
        $request->validate([
            'results' => 'required|array',
            'results.*.audit_criterion_id' => 'required|exists:audit_criteria,id',
            'results.*.is_passed' => 'required|in:1,0',
            'results.*.note' => 'required_if:results.*.is_passed,0',
            'results.*.image' => 'nullable|array|max:20', // Array of images, max 20 images
            'results.*.image.*' => 'image|max:10240', // Max 10MB per image
        ], [
            'results.*.note.required_if' => 'Vui lòng nhập ghi chú nguyên nhân cho các mục Không đạt (X).',
            'results.*.image.*.image' => 'File đính kèm phải là hình ảnh.',
            'results.*.image.*.max' => 'Kích thước ảnh tối đa là 10MB.',
        ]);

        // Create the main record
        $record = AuditRecord::create([
            'audit_template_id' => $template->id,
            'auditor_id' => auth()->id(),
            'status' => 'completed',
        ]);

        // Create the individual criterion results
        foreach ($results as $index => $item) {
            $imagePaths = [];

            // Handle image upload if provided and the criterion failed
            if (isset($item['is_passed']) && $item['is_passed'] == 0 && $request->hasFile("results.{$index}.image")) {
                $files = $request->file("results.{$index}.image");
                if (!is_array($files)) {
                    $files = [$files];
                }

                foreach ($files as $file) {
                    $filename = now()->format('Y-m-d-His-') . uniqid() . '.' . $file->extension();
                    $path = $file->storeAs('audits', $filename, 'public');
                    // Prefix with storage/ for asset referencing 
                    $imagePaths[] = 'storage/' . ltrim($path, '/');
                }
            }

            AuditResult::create([
                'audit_record_id' => $record->id,
                'audit_criterion_id' => $item['audit_criterion_id'],
                'is_passed' => (bool)$item['is_passed'],
                'note' => $item['note'] ?? null,
                'image_path' => empty($imagePaths) ? null : $imagePaths,
            ]);
        }

        $record->load('results');
        $this->notifyDepartmentAboutAuditCreated($record, $template);

        return redirect('/audits')->with('success', "Đã đánh giá thành công bộ phận {$template->department_name}! Điểm số đạt: {$record->score}%");
    }

    public function editAudit($id)
    {
        $user = auth()->user();
        abort_unless(
            $user->hasRole('admin') || ($user->hasRole('audit') && empty($user->managed_department)),
            403,
            'Bạn không có quyền sửa phiếu đánh giá.'
        );

        $audit = AuditRecord::with(['template', 'auditor', 'results.criterion'])->findOrFail($id);

        $isFullyReviewed = $audit->results->contains(function ($r) {
            return !empty($r->improver_name);
        }) &&
            $audit->results->filter(function ($r) {
                return !empty($r->improver_name) && empty($r->reviewer_name);
            })->isEmpty();

        if ($isFullyReviewed && !$user->hasRole('admin')) {
            abort(403, 'Phiếu này đã được đánh giá lần 2 và bị khóa. Chỉ Admin mới có thể chỉnh sửa.');
        }

        return view('audits.edit', compact('audit'));
    }

    public function updateAudit(Request $request, $id)
    {
        $user = auth()->user();
        abort_unless(
            $user->hasRole('admin') || ($user->hasRole('audit') && empty($user->managed_department)),
            403,
            'Bạn không có quyền sửa phiếu đánh giá.'
        );

        $audit = AuditRecord::with('results')->findOrFail($id);

        $isFullyReviewed = $audit->results->contains(function ($r) {
            return !empty($r->improver_name);
        }) &&
            $audit->results->filter(function ($r) {
                return !empty($r->improver_name) && empty($r->reviewer_name);
            })->isEmpty();

        if ($isFullyReviewed && !$user->hasRole('admin')) {
            abort(403, 'Phiếu này đã được đánh giá lần 2 và bị khóa. Chỉ Admin mới có thể chỉnh sửa.');
        }

        $results = $request->input('results', []);
        $files   = $request->file('results', []);

        foreach ($audit->results as $result) {
            $rid   = $result->id;
            $input = $results[$rid] ?? [];

            // 1. Đổi pass/fail và ghi chú
            $isPassed = isset($input['is_passed']) && $input['is_passed'] == '1';
            $note     = $isPassed ? null : ($input['note'] ?? null);

            // 2. Xử lý xóa ảnh cũ
            $currentPaths = (array) ($result->image_path ?? []);
            $toRemove     = $input['image_remove'] ?? [];      // mảng path cần xóa
            if (!empty($toRemove)) {
                foreach ($toRemove as $removePath) {
                    $currentPaths = array_filter($currentPaths, fn($p) => $p !== $removePath);
                    // Xóa file vật lý khỏi storage
                    $disk = \Illuminate\Support\Facades\Storage::disk('public');
                    $physicalPath = str_replace('storage/', '', ltrim($removePath, '/'));
                    if ($disk->exists($physicalPath)) {
                        $disk->delete($physicalPath);
                    }
                }
                $currentPaths = array_values($currentPaths);
            }

            // Nếu đổi sang Đạt thì xóa hết ảnh cũ
            if ($isPassed) {
                $currentPaths = [];
            }

            // 3. Upload ảnh mới
            if (!$isPassed && isset($files[$rid]['image'])) {
                $newFiles = $files[$rid]['image'];
                if (!is_array($newFiles)) {
                    $newFiles = [$newFiles];
                }
                foreach ($newFiles as $file) {
                    if ($file && $file->isValid()) {
                        $filename = now()->format('Y-m-d-His-') . uniqid() . '.' . $file->extension();
                        $path = $file->storeAs('audits', $filename, 'public');
                        $currentPaths[] = 'storage/' . ltrim($path, '/');
                    }
                }
            }

            // 4. Lưu
            $result->update([
                'is_passed'  => $isPassed,
                'note'       => $note,
                'image_path' => empty($currentPaths) ? null : array_values($currentPaths),
            ]);
        }

        // 5. Tính lại điểm
        $audit->refresh();
        $total  = $audit->results->count();
        $passed = $audit->results->where('is_passed', true)->count();
        $score  = $total > 0 ? round(($passed / $total) * 100, 2) : 0;
        $audit->update(['score' => $score]);

        return redirect()->route('audits.show', $audit->id)->with('success', "Đã cập nhật phiếu đánh giá! Điểm số mới: {$score}%");
    }

    public function export(Request $request)
    {
        $user = auth()->user();
        $selectedIds = collect($request->input('audit_ids', []))
            ->map(fn($id) => (int) $id)
            ->filter(fn($id) => $id > 0)
            ->unique()
            ->values();

        if ($selectedIds->isEmpty()) {
            return redirect()->route('audits.index')
                ->with('error', 'Vui lòng chọn ít nhất 1 phiếu trước khi xuất Excel.');
        }

        $query = AuditRecord::with(['template', 'auditor', 'results']);

        if (!empty($user->managed_department)) {
            $query->whereHas('template', function ($q) use ($user) {
                $q->where('department_name', $user->managed_department);
            });
        }

        $audits = $query
            ->whereIn('id', $selectedIds)
            ->orderByDesc('created_at')
            ->get();

        if ($audits->isEmpty()) {
            return redirect()->route('audits.index')
                ->with('error', 'Không có phiếu hợp lệ để xuất.');
        }

        $headers = [
            'ID',
            'Tổ',
            'Người đánh giá',
            'Thời gian',
            'Điểm số (%)',
            'Tổng mục',
            'Đạt',
            'Lỗi'
        ];

        $renderRow = function ($a) {
            $total = $a->results->count();
            $passed = $a->results->where('is_passed', true)->count();
            $failed = $total - $passed;

            $cells = [
                $a->id,
                $a->template->department_name ?? '',
                $a->auditor->name ?? '',
                $a->created_at ? $a->created_at->format('Y-m-d H:i:s') : '',
                $a->score,
                $total,
                $passed,
                $failed,
            ];

            $xml = "    <Row>\n";
            foreach ($cells as $cell) {
                // Escape XML special chars
                $safe = htmlspecialchars((string)$cell, ENT_XML1, 'UTF-8');
                $xml .= "     <Cell><Data ss:Type=\"String\">{$safe}</Data></Cell>\n";
            }
            $xml .= "    </Row>\n";
            return $xml;
        };

        $startSheet = function ($name) use ($headers) {
            $safeName = preg_replace('/[\\\\\\/?*:\\[\\]]/', ' ', $name);
            if (mb_strlen($safeName) > 31) $safeName = mb_substr($safeName, 0, 31);

            $xml = " <Worksheet ss:Name=\"{$safeName}\">\n";
            $xml .= "  <Table>\n";
            $xml .= "   <Row>\n";
            foreach ($headers as $h) {
                $xml .= "    <Cell><Data ss:Type=\"String\">{$h}</Data></Cell>\n";
            }
            $xml .= "   </Row>\n";
            return $xml;
        };

        $endSheet = "  </Table>\n </Worksheet>\n";

        $fileName = 'audits-' . now()->format('Ymd-His') . '.xls';
        return response()->streamDownload(function () use ($audits, $renderRow, $startSheet, $endSheet) {
            $output = fopen('php://output', 'w');

            $preamble = '<?xml version="1.0"?>' . "\n";
            $preamble .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
            $preamble .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" ' . "\n";
            $preamble .= ' xmlns:o="urn:schemas-microsoft-com:office:office" ' . "\n";
            $preamble .= ' xmlns:x="urn:schemas-microsoft-com:office:excel" ' . "\n";
            $preamble .= ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" ' . "\n";
            $preamble .= ' xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";

            fwrite($output, $preamble);

            fwrite($output, $startSheet('Lịch sử đánh giá'));
            foreach ($audits as $a) {
                fwrite($output, $renderRow($a));
            }
            fwrite($output, $endSheet);

            fwrite($output, "</Workbook>");
            fclose($output);
        }, $fileName, [
            'Content-Type' => 'application/vnd.ms-excel',
        ]);
    }

    public function exportDetail($id)
    {
        $audit = AuditRecord::with(['template', 'auditor', 'results.criterion'])->findOrFail($id);

        $html = view('audits.export_detail', compact('audit'))->render();

        $fileName = 'phieu-danh-gia-' . $audit->id . '-' . now()->format('Ymd-His') . '.xls';

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    public function show($id)
    {
        $audit = AuditRecord::with(['template', 'auditor', 'results.criterion'])->findOrFail($id);

        return view('audits.show', compact('audit'));
    }

    public function updateImprovements(Request $request, $id)
    {
        $audit = AuditRecord::with('results')->findOrFail($id);

        $isFullyReviewed = $audit->results->contains(function ($r) {
            return !empty($r->improver_name);
        }) &&
            $audit->results->filter(function ($r) {
                return !empty($r->improver_name) && empty($r->reviewer_name);
            })->isEmpty();

        if ($isFullyReviewed && !auth()->user()->hasRole('admin')) {
            abort(403, 'Phiếu này đã được đánh giá lần 2 và bị khóa. Chỉ Admin mới có thể chỉnh sửa.');
        }

        $validated = $request->validate([
            'improvements' => 'required|array',
            'improvements.*.root_cause' => 'required|string',
            'improvements.*.corrective_action' => 'required|string',
            'improvements.*.improvement_deadline' => 'required|date',
        ]);

        foreach ($validated['improvements'] as $resultId => $improvementData) {
            $result = $audit->results->where('id', $resultId)->first();
            if ($result) {
                // If already has an improvement and not an admin, don't allow rewrite
                if (!empty($result->improver_name) && !auth()->user()->hasRole('admin')) {
                    continue;
                }

                $result->update([
                    'root_cause' => $improvementData['root_cause'],
                    'corrective_action' => $improvementData['corrective_action'],
                    'improvement_deadline' => $improvementData['improvement_deadline'],
                    'improver_name' => auth()->user()->name,
                ]);
            }
        }

        $this->notifyAuditParticipants(
            $audit,
            'audit_improved',
            'messages.notif_audit_improved_title',
            'messages.notif_audit_improved_message',
            ['id' => $audit->id, 'department' => $audit->template->department_name]
        );

        return redirect()->back()->with('success', 'Đã lưu thông tin cải thiện thành công.');
    }

    public function confirmCompletion(Request $request, $id)
    {
        $audit = AuditRecord::with('results')->findOrFail($id);
        $user = auth()->user();

        // Check if user is from the department being audited
        $template = $audit->template;
        if (!empty($user->managed_department)) {
            $mappedDept = $user->managed_department === 'Bán thành phẩm' ? 'BTP' : $user->managed_department;
            if ($mappedDept !== $template->department_name && !$user->hasRole('admin')) {
                abort(403, 'Bạn không có quyền xác nhận hoàn thành cho phiếu thuộc bộ phận khác.');
            }
        }

        $request->validate([
            'completion' => 'required|array',
            'completion.*.result_id' => 'required|exists:audit_results,id',
            'completion.*.completion_note' => 'required|string',
            'completion.*.images' => 'nullable|array|max:10',
            'completion.*.images.*' => 'image|max:10240',
        ]);

        foreach ($request->completion as $index => $data) {
            $result = $audit->results->where('id', $data['result_id'])->first();
            if ($result && !empty($result->root_cause)) {
                $imagePaths = $result->completion_image_path ?? [];
                if ($request->hasFile("completion.{$index}.images")) {
                    foreach ($request->file("completion.{$index}.images") as $file) {
                        $path = $file->store('audits/completions', 'public');
                        $imagePaths[] = 'storage/' . ltrim($path, '/');
                    }
                }

                $result->update([
                    'is_completed' => true,
                    'completed_at' => now(),
                    'completion_image_path' => $imagePaths,
                    'completion_note' => $data['completion_note'] ?? null,
                ]);
            }
        }

        $this->notifyAuditParticipants(
            $audit,
            'audit_completed_report',
            'messages.notif_audit_completed_report_title',
            'messages.notif_audit_completed_report_message',
            ['id' => $audit->id, 'department' => $audit->template->department_name]
        );

        return redirect()->back()->with('success', 'Đã xác nhận hoàn thành cải thiện thành công. Đang chờ Audit phê duyệt.');
    }

    public function rejectCompletion(Request $request, $id, $resultId)
    {
        $user = auth()->user();
        abort_unless($user->hasRole('admin'), 403, 'Chỉ Admin mới có quyền trả lại yêu cầu hoàn thiện.');

        $audit = AuditRecord::with('results')->findOrFail($id);
        $result = $audit->results->where('id', $resultId)->firstOrFail();

        if (!$result->is_completed) {
            return redirect()->back()->with('error', 'Hạng mục này chưa được báo cáo hoàn thiện.');
        }

        $result->update([
            'is_completed' => false,
            'completed_at' => null,
        ]);

        $this->notifyDepartmentUsers(
            $audit->template->department_name,
            $audit->id,
            'audit_completion_rejected',
            'messages.notif_audit_completion_rejected_title',
            'messages.notif_audit_completion_rejected_message',
            [auth()->id()],
            ['id' => $audit->id, 'department' => $audit->template->department_name]
        );

        return redirect()->back()->with('success', 'Đã trả lại yêu cầu xác nhận hoàn thiện thành công.');
    }

    public function storeReviews(Request $request, $id)
    {
        // 1. Check permissions (must have 'audit' or 'admin' role, and no managed_department)
        $user = auth()->user();
        abort_unless(
            ($user->hasRole('audit') || $user->hasRole('admin')) && empty($user->managed_department),
            403,
            'Bạn không có quyền đánh giá lại cải thiện.'
        );

        $audit = AuditRecord::with('results')->findOrFail($id);

        $isFullyReviewed = $audit->results->contains(function ($r) {
            return !empty($r->improver_name);
        }) &&
            $audit->results->filter(function ($r) {
                return !empty($r->improver_name) && empty($r->reviewer_name);
            })->isEmpty();

        if ($isFullyReviewed && !$user->hasRole('admin')) {
            abort(403, 'Phiếu này đã được đánh giá lần 2 và bị khóa. Chỉ Admin mới có thể chỉnh sửa.');
        }


        $request->validate([
            'reviews' => 'required|array',
            'reviews.*.result_id' => 'required|exists:audit_results,id',
            'reviews.*.review_note' => 'nullable|string',
            'reviews.*.review_image' => 'nullable|image|max:10240', // max 10MB
        ]);

        foreach ($request->reviews as $reviewData) {
            // Check if there is anything to update
            if (empty($reviewData['review_note']) && empty($reviewData['review_image'])) {
                continue;
            }

            $result = AuditResult::where('id', $reviewData['result_id'])
                ->where('audit_record_id', $audit->id)
                ->first();

            if ($result) {
                // Ensure it actually has an improver_name (meaning it was improved)
                if (empty($result->improver_name)) {
                    continue; // Skip if it hasn't been improved yet
                }

                $updateData = [
                    'reviewer_name' => $user->name,
                    'reviewed_at' => now(),
                    'review_note' => $reviewData['review_note'] ?? null,
                ];

                if (isset($reviewData['review_image'])) {
                    $file = $reviewData['review_image'];
                    $filename = now()->format('Y-m-d-His-') . uniqid() . '.' . $file->extension();
                    $path = $file->storeAs('audits', $filename, 'public');
                    // Store identically to original logic
                    $updateData['review_image_path'] = 'storage/' . ltrim($path, '/');
                }

                $result->update($updateData);
            }
        }

        $this->notifyDepartmentUsers(
            $audit->template->department_name,
            $audit->id,
            'audit_reviewed',
            'messages.notif_audit_reviewed_title',
            'messages.notif_audit_reviewed_message',
            [auth()->id()],
            ['id' => $audit->id, 'department' => $audit->template->department_name]
        );

        return redirect()->route('audits.show', $audit->id)
            ->with('success', 'Đã lưu kết quả đánh giá lại thành công.');
    }

    public function destroy($id)
    {
        $audit = AuditRecord::findOrFail($id);

        // Ensure only admin can delete
        abort_unless(auth()->user()->hasRole('admin'), 403, 'Chỉ Admin mới có quyền xóa phiếu đánh giá');

        // Delete the audit record (AuditResults should cascade, or we can delete them explicitly)
        // Let's explicitly delete results to be safe if cascade isn't set up
        $audit->results()->delete();
        $audit->delete();

        return redirect()->route('audits.index')
            ->with('success', 'Đã xóa phiếu đánh giá thành công.');
    }

    public function submitAgreements(Request $request, $id)
    {
        $audit = AuditRecord::with('results', 'template')->findOrFail($id);

        // Authorization: Current user must belong to the department being audited or be an Admin
        $user = auth()->user();
        $targetDept = $audit->template->department_name === 'BTP' ? 'Bán thành phẩm' : $audit->template->department_name;
        $isAdmin = $user->hasRole('admin');
        abort_unless($isAdmin || $user->managed_department === $targetDept, 403, 'Bạn không thuộc bộ phận này nên không thể phản hồi lỗi.');

        $request->validate([
            'agreements' => 'required|array',
            'agreements.*.department_agreement' => 'required|in:1,0',
            'agreements.*.department_reject_reason' => 'required_if:agreements.*.department_agreement,0'
        ], [
            'agreements.*.department_reject_reason.required_if' => 'Vui lòng nhập lý do nếu bạn phản đối lỗi.'
        ]);

        foreach ($request->agreements as $resultId => $data) {
            $result = AuditResult::where('id', $resultId)->where('audit_record_id', $audit->id)->first();
            if ($result && !$result->is_passed && is_null($result->department_agreement)) {
                $isAgreement = $data['department_agreement'] == '1';
                $result->update([
                    'department_agreement' => $isAgreement,
                    'department_reject_reason' => $isAgreement ? null : ($data['department_reject_reason'] ?? null)
                ]);
            }
        }

        $this->notifyAuditParticipants(
            $audit,
            'audit_responded',
            'messages.notif_audit_agreed_title',
            'messages.notif_audit_agreed_message',
            ['id' => $audit->id, 'department' => $audit->template->department_name]
        );

        return redirect()->route('audits.show', $audit->id)->with('success', 'Đã ghi nhận phản hồi lỗi thành công.');
    }

    public function reviewRejections(Request $request, $id)
    {
        $audit = AuditRecord::with('results')->findOrFail($id);

        // Authorization: Admin or Audit without department
        $user = auth()->user();
        abort_unless(
            $user->hasRole('admin') || ($user->hasRole('audit') && empty($user->managed_department)),
            403,
            'Bạn không có quyền duyệt phản đối lỗi.'
        );

        $request->validate([
            'rejections' => 'required|array',
            'rejections.*.decision' => 'required|in:1,0'
        ]);

        foreach ($request->rejections as $resultId => $data) {
            $result = AuditResult::where('id', $resultId)->where('audit_record_id', $audit->id)->first();
            if ($result && $result->department_agreement === false && is_null($result->audit_rejection_decision)) {
                $decision = $data['decision'] == '1';
                if ($decision) {
                    // Decide to waive the error
                    $result->update([
                        'audit_rejection_decision' => true,
                        'is_passed' => true, // Waived so it counts as passed
                    ]);
                } else {
                    // Decide to reject the department's rejection (Confirm error)
                    $result->update([
                        'audit_rejection_decision' => false,
                    ]);
                }
            }
        }

        return redirect()->route('audits.show', $audit->id)->with('success', 'Đã duyệt các lời phản đối lỗi.');
    }

    private function notifyDepartmentAboutAuditCreated(AuditRecord $record, AuditTemplate $template): void
    {
        $departmentName = $template->department_name ?? '';
        $title = 'messages.notif_new_audit_title';
        $message = 'messages.notif_new_audit_message';
        $params = [
            'id' => $record->id,
            'department' => $departmentName
        ];

        $this->notifyDepartmentUsers(
            $departmentName,
            $record->id,
            'audit_created',
            $title,
            $message,
            [auth()->id()],
            $params
        );
    }

    private function notifyDepartmentUsers(
        string $departmentName,
        int $auditId,
        string $eventKey,
        string $title,
        string $message,
        array $excludeUserIds = [],
        array $params = []
    ): void {
        $normalizedDepartment = $this->normalizeDepartmentName($departmentName);
        if (empty($normalizedDepartment)) {
            return;
        }

        $users = User::query()
            ->whereNotNull('managed_department')
            ->whereNotIn('id', $excludeUserIds)
            ->get()
            ->filter(function (User $user) use ($normalizedDepartment) {
                return $this->normalizeDepartmentName($user->managed_department) === $normalizedDepartment;
            });

        $notification = new AuditStatusChangedNotification($auditId, $eventKey, $title, $message, $params);
        foreach ($users as $user) {
            $user->notify($notification);
        }
    }

    private function notifyAuditParticipants(AuditRecord $audit, string $eventKey, string $title, string $message, array $params = []): void
    {
        $users = User::query()
            ->where('id', $audit->auditor_id)
            ->orWhereHas('roles', function ($q) {
                $q->whereIn('name', ['admin', 'audit']);
            })
            ->get()
            ->where('id', '!=', auth()->id())
            ->unique('id');

        $notification = new AuditStatusChangedNotification($audit->id, $eventKey, $title, $message, $params);
        foreach ($users as $user) {
            $user->notify($notification);
        }
    }

    private function normalizeDepartmentName(?string $departmentName): ?string
    {
        if (empty($departmentName)) {
            return null;
        }

        $name = mb_strtolower(trim($departmentName));
        $map = [
            'xnk' => 'XNK',
            'btp' => 'btp',
            'bán thành phẩm' => 'btp',
            'phòng mẫu' => 'phong mau',
            'kiểm vải' => 'kiem vai',
            'thu mua' => 'thu mua',
            'kho cơ khí' => 'kho co khi',
            'công trình + cơ điện' => 'cong trinh + co dien',
            'phòng thí nghiệm' => 'phong thi nghiem',
            'nhân quyền' => 'nhan quyen',
            'nhân sự' => 'nhan su',
            'hành chính' => 'hanh chinh',
            'xưởng 6 tầng 1' => 'xuong 6 tang 1',
            'xưởng 6 tầng 2' => 'xuong 6 tang 2',
        ];

        return $map[$name] ?? $name;
    }
}
