<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CandidateController extends Controller
{
    // ─── Public (no auth) ────────────────────────────────────────────────────

    public function showPublicForm()
    {
        return view('candidates.apply');
    }

    public function storePublic(Request $request)
    {
        $validated = $this->validateForm($request);

        $validated['submitted_by'] = null;
        $this->handlePhotoUpload($request, $validated);
        $this->handleWorkExperiences($request, $validated);

        Candidate::create($validated);

        return redirect()->route('candidates.public')
            ->with('success', __('messages.candidate_submit_success'));
    }

    // ─── Protected (admin / senior_manager) ──────────────────────────────────

    private function buildCandidateQuery(Request $request)
    {
        $user = auth()->user();
        $query = Candidate::with(['seniorManagers', 'submitter'])->latest();

        // Scope to assigned candidates for senior_manager and supervisor
        if ($user->hasAnyRole(['senior_manager', 'supervisor'])) {
            $query->whereHas('seniorManagers', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        // Non-management accounts (e.g. accounts with candidates.create permission) can only view candidates created within the last 1 hour
        if (!$user->hasAnyRole(['admin', 'hr', 'senior_manager', 'supervisor'])) {
            $query->where('created_at', '>=', now()->subHours(1));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%")
                  ->orWhere('department_applied', 'like', "%$search%")
                  ->orWhere('position_applied', 'like', "%$search%");
            });
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'new') {
                $query->whereDoesntHave('seniorManagers');
            } elseif ($status === 'approved_locked') {
                $query->whereHas('seniorManagers', function ($q) {
                    $q->where('candidate_senior_manager.review_result', 'approved')
                      ->where('candidate_senior_manager.is_locked', true);
                });
            } elseif ($status === 'approved_draft') {
                $query->whereHas('seniorManagers', function ($q) {
                    $q->where('candidate_senior_manager.review_result', 'approved')
                      ->where(function ($sub) {
                          $sub->whereNull('candidate_senior_manager.is_locked')
                              ->orWhere('candidate_senior_manager.is_locked', false);
                      });
                });
            } elseif ($status === 'approved') {
                $query->whereHas('seniorManagers', function ($q) {
                    $q->where('candidate_senior_manager.review_result', 'approved');
                });
            } elseif ($status === 'rejected_locked') {
                $query->whereHas('seniorManagers', function ($q) {
                    $q->where('candidate_senior_manager.review_result', 'rejected')
                      ->where('candidate_senior_manager.is_locked', true);
                })->whereDoesntHave('seniorManagers', function ($q) {
                    $q->where('candidate_senior_manager.review_result', 'approved');
                });
            } elseif ($status === 'rejected_draft') {
                $query->whereHas('seniorManagers', function ($q) {
                    $q->where('candidate_senior_manager.review_result', 'rejected')
                      ->where(function ($sub) {
                          $sub->whereNull('candidate_senior_manager.is_locked')
                              ->orWhere('candidate_senior_manager.is_locked', false);
                      });
                })->whereDoesntHave('seniorManagers', function ($q) {
                    $q->where('candidate_senior_manager.review_result', 'approved');
                });
            } elseif ($status === 'rejected') {
                $query->whereHas('seniorManagers', function ($q) {
                    $q->where('candidate_senior_manager.review_result', 'rejected');
                })->whereDoesntHave('seniorManagers', function ($q) {
                    $q->where('candidate_senior_manager.review_result', 'approved');
                });
            } elseif ($status === 'routed') {
                $query->has('seniorManagers', '=', 1)
                      ->whereDoesntHave('seniorManagers', function ($q) {
                          $q->whereIn('candidate_senior_manager.review_result', ['approved', 'rejected']);
                      });
            } elseif ($status === 'forwarded') {
                $query->has('seniorManagers', '>', 1)
                      ->whereDoesntHave('seniorManagers', function ($q) {
                          $q->whereIn('candidate_senior_manager.review_result', ['approved', 'rejected']);
                      });
            }
        }

        return $query;
    }

    public function index(Request $request)
    {
        $query = $this->buildCandidateQuery($request);

        // Tính toán thống kê báo cáo trạng thái theo bộ lọc hiện tại
        $statsQuery = clone $query;
        $allFiltered = $statsQuery->get();

        $statusCounts = [
            'new'             => 0,
            'routed'          => 0,
            'forwarded'       => 0,
            'approved_draft'  => 0,
            'approved_locked' => 0,
            'rejected_draft'  => 0,
            'rejected_locked' => 0,
        ];

        foreach ($allFiltered as $c) {
            $st = $c->overall_review_status;
            if (isset($statusCounts[$st])) {
                $statusCounts[$st]++;
            } else {
                $statusCounts[$st] = 1;
            }
        }

        $totalCount = $allFiltered->count();
        $totalApproved = $statusCounts['approved_locked'] + $statusCounts['approved_draft'];
        $totalRejected = $statusCounts['rejected_locked'] + $statusCounts['rejected_draft'];
        $totalPendingReview = $statusCounts['new'] + $statusCounts['routed'] + $statusCounts['forwarded'];

        $stats = [
            'total'               => $totalCount,
            'total_approved'      => $totalApproved,
            'total_rejected'      => $totalRejected,
            'total_pending'       => $totalPendingReview,
            'status_counts'       => $statusCounts,
            'approval_rate'       => $totalCount > 0 ? round(($totalApproved / $totalCount) * 100, 1) : 0,
        ];

        $candidates = $query->paginate(20)->withQueryString();

        return view('candidates.index', compact('candidates', 'stats'));
    }

    /**
     * Xuất file Excel tổng hợp các báo cáo ứng viên theo bộ lọc
     */
    public function export(Request $request)
    {
        $query = $this->buildCandidateQuery($request);
        $candidates = $query->get();

        $headers = [
            'Mã phiếu',
            'Ngày ứng tuyển',
            'Họ và tên',
            'Giới tính',
            'Ngày sinh',
            'Số CCCD',
            'Số điện thoại',
            'Địa chỉ',
            'Trình độ văn hóa',
            'Kỹ năng ngoại ngữ',
            'Bộ phận ứng tuyển',
            'Vị trí ứng tuyển',
            'Mức lương mong muốn',
            'Tình trạng hôn nhân',
            'Người giới thiệu',
            'Trạng thái phê duyệt',
            'Quản lý xem xét / phê duyệt',
            'Bộ phận tiếp nhận (Được duyệt)',
            'Mức lương chính thức (Được duyệt)',
            'Ngày bắt đầu đi làm',
            'Thời gian thử việc',
            'Đánh giá / Ghi chú của quản lý',
            'Ghi chú của HR',
        ];

        $renderRow = function ($c) {
            $managersList = $c->seniorManagers->pluck('name')->join(', ');
            
            // Tìm thông tin phê duyệt từ Quản lý (nếu có)
            $approvedManager = $c->seniorManagers->first(fn($u) => $u->pivot->review_result === 'approved');
            $activeManager = $approvedManager ?? $c->seniorManagers->first();
            
            $assignedDept = $activeManager ? ($activeManager->pivot->assigned_department ?? '') : '';
            $approvedSalary = $activeManager ? ($activeManager->pivot->proposed_salary ?? '') : '';
            $startDate = $activeManager && $activeManager->pivot->start_date ? \Carbon\Carbon::parse($activeManager->pivot->start_date)->format('d/m/Y') : '';
            $probation = $activeManager ? ($activeManager->pivot->probation_period ?? '') : '';
            $managerNotes = $activeManager ? ($activeManager->pivot->review_note ?? '') : '';

            $cells = [
                '#UV-' . str_pad($c->id, 5, '0', STR_PAD_LEFT),
                $c->created_at ? $c->created_at->format('d/m/Y H:i') : '',
                $c->full_name,
                $c->gender === 'male' ? 'Nam' : 'Nữ',
                $c->dob ? $c->dob->format('d/m/Y') : '',
                $c->id_number,
                $c->phone,
                $c->address,
                $c->education,
                $c->language_skills,
                $c->department_applied,
                $c->position_applied,
                $c->expected_salary,
                $c->marital_status === 'married' ? 'Đã kết hôn' : ($c->marital_status === 'divorced' ? 'Đã ly hôn' : 'Độc thân'),
                $c->referral_name ? ($c->referral_name . ' (' . ($c->referral_department ?? '') . ')') : 'Tự ứng tuyển',
                $c->status_label,
                $managersList,
                $assignedDept,
                $approvedSalary,
                $startDate,
                $probation,
                $managerNotes,
                $c->hr_notes ?? '',
            ];

            $xml = "    <Row ss:Height=\"20\">\n";
            foreach ($cells as $cell) {
                $type = 'String';
                $safe = htmlspecialchars((string)$cell, ENT_XML1, 'UTF-8');
                $xml .= "     <Cell><Data ss:Type=\"{$type}\">{$safe}</Data></Cell>\n";
            }
            $xml .= "    </Row>\n";
            return $xml;
        };

        // Tính toán số liệu thống kê tóm tắt
        $statusCounts = [
            'new'             => 0,
            'routed'          => 0,
            'forwarded'       => 0,
            'approved_draft'  => 0,
            'approved_locked' => 0,
            'rejected_draft'  => 0,
            'rejected_locked' => 0,
        ];
        foreach ($candidates as $c) {
            $st = $c->overall_review_status;
            if (isset($statusCounts[$st])) {
                $statusCounts[$st]++;
            } else {
                $statusCounts[$st] = 1;
            }
        }
        $totalApproved = $statusCounts['approved_locked'] + $statusCounts['approved_draft'];
        $totalRejected = $statusCounts['rejected_locked'] + $statusCounts['rejected_draft'];
        $totalPending = $statusCounts['new'] + $statusCounts['routed'] + $statusCounts['forwarded'];

        $fileName = 'bao-cao-ung-vien-' . now()->format('Ymd-His') . '.xls';

        return response()->streamDownload(function () use ($candidates, $headers, $renderRow, $statusCounts, $totalApproved, $totalRejected, $totalPending) {
            $output = fopen('php://output', 'w');

            $preamble = '<?xml version="1.0"?>' . "\n";
            $preamble .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
            $preamble .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" ' . "\n";
            $preamble .= ' xmlns:o="urn:schemas-microsoft-com:office:office" ' . "\n";
            $preamble .= ' xmlns:x="urn:schemas-microsoft-com:office:excel" ' . "\n";
            $preamble .= ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" ' . "\n";
            $preamble .= ' xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";

            // Style definitions
            $preamble .= " <Styles>\n";
            $preamble .= "  <Style ss:ID=\"Default\" ss:Name=\"Normal\">\n";
            $preamble .= "   <Alignment ss:Vertical=\"Center\"/>\n";
            $preamble .= "   <Font ss:FontName=\"Calibri\" ss:Size=\"11\"/>\n";
            $preamble .= "  </Style>\n";
            $preamble .= "  <Style ss:ID=\"HeaderStyle\">\n";
            $preamble .= "   <Alignment ss:Horizontal=\"Center\" ss:Vertical=\"Center\" ss:WrapText=\"1\"/>\n";
            $preamble .= "   <Borders>\n";
            $preamble .= "    <Border ss:Position=\"Bottom\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#cbd5e1\"/>\n";
            $preamble .= "   </Borders>\n";
            $preamble .= "   <Font ss:FontName=\"Calibri\" ss:Size=\"11\" ss:Bold=\"1\" ss:Color=\"#0f172a\"/>\n";
            $preamble .= "   <Interior ss:Color=\"#e2e8f0\" ss:Pattern=\"Solid\"/>\n";
            $preamble .= "  </Style>\n";
            $preamble .= "  <Style ss:ID=\"TitleStyle\">\n";
            $preamble .= "   <Alignment ss:Horizontal=\"Left\" ss:Vertical=\"Center\"/>\n";
            $preamble .= "   <Font ss:FontName=\"Calibri\" ss:Size=\"14\" ss:Bold=\"1\" ss:Color=\"#1e3a8a\"/>\n";
            $preamble .= "  </Style>\n";
            $preamble .= "  <Style ss:ID=\"StatLabel\">\n";
            $preamble .= "   <Font ss:FontName=\"Calibri\" ss:Size=\"11\" ss:Bold=\"1\" ss:Color=\"#334155\"/>\n";
            $preamble .= "   <Interior ss:Color=\"#f1f5f9\" ss:Pattern=\"Solid\"/>\n";
            $preamble .= "  </Style>\n";
            $preamble .= "  <Style ss:ID=\"StatVal\">\n";
            $preamble .= "   <Alignment ss:Horizontal=\"Center\" ss:Vertical=\"Center\"/>\n";
            $preamble .= "   <Font ss:FontName=\"Calibri\" ss:Size=\"11\" ss:Bold=\"1\" ss:Color=\"#0f172a\"/>\n";
            $preamble .= "  </Style>\n";
            $preamble .= " </Styles>\n";

            fwrite($output, $preamble);

            // Worksheet 1: Danh sách ứng viên
            fwrite($output, " <Worksheet ss:Name=\"Danh sách ứng viên\">\n");
            fwrite($output, "  <Table>\n");

            // Title Row
            fwrite($output, "   <Row ss:Height=\"28\">\n");
            fwrite($output, "    <Cell ss:StyleID=\"TitleStyle\"><Data ss:Type=\"String\">BÁO CÁO TỔNG HỢP PHIẾU PHỎNG VẤN ỨNG VIÊN</Data></Cell>\n");
            fwrite($output, "   </Row>\n");

            // Subtitle Row
            $dateRange = 'Thời gian xuất: ' . now()->format('d/m/Y H:i:s') . ' | Tổng cộng: ' . count($candidates) . ' hồ sơ';
            fwrite($output, "   <Row ss:Height=\"20\">\n");
            fwrite($output, "    <Cell><Data ss:Type=\"String\">{$dateRange}</Data></Cell>\n");
            fwrite($output, "   </Row>\n");
            fwrite($output, "   <Row ss:Height=\"10\"></Row>\n");

            // Table Header Row
            fwrite($output, "   <Row ss:Height=\"26\">\n");
            foreach ($headers as $h) {
                fwrite($output, "    <Cell ss:StyleID=\"HeaderStyle\"><Data ss:Type=\"String\">{$h}</Data></Cell>\n");
            }
            fwrite($output, "   </Row>\n");

            // Data Rows
            foreach ($candidates as $c) {
                fwrite($output, $renderRow($c));
            }
            fwrite($output, "  </Table>\n");
            fwrite($output, " </Worksheet>\n");

            // Worksheet 2: Thống kê trạng thái (Summary Sheet)
            fwrite($output, " <Worksheet ss:Name=\"Thống kê trạng thái\">\n");
            fwrite($output, "  <Table>\n");
            fwrite($output, "   <Row ss:Height=\"26\">\n");
            fwrite($output, "    <Cell ss:StyleID=\"TitleStyle\"><Data ss:Type=\"String\">BẢNG THỐNG KÊ TỔNG HỢP TRẠNG THÁI</Data></Cell>\n");
            fwrite($output, "   </Row>\n");
            fwrite($output, "   <Row ss:Height=\"10\"></Row>\n");

            fwrite($output, "   <Row ss:Height=\"22\">\n");
            fwrite($output, "    <Cell ss:StyleID=\"HeaderStyle\"><Data ss:Type=\"String\">Trạng thái</Data></Cell>\n");
            fwrite($output, "    <Cell ss:StyleID=\"HeaderStyle\"><Data ss:Type=\"String\">Số lượng hồ sơ</Data></Cell>\n");
            fwrite($output, "    <Cell ss:StyleID=\"HeaderStyle\"><Data ss:Type=\"String\">Tỷ lệ (%)</Data></Cell>\n");
            fwrite($output, "   </Row>\n");

            $total = max(count($candidates), 1);
            $summaryItems = [
                ['Mới nộp — Chờ chuyển', $statusCounts['new']],
                ['Đã chuyển Quản lý', $statusCounts['routed']],
                ['Đã chuyển tiếp Quản lý khác', $statusCounts['forwarded']],
                ['Đề xuất Đồng ý tuyển (Bản nháp)', $statusCounts['approved_draft']],
                ['Đồng ý tuyển (Đã duyệt chính thức)', $statusCounts['approved_locked']],
                ['Đề xuất Không tuyển (Bản nháp)', $statusCounts['rejected_draft']],
                ['Không tuyển (Đã duyệt chính thức)', $statusCounts['rejected_locked']],
                ['--- TỔNG DUYỆT ĐỒNG Ý TUYỂN ---', $totalApproved],
                ['--- TỔNG TỪ CHỐI TUYỂN ---', $totalRejected],
                ['--- TỔNG ĐANG XỬ LÝ / CHỜ DUYỆT ---', $totalPending],
                ['=== TỔNG CỘNG HỒ SƠ ===', count($candidates)],
            ];

            foreach ($summaryItems as $item) {
                $pct = round(($item[1] / $total) * 100, 1) . '%';
                $isSpecial = str_contains($item[0], '---') || str_contains($item[0], '===');
                $styleAttr = $isSpecial ? ' ss:StyleID="StatLabel"' : '';
                fwrite($output, "   <Row ss:Height=\"20\">\n");
                fwrite($output, "    <Cell{$styleAttr}><Data ss:Type=\"String\">{$item[0]}</Data></Cell>\n");
                fwrite($output, "    <Cell ss:StyleID=\"StatVal\"><Data ss:Type=\"Number\">{$item[1]}</Data></Cell>\n");
                fwrite($output, "    <Cell ss:StyleID=\"StatVal\"><Data ss:Type=\"String\">{$pct}</Data></Cell>\n");
                fwrite($output, "   </Row>\n");
            }

            fwrite($output, "  </Table>\n");
            fwrite($output, " </Worksheet>\n");

            fwrite($output, "</Workbook>");
            fclose($output);
        }, $fileName, [
            'Content-Type' => 'application/vnd.ms-excel',
        ]);
    }

    public function create()
    {
        return view('candidates.apply', ['isAdmin' => true]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateForm($request);

        $validated['submitted_by'] = auth()->id();
        $this->handlePhotoUpload($request, $validated);
        $this->handleWorkExperiences($request, $validated);

        $candidate = Candidate::create($validated);

        return redirect()->route('candidates.show', $candidate->id)
            ->with('success', __('messages.candidate_submit_success'));
    }

    public function edit($id)
    {
        $candidate = Candidate::with('seniorManagers')->findOrFail($id);
        $user = auth()->user();

        // Non-admins cannot edit if candidate has already been routed to senior manager(s)
        if ($candidate->seniorManagers->count() > 0 && !$user->isAdminUser()) {
            abort(403, 'Phiếu ứng tuyển này đã được HR chuyển tới Quản lý cấp cao, không thể chỉnh sửa.');
        }

        // Non-management accounts can only edit candidates created within the last 30 minutes
        if (!$user->hasAnyRole(['admin', 'hr', 'senior_manager', 'supervisor'])) {
            if ($candidate->created_at < now()->subMinutes(30)) {
                abort(403, 'Phiếu phỏng vấn này đã tạo quá 30 phút, bạn không thể chỉnh sửa nữa.');
            }
        }

        return view('candidates.apply', [
            'isAdmin'   => true,
            'isEdit'    => true,
            'candidate' => $candidate,
        ]);
    }

    public function update(Request $request, $id)
    {
        $candidate = Candidate::with('seniorManagers')->findOrFail($id);
        $user = auth()->user();

        if ($candidate->seniorManagers->count() > 0 && !$user->isAdminUser()) {
            abort(403, 'Phiếu ứng tuyển này đã được HR chuyển tới Quản lý cấp cao, không thể chỉnh sửa.');
        }

        if (!$user->hasAnyRole(['admin', 'hr', 'senior_manager', 'supervisor'])) {
            if ($candidate->created_at < now()->subMinutes(30)) {
                abort(403, 'Phiếu phỏng vấn này đã tạo quá 30 phút, bạn không thể chỉnh sửa nữa.');
            }
        }

        $validated = $this->validateForm($request);

        $this->handlePhotoUpload($request, $validated);
        $this->handleWorkExperiences($request, $validated);

        $candidate->update($validated);

        return redirect()->route('candidates.show', $candidate->id)
            ->with('success', 'Đã cập nhật thông tin phiếu ứng tuyển thành công.');
    }

    public function show($id)
    {
        $candidate = Candidate::with('seniorManagers')->findOrFail($id);
        $user = auth()->user();

        // Non-management accounts can only access candidates created within the last 1 hour
        if (!$user->hasAnyRole(['admin', 'hr', 'senior_manager', 'supervisor'])) {
            if ($candidate->created_at < now()->subHours(1)) {
                abort(403, 'Phiếu phỏng vấn này đã tạo quá 1 tiếng, tài khoản của bạn không thể xem lại.');
            }
        }

        // Access control for senior manager & supervisor: only see assigned candidate forms
        if ($user->hasAnyRole(['senior_manager', 'supervisor'])) {
            if (!$candidate->seniorManagers->contains($user->id)) {
                abort(403, 'Bạn không được phép truy cập hồ sơ này.');
            }
        }

        $seniorManagers = [];
        if ($user->hasAnyRole(['admin', 'hr'])) {
            $seniorManagers = \App\Models\User::role(['senior_manager', 'supervisor'])->get();
        }

        $allSeniorManagers = \App\Models\User::role(['senior_manager', 'supervisor'])->get();

        return view('candidates.show', compact('candidate', 'seniorManagers', 'allSeniorManagers'));
    }

    public function destroy($id)
    {
        $candidate = Candidate::findOrFail($id);
        $user = auth()->user();

        if (!$user->hasAnyRole(['admin', 'hr', 'senior_manager', 'supervisor'])) {
            if ($candidate->created_at < now()->subHours(1)) {
                abort(403, 'Phiếu phỏng vấn này đã tạo quá 1 tiếng, tài khoản của bạn không thể xóa.');
            }
        }

        if ($candidate->photo_path) {
            $physicalPath = str_replace('storage/', '', ltrim($candidate->photo_path, '/'));
            Storage::disk('public')->delete($physicalPath);
        }

        if ($candidate->hr_photo_path) {
            $physicalPath = str_replace('storage/', '', ltrim($candidate->hr_photo_path, '/'));
            Storage::disk('public')->delete($physicalPath);
        }

        $candidate->delete();

        return redirect()->route('candidates.index')
            ->with('success', __('messages.candidate_deleted'));
    }

    public function exportPrint($id)
    {
        $candidate = Candidate::with('seniorManagers')->findOrFail($id);
        $user = auth()->user();

        if (!$user->hasAnyRole(['admin', 'hr', 'senior_manager', 'supervisor'])) {
            if ($candidate->created_at < now()->subHours(1)) {
                abort(403, 'Phiếu phỏng vấn này đã tạo quá 1 tiếng, tài khoản của bạn không thể in.');
            }
        }

        // Access control for senior manager & supervisor
        if ($user->hasAnyRole(['senior_manager', 'supervisor'])) {
            if (!$candidate->seniorManagers->contains($user->id)) {
                abort(403, 'Bạn không được phép in hồ sơ này.');
            }
        }

        return view('candidates.print', compact('candidate'));
    }

    public function routeCandidate(Request $request, $id)
    {
        abort_unless(auth()->user()->hasAnyRole(['admin', 'hr']), 403);

        $candidate = Candidate::findOrFail($id);

        $request->validate([
            'senior_manager_ids'   => ['nullable', 'array'],
            'senior_manager_ids.*' => ['integer', 'exists:users,id'],
            'hr_notes'             => ['nullable', 'string', 'max:5000'],
            'hr_photo'             => ['nullable', 'image', 'max:10240'],
            'remove_hr_photo'      => ['nullable', 'boolean'],
        ]);

        $updateData = [
            'hr_notes' => $request->input('hr_notes'),
        ];

        if ($request->boolean('remove_hr_photo') && $candidate->hr_photo_path) {
            $physicalPath = str_replace('storage/', '', ltrim($candidate->hr_photo_path, '/'));
            Storage::disk('public')->delete($physicalPath);
            $updateData['hr_photo_path'] = null;
        }

        if ($request->hasFile('hr_photo')) {
            if ($candidate->hr_photo_path) {
                $physicalPath = str_replace('storage/', '', ltrim($candidate->hr_photo_path, '/'));
                Storage::disk('public')->delete($physicalPath);
            }
            $file = $request->file('hr_photo');
            $filename = now()->format('Y-m-d-His-') . uniqid() . '.' . $file->extension();
            $path = $file->storeAs('candidates/hr', $filename, 'public');
            $updateData['hr_photo_path'] = 'storage/' . ltrim($path, '/');
        }

        $candidate->update($updateData);
        $candidate->seniorManagers()->sync($request->input('senior_manager_ids', []));

        return back()->with('success', 'Đã lưu nhận xét và chuyển đơn ứng tuyển thành công.');
    }

    public function saveReview(Request $request, $id)
    {
        // Only the assigned senior_manager, supervisor or admin can review
        $user = auth()->user();
        $candidate = Candidate::findOrFail($id);

        $isAssigned = $candidate->seniorManagers->contains($user->id);
        abort_unless($user->isAdminUser() || ($user->hasAnyRole(['senior_manager', 'supervisor']) && $isAssigned), 403);

        $currentPivot = $candidate->seniorManagers->where('id', $user->id)->first()?->pivot;
        if ($currentPivot && $currentPivot->is_locked) {
            return back()->with('error', '🔒 Phiếu đánh giá này đã được duyệt và khóa, không thể chỉnh sửa.');
        }

        $request->validate([
            'review_note'         => ['required', 'string', 'max:2000'],
            'review_result'       => ['required', 'in:approved,rejected,pending'],
            'proposed_salary'     => ['nullable', 'string', 'max:100'],
            'start_date'          => ['nullable', 'date'],
            'probation_period'    => ['nullable', 'string', 'max:100'],
            'assigned_department' => ['nullable', 'string', 'max:255'],
            'extra_note'          => ['nullable', 'string', 'max:2000'],
            'submit_action'       => ['nullable', 'string', 'in:save,approve'],
        ]);

        $isApproveAction = $request->input('submit_action') === 'approve';

        if ($isApproveAction) {
            if (!in_array($request->review_result, ['approved', 'rejected'])) {
                return back()->withInput()->with('error', '⚠️ Vui lòng chọn kết quả đánh giá (Đồng ý tuyển dụng hoặc Không tuyển dụng) trước khi bấm Duyệt.');
            }
        }

        $candidate->seniorManagers()->updateExistingPivot($user->id, [
            'review_note'         => $request->review_note,
            'review_result'       => $request->review_result,
            'proposed_salary'     => $request->proposed_salary,
            'start_date'          => $request->start_date,
            'probation_period'    => $request->probation_period,
            'assigned_department' => $request->assigned_department,
            'extra_note'          => $request->extra_note,
            'is_locked'           => $isApproveAction ? true : false,
            'reviewed_at'         => now(),
        ]);

        if ($isApproveAction) {
            return back()->with('success', '🔒 Đã phê duyệt và khóa phiếu thành công! Phiếu này không thể chỉnh sửa thêm.');
        }

        return back()->with('success', '✅ Đã lưu nhận xét thành công.');
    }

    public function unlockReview(Request $request, $id, $userId)
    {
        // Only admin can unlock / un-approve candidate review
        abort_unless(auth()->user()->isAdminUser(), 403, 'Chỉ Admin mới có quyền hạ phiếu duyệt.');

        $candidate = Candidate::findOrFail($id);

        $candidate->seniorManagers()->updateExistingPivot($userId, [
            'is_locked' => false,
        ]);

        return back()->with('success', '🔓 Đã hạ phiếu duyệt thành công! Quản lý hiện tại có thể chỉnh sửa lại dữ liệu.');
    }

    public function forwardReview(Request $request, $id)
    {
        $user = auth()->user();
        $candidate = Candidate::findOrFail($id);

        $isAssigned = $candidate->seniorManagers->contains($user->id);
        abort_unless($user->isAdminUser() || ($user->hasAnyRole(['senior_manager', 'supervisor']) && $isAssigned), 403);

        $request->validate([
            'target_user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        if (!in_array($request->review_result, ['approved', 'rejected'])) {
            return back()->withInput()->with('error', '⚠️ Vui lòng chọn kết quả đánh giá (Đồng ý tuyển dụng hoặc Không tuyển dụng) trước khi bấm Gửi phiếu.');
        }

        $targetUser = \App\Models\User::findOrFail($request->target_user_id);

        // Save current user draft review if review_note is provided and not locked
        $currentPivot = $candidate->seniorManagers->where('id', $user->id)->first()?->pivot;
        if ($request->boolean('save_current_draft') && $request->filled('review_note') && (!$currentPivot || !$currentPivot->is_locked)) {
            $candidate->seniorManagers()->updateExistingPivot($user->id, [
                'review_note'         => $request->review_note,
                'review_result'       => $request->review_result ?? 'pending',
                'proposed_salary'     => $request->proposed_salary,
                'start_date'          => $request->start_date,
                'probation_period'    => $request->probation_period,
                'assigned_department' => $request->assigned_department,
                'extra_note'          => $request->extra_note,
                'reviewed_at'         => now(),
            ]);
        }

        // Attach target senior manager if not attached
        if (!$candidate->seniorManagers->contains($targetUser->id)) {
            $candidate->seniorManagers()->attach($targetUser->id, [
                'review_result' => 'pending',
            ]);
        }

        return back()->with('success', "📤 Đã gửi phiếu phỏng vấn tới Quản lý {$targetUser->name} thành công!");
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function validateForm(Request $request): array
    {
        return $request->validate([
            'full_name'            => 'required|string|max:255',
            'gender'               => 'required|in:male,female',
            'dob'                  => 'nullable|date',
            'id_number'            => 'nullable|string|regex:/^[0-9]+$/|max:12',
            'education'            => 'nullable|string|max:255',
            'language_skills'      => 'nullable|string|max:255',
            'department_applied'   => 'nullable|string|max:255',
            'position_applied'     => 'required|string|max:255',
            'phone'                => 'required|string|regex:/^[0-9]+$/|max:10',
            'address'              => 'nullable|string|max:500',
            'bank_account'         => 'nullable|string|max:50',
            'photo'                => 'nullable|image|max:5120',
            'marital_status'       => 'required|in:single,married,divorced',
            'children_dob'         => 'nullable|array|max:3',
            'children_dob.*'       => 'nullable|string|max:10',
            'referral_source'      => 'nullable|array',
            'referral_source.*'    => 'nullable|string',
            'referral_name'        => 'nullable|string|max:255',
            'referral_department'  => 'nullable|string|max:255',
            'referral_relation'    => 'nullable|string|max:100',
            'emergency_name'       => 'nullable|string|max:255',
            'emergency_address'    => 'nullable|string|max:500',
            'emergency_relation'   => 'nullable|string|max:100',
            'emergency_phone'      => 'nullable|string|regex:/^[0-9]+$/|max:10',
            'expected_salary'      => 'nullable|string|max:50',
        ], [
            'id_number.regex'       => 'Số CCCD chỉ được nhập ký tự số.',
            'id_number.max'         => 'Số CCCD tối đa 12 ký tự.',
            'phone.regex'           => 'Số điện thoại chỉ được nhập ký tự số.',
            'phone.max'             => 'Số điện thoại tối đa 10 ký tự.',
            'emergency_phone.regex' => 'Số điện thoại khẩn cấp chỉ được nhập ký tự số.',
            'emergency_phone.max'   => 'Số điện thoại khẩn cấp tối đa 10 ký tự.',
        ]);
    }

    private function handlePhotoUpload(Request $request, array &$data): void
    {
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = now()->format('Y-m-d-His-') . uniqid() . '.' . $file->extension();
            $path = $file->storeAs('candidates', $filename, 'public');
            $data['photo_path'] = 'storage/' . ltrim($path, '/');
        }
        unset($data['photo']);
    }

    private function handleWorkExperiences(Request $request, array &$data): void
    {
        $experiences = [];
        $rawExps = $request->input('work_experiences', []);
        foreach ($rawExps as $exp) {
            if (!empty($exp['company']) || !empty($exp['position'])) {
                $experiences[] = [
                    'start_date'    => $exp['start_date'] ?? '',
                    'end_date'      => $exp['end_date'] ?? '',
                    'company'       => $exp['company'] ?? '',
                    'position'      => $exp['position'] ?? '',
                    'salary'        => $exp['salary'] ?? '',
                    'reason_leaving' => $exp['reason_leaving'] ?? '',
                ];
            }
        }
        $data['work_experiences'] = empty($experiences) ? null : $experiences;
    }
}
