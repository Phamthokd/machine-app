<?php

namespace App\Http\Controllers;

use App\Models\SevenSChecklist;
use App\Models\SevenSRecord;
use App\Models\SevenSResult;
use Illuminate\Http\Request;

class SevenSController extends Controller
{
  /* Danh sách phiếu kiểm tra */
  public function index()
  {
    $user = auth()->user();
    $query = SevenSRecord::with(['inspector', 'results'])->orderByDesc('created_at');

    // Non-admin 7s users see their own records OR records of their managed department
    if (!$user->hasRole('admin')) {
      $query->where(function ($q) use ($user) {
        $q->where('inspector_id', $user->id);
        if ($user->managed_department) {
          $q->orWhere('department', $user->managed_department);
        }
      });
    }

    $records = $query->paginate(20);
    return view('seven_s.index', compact('records'));
  }

  /* Form tạo phiếu kiểm tra */
  public function create(Request $request)
  {
    $department = $request->query('department', 'XNK');
    $departments = SevenSChecklist::distinct()->pluck('department');

    $checklist = SevenSChecklist::where('department', $department)
      ->orderBy('sort_order')
      ->get()
      ->groupBy('section');

    return view('seven_s.create', compact('department', 'departments', 'checklist'));
  }

  /* Lưu phiếu kiểm tra */
  public function store(Request $request)
  {
    $department = $request->input('department', 'XNK');
    $grades = $request->input('grades', []);
    $notes  = $request->input('notes', []);

    // Validate: C/D/E must have note
    $errors = [];
    foreach ($grades as $checklistId => $grade) {
      if (in_array($grade, ['C', 'D', 'E']) && empty(trim($notes[$checklistId] ?? ''))) {
        $errors["notes.{$checklistId}"] = 'Vui lòng nhập nhận xét cho mục ' . $checklistId;
      }
    }
    if (!empty($errors)) {
      return back()->withErrors($errors)->withInput();
    }

    // Create record
    $record = SevenSRecord::create([
      'department'   => $department,
      'inspector_id' => auth()->id(),
      'score'        => 0,
      'max_score'    => 0,
    ]);

    $totalScore = 0;
    $maxScore   = 0;

    foreach ($grades as $checklistId => $grade) {
      $points = SevenSResult::gradeToPoints($grade);

      // Upload images
      $imagePaths = [];
      if ($request->hasFile("images.{$checklistId}")) {
        foreach ($request->file("images.{$checklistId}") as $file) {
          if ($file && $file->isValid()) {
            $filename = now()->format('Y-m-d-His-') . uniqid() . '.' . $file->extension();
            $path = $file->storeAs('seven_s', $filename, 'public');
            $imagePaths[] = 'storage/' . $path;
          }
        }
      }

      SevenSResult::create([
        'record_id'    => $record->id,
        'checklist_id' => $checklistId,
        'grade'        => $grade,
        'note'         => $notes[$checklistId] ?? null,
        'image_path'   => empty($imagePaths) ? null : $imagePaths,
        'points'       => $points,
      ]);

      $totalScore += $points;
      $maxScore   += 2; // max per item is B=2
    }

    $record->update(['score' => $totalScore, 'max_score' => $maxScore]);

    return redirect()->route('seven-s.show', $record->id)
      ->with('success', "Đã lưu phiếu kiểm tra 7S! Điểm: {$totalScore}/{$maxScore}");
  }

  /* Xem kết quả phiếu */
  public function show($id)
  {
    $record = SevenSRecord::with(['inspector', 'results.checklist', 'results.improver'])->findOrFail($id);
    return view('seven_s.show', compact('record'));
  }

  /* Form chỉnh sửa phiếu */
  public function edit($id)
  {
    $record = SevenSRecord::with(['results.checklist'])->findOrFail($id);
    $user = auth()->user();

    // Only admin or the inspector (7s role, empty managed_department)  
    $isInspector = $user->hasRole('7s') && empty($user->managed_department) && $record->inspector_id === $user->id;
    if (!$user->hasRole('admin') && !$isInspector) {
      abort(403, 'Bạn không có quyền chỉnh sửa phiếu này.');
    }

    // Cannot edit if fully improved (all non-B items have improvement_note)
    $nonBResults = $record->results->where('grade', '!=', 'B');
    if ($nonBResults->isNotEmpty() && $nonBResults->every(fn($r) => !empty($r->improvement_note))) {
      return redirect()->route('seven-s.show', $id)
        ->with('error', 'Phiếu này đã được cải thiện xong, không thể chỉnh sửa nữa.');
    }

    // Group existing results by checklist_id for easy access in view
    $existingResults = $record->results->keyBy('checklist_id');

    $checklist = SevenSChecklist::where('department', $record->department)
      ->orderBy('sort_order')
      ->get()
      ->groupBy('section');

    return view('seven_s.edit', compact('record', 'checklist', 'existingResults'));
  }

  /* Lưu chỉnh sửa phiếu */
  public function update(Request $request, $id)
  {
    $record = SevenSRecord::with(['results'])->findOrFail($id);
    $user = auth()->user();

    $isInspector = $user->hasRole('7s') && empty($user->managed_department) && $record->inspector_id === $user->id;
    if (!$user->hasRole('admin') && !$isInspector) {
      abort(403);
    }

    $nonBResults = $record->results->where('grade', '!=', 'B');
    if ($nonBResults->isNotEmpty() && $nonBResults->every(fn($r) => !empty($r->improvement_note))) {
      return redirect()->route('seven-s.show', $id)
        ->with('error', 'Phiếu này đã được cải thiện xong, không thể chỉnh sửa nữa.');
    }

    $grades = $request->input('grades', []);
    $notes  = $request->input('notes', []);

    // Validate: C/D/E must have note
    $errors = [];
    foreach ($grades as $checklistId => $grade) {
      if (in_array($grade, ['C', 'D', 'E']) && empty(trim($notes[$checklistId] ?? ''))) {
        $errors["notes.{$checklistId}"] = 'Vui lòng nhập nhận xét cho mục ' . $checklistId;
      }
    }
    if (!empty($errors)) {
      return back()->withErrors($errors)->withInput();
    }

    $totalScore = 0;
    $maxScore   = 0;

    foreach ($grades as $checklistId => $grade) {
      $points = SevenSResult::gradeToPoints($grade);
      $result = $record->results->firstWhere('checklist_id', $checklistId);

      // Append new images to existing ones
      $imagePaths = $result ? ($result->image_path ?? []) : [];
      if ($request->hasFile("images.{$checklistId}")) {
        foreach ($request->file("images.{$checklistId}") as $file) {
          if ($file && $file->isValid()) {
            $filename = now()->format('Y-m-d-His-') . uniqid() . '.' . $file->extension();
            $path = $file->storeAs('seven_s', $filename, 'public');
            $imagePaths[] = 'storage/' . $path;
          }
        }
      }

      // Handle removed images
      $removedImages = $request->input("remove_images.{$checklistId}", []);
      if (!empty($removedImages)) {
        $imagePaths = array_values(array_filter($imagePaths, fn($p) => !in_array($p, $removedImages)));
      }

      if ($result) {
        $result->update([
          'grade'      => $grade,
          'note'       => $notes[$checklistId] ?? null,
          'image_path' => empty($imagePaths) ? null : array_values($imagePaths),
          'points'     => $points,
        ]);
      } else {
        SevenSResult::create([
          'record_id'    => $record->id,
          'checklist_id' => $checklistId,
          'grade'        => $grade,
          'note'         => $notes[$checklistId] ?? null,
          'image_path'   => empty($imagePaths) ? null : $imagePaths,
          'points'       => $points,
        ]);
      }

      $totalScore += $points;
      $maxScore   += 2;
    }

    $record->update(['score' => $totalScore, 'max_score' => $maxScore]);

    return redirect()->route('seven-s.show', $record->id)
      ->with('success', "Đã cập nhật phiếu kiểm tra 7S! Điểm: {$totalScore}/{$maxScore}");
  }

  /* Xuất Excel chi tiết phiếu */
  public function exportDetail($id)
  {
    $record = SevenSRecord::with(['inspector', 'results.checklist'])->findOrFail($id);

    $pct = $record->max_score > 0
      ? round(($record->score / $record->max_score) * 100)
      : 0;

    $gradeColors = ['B' => '#d4edda', 'C' => '#fff3cd', 'D' => '#f8d7da', 'E' => '#c82333'];
    $gradeFontColor = ['B' => '#000', 'C' => '#000', 'D' => '#000', 'E' => '#fff'];
    $gradeScores = ['B' => '2', 'C' => '1', 'D' => '0', 'E' => '-2'];

    $fileName = "7S_{$record->department}_#{$record->id}_" . now()->format('Ymd') . '.xls';

    $grouped = $record->results->groupBy(fn($r) => $r->checklist?->section ?? 'Khác');

    $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office"
    xmlns:x="urn:schemas-microsoft-com:office:excel"
    xmlns="http://www.w3.org/TR/REC-html40">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style>
body { font-family: "Times New Roman", serif; font-size: 11pt; }
table { border-collapse: collapse; width: 100%; white-space: normal; tab-size: 4; }
td, th { border: 1px solid #999; padding: 4px 6px; vertical-align: middle; mso-number-format: "\@"; word-wrap: break-word; }
.no-border td { border: none; }
.title1 { font-size: 15pt; font-weight: bold; text-align: center; }
.title2 { font-size: 11pt; text-align: center; color: #003399; }
.info-row td { border: none; font-size: 10pt; }
.legend td { border: none; font-size: 9pt; }
.score-guide th { background: #1F2937; color: #fff; text-align: center; font-size: 10pt; font-weight: bold; }
.score-guide td { text-align: center; font-weight: bold; font-size: 11pt; }
.col-header { background: #D3D3D3; font-weight: bold; font-size: 10pt; text-align: center; }
.section-row td { background: #F2F2F2; font-weight: bold; font-size: 10pt; }
.item-num { text-align: center; width: 40px; }
.grade-cell { text-align: center; font-weight: bold; width: 60px; }
.score-cell { text-align: center; width: 60px; }
</style>
</head>
<body>
<table>
  <colgroup>
    <col width="45">  <!-- No. -->
    <col width="280"> <!-- VN Content -->
    <col width="250"> <!-- CN Content -->
    <col width="80">  <!-- Grade -->
    <col width="60">  <!-- Score -->
    <col width="220"> <!-- Notes -->
    <col width="80">  <!-- Total -->
    <col width="340"> <!-- Images -->
  </colgroup>
  <tr class="no-border"><td colspan="9" class="title1" style="border:none;">DEPARTMENT INTERNAL CHECKLIST</td></tr>
  <tr class="no-border"><td colspan="9" class="title2" style="border:none;">DANH MỤC KIỂM TRA 7S BỘ PHẬN ' . htmlspecialchars($record->department) . ' 部门7S检查项目录</td></tr>
  <tr style="height:8px;"><td colspan="9" style="border:none;"></td></tr>

  <tr class="info-row">
    <td colspan="3"><b>Ngày check 重厂日期:</b> ' . $record->created_at->format('d/m/Y') . '</td>
    <td colspan="3"><b>Auditor (Người kiểm tra by):</b> ' . htmlspecialchars($record->inspector->name ?? '—') . '</td>
    <td colspan="3"><b>Sup/ Quản lý 上司:</b></td>
  </tr>
  <tr class="info-row">
    <td colspan="3"><b>Line/Chuyền# 班:</b> ' . htmlspecialchars($record->department) . '</td>
    <td colspan="3"><b>Fact/Xưởng/ 厂:</b></td>
    <td colspan="3"></td>
  </tr>
  <tr style="height:8px;"><td colspan="9" style="border:none;"></td></tr>

  <tr class="legend">
    <td colspan="9" style="border:1px solid #999; font-size:9pt; background:#FFF9C4;">
      <b>B</b> Hoàn thành theo các yêu cầu đã đặt &nbsp;&nbsp;
      <b>C</b> Hoàn thành nhưng chưa tốt, có điểm đó là là có &nbsp;&nbsp;
      <b>D</b>: Không hoàn thành, là ở đó &nbsp;&nbsp;
      <b>E</b> Rất kém, vi phạm nghiêm trọng
    </td>
  </tr>
  <tr style="height:8px;"><td colspan="9" style="border:none;"></td></tr>

  <tr class="score-guide">
    <th colspan="4"></th>
    <th>2</th><th>1</th><th>0</th><th></th><th></th>
  </tr>
  <tr class="score-guide">
    <td colspan="4" style="background:#fff; color:#000; font-size:9pt; border:1px solid #999;">Nội dung đánh giá 重/ 内容</td>
    <td style="background:#d4edda;">B</td>
    <td style="background:#fff3cd;">C</td>
    <td style="background:#f8d7da;">D</td>
    <td></td>
    <td></td>
  </tr>
  <tr style="height:8px;"><td colspan="9" style="border:none;"></td></tr>

  <tr class="col-header">
    <td>No.</td>
    <td>Nội dung đánh giá 重/ 内容 (VN)</td>
    <td>Nội dung đánh giá 重/ 内容 (中文)</td>
    <td>Mức / 级別</td>
    <td>Điểm</td>
    <td>Nhận xét / 备注</td>
    <td>Tổng điểm ' . $record->score . '/' . $record->max_score . '</td>
    <td>Ảnh đính kèm / 附件</td>
    <td>Cải thiện / 改善</td>
  </tr>';

    $num = 1;
    foreach ($grouped as $section => $results) {
      $html .= '
  <tr class="section-row">
    <td colspan="9">' . htmlspecialchars($section) . '</td>
  </tr>';
      foreach ($results as $result) {
        $grade = $result->grade;
        $bg    = $gradeColors[$grade] ?? '#fff';
        $fc    = $gradeFontColor[$grade] ?? '#000';
        $pts   = ($result->points >= 0 ? '+' : '') . $result->points;
        $html .= '
  <tr style="vertical-align:top; height:' . (!empty((array)($result->image_path ?? [])) ? (ceil(count((array)($result->image_path ?? [])) / 2) * 160) : 40) . 'px;">
    <td class="item-num">' . $num++ . '</td>
    <td>' . htmlspecialchars($result->checklist?->content ?? '—') . '</td>
    <td style="color:#555; font-size:9pt;"></td>
    <td class="grade-cell" style="background:' . $bg . '; color:' . $fc . ';">' . $grade . '</td>
    <td class="score-cell">' . $gradeScores[$grade] . '</td>
    <td style="font-size:9pt; color:#c00;">' . htmlspecialchars($result->note ?? '') . '</td>
    <td></td>
    <td style="padding:4px;">' . (function () use ($result) {
          $imgs = (array)($result->image_path ?? []);
          if (empty($imgs)) return '<span style="color:#999;">—</span>';
          $count = count($imgs);
          $size = $count === 1 ? 250 : ($count === 2 ? 130 : ($count <= 4 ? 90 : 65));
          $out = '<div style="display:table; border-collapse:separate; border-spacing:4px;">';
          foreach (array_chunk($imgs, 2) as $row) {
            $out .= '<div style="display:table-row;">';
            foreach ($row as $path) {
              $url = asset($path);
              $out .= '<div style="display:table-cell; padding:2px;"><img src="' . htmlspecialchars($url) . '" width="' . $size . '" height="' . $size . '" style="display:block; border:1px solid #bbb; border-radius:2px; object-fit:cover;"></div>';
            }
            $out .= '</div>';
          }
          $out .= '</div>';
          return $out;
        })() . '</td>
    <td style="padding:4px; vertical-align:top;">' . (function () use ($result) {
          if (!$result->improvement_note) return '';
          $out = '<div style="margin-bottom:8px;"><b>' . htmlspecialchars($result->improver->name ?? 'Unknown') . ':</b> ' . nl2br(htmlspecialchars($result->improvement_note)) . '</div>';
          $imgs = (array)($result->improvement_image_path ?? []);
          if (!empty($imgs)) {
            $count = count($imgs);
            $size = $count === 1 ? 250 : ($count === 2 ? 130 : ($count <= 4 ? 90 : 65));
            $out .= '<div style="display:table; border-collapse:separate; border-spacing:4px;">';
            foreach (array_chunk($imgs, 2) as $row) {
              $out .= '<div style="display:table-row;">';
              foreach ($row as $path) {
                $url = asset($path);
                $out .= '<div style="display:table-cell; padding:2px;"><img src="' . htmlspecialchars($url) . '" width="' . $size . '" height="' . $size . '" style="display:block; border:1px solid #bbb; border-radius:2px; object-fit:cover;"></div>';
              }
              $out .= '</div>';
            }
            $out .= '</div>';
          }
          return $out;
        })() . '</td>
  </tr>';
      }
    }

    $html .= '
  <tr style="background:#1F2937; color:#fff; font-weight:bold;">
    <td colspan="4" style="text-align:right; padding-right:10px; color:#fff;">TỔNG ĐIỂM</td>
    <td class="score-cell" style="color:#fff; font-size:14pt;">' . $record->score . '</td>
    <td style="color:#fff;">/ ' . $record->max_score . ' (' . $pct . '%)</td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
</table>
</body></html>';

    return response($html, 200, [
      'Content-Type'        => 'application/vnd.ms-excel; charset=utf-8',
      'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
      'Pragma'              => 'no-cache',
      'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
    ]);
  }
  public function storeImprovement(Request $request, SevenSResult $result)
  {
    $user = auth()->user();
    if (!$user->hasRole('admin') && !($user->hasRole('7s') && $user->managed_department === 'XNK')) {
      abort(403);
    }
    if ($result->grade === 'B') {
      return back()->with('error', 'Chỉ cải thiện được các mục không đạt loại B.');
    }
    $request->validate([
      'improvement_note' => 'required|string',
      'improvement_images.*' => 'image|max:5120',
    ]);
    $imagePaths = $result->improvement_image_path ?? [];
    if ($request->hasFile('improvement_images')) {
      foreach ($request->file('improvement_images') as $file) {
        $path = $file->store('uploads/seven_s_improvements', 'public');
        $imagePaths[] = 'storage/' . $path;
      }
    }
    $result->update([
      'improvement_note' => $request->improvement_note,
      'improvement_image_path' => $imagePaths,
      'improver_id' => $user->id,
      'improved_at' => now(),
    ]);
    return back()->with('success', 'Đã lưu nội dung cải thiện thành công.');
  }

  // Batch improvement for all non-B items in a record (like Audit)
  public function storeImprovements(Request $request, SevenSRecord $record)
  {
    $user = auth()->user();
    if (!$user->hasRole('admin') && !($user->hasRole('7s') && $user->managed_department === 'XNK')) {
      abort(403);
    }

    $request->validate([
      'improvements' => 'required|array',
      'improvements.*.improvement_note' => 'required|string',
    ]);

    foreach ($request->input('improvements') as $resultId => $data) {
      $result = SevenSResult::find($resultId);
      if (!$result || $result->record_id !== $record->id || $result->grade === 'B') {
        continue;
      }
      $imagePaths = $result->improvement_image_path ?? [];
      if ($request->hasFile("improvements.{$resultId}.improvement_images")) {
        foreach ($request->file("improvements.{$resultId}.improvement_images") as $file) {
          $path = $file->store('uploads/seven_s_improvements', 'public');
          $imagePaths[] = 'storage/' . $path;
        }
      }
      $result->update([
        'improvement_note' => $data['improvement_note'],
        'improvement_image_path' => $imagePaths,
        'improver_id' => $user->id,
        'improved_at' => now(),
      ]);
    }

    return back()->with('success', 'Đã lưu nội dung cải thiện thành công.');
  }

  /* Xoá phiếu kiểm tra — chỉ Admin */
  public function destroy($id)
  {
    if (!auth()->user()->hasRole('admin')) {
      abort(403);
    }

    $record = SevenSRecord::with('results')->findOrFail($id);
    $record->results()->delete();
    $record->delete();

    return redirect()->route('seven-s.index')
      ->with('success', "Đã xoá phiếu kiểm tra 7S #{$id} thành công.");
  }
}
