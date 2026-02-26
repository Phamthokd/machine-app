<?php

namespace App\Http\Controllers;

use App\Models\AuditCriterion;
use App\Models\AuditRecord;
use App\Models\AuditResult;
use App\Models\AuditTemplate;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index()
    {
        // Get list of active templates for creating new audits
        $templates = AuditTemplate::where('is_active', true)->get();
        
        // Get recent audit history
        $audits = AuditRecord::with('template', 'auditor')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('audits.index', compact('templates', 'audits'));
    }

    public function create(Request $request)
    {
        $templateId = $request->query('template_id');
        abort_unless($templateId, 400, 'Thiếu ID bộ đánh giá');

        $template = AuditTemplate::with('criteria')->findOrFail($templateId);

        return view('audits.create', compact('template'));
    }

    public function store(Request $request)
    {
        $templateId = $request->input('audit_template_id');
        $template = AuditTemplate::findOrFail($templateId);
        
        $results = $request->input('results', []);

        // Validate results
        $request->validate([
            'results' => 'required|array',
            'results.*.audit_criterion_id' => 'required|exists:audit_criteria,id',
            'results.*.is_passed' => 'required|in:1,0',
            'results.*.note' => 'required_if:results.*.is_passed,0',
            'results.*.image' => 'nullable|image|max:10240', // Max 10MB
        ], [
            'results.*.note.required_if' => 'Vui lòng nhập ghi chú nguyên nhân cho các mục Không đạt (X).',
            'results.*.image.image' => 'File đính kèm phải là hình ảnh.',
            'results.*.image.max' => 'Kích thước ảnh tối đa là 10MB.',
        ]);

        // Create the main record
        $record = AuditRecord::create([
            'audit_template_id' => $template->id,
            'auditor_id' => auth()->id(),
            'status' => 'completed',
        ]);

        // Create the individual criterion results
        foreach ($results as $index => $item) {
            $imagePath = null;
            
            // Handle image upload if provided and the criterion failed
            if (isset($item['is_passed']) && $item['is_passed'] == 0 && $request->hasFile("results.{$index}.image")) {
                $file = $request->file("results.{$index}.image");
                $filename = now()->format('Y-m-d-His-') . uniqid() . '.' . $file->extension();
                $path = $file->storeAs('audits', $filename, 'public');
                // Prefix with storage/ for asset referencing 
                $imagePath = 'storage/' . ltrim($path, '/');
            }

            AuditResult::create([
                'audit_record_id' => $record->id,
                'audit_criterion_id' => $item['audit_criterion_id'],
                'is_passed' => (bool)$item['is_passed'],
                'note' => $item['note'] ?? null,
                'image_path' => $imagePath,
            ]);
        }

        $record->load('results');
        return redirect('/audits')->with('success', "Đã đánh giá thành công bộ phận {$template->department_name}! Điểm số đạt: {$record->score}%");
    }

    public function export()
    {
        $audits = AuditRecord::with(['template', 'auditor', 'results'])
            ->orderByDesc('created_at')
            ->get();

        $headers = [
            'ID', 'Tên đánh giá', 'Tổ', 'Người đánh giá', 'Thời gian', 'Điểm số (%)', 'Tổng mục', 'Đạt', 'Lỗi'
        ];

        $renderRow = function ($a) {
            $total = $a->results->count();
            $passed = $a->results->where('is_passed', true)->count();
            $failed = $total - $passed;
            
            $cells = [
                $a->id,
                $a->template->name ?? '',
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

        $validated = $request->validate([
            'improvements' => 'required|array',
            'improvements.*.root_cause' => 'required|string',
            'improvements.*.corrective_action' => 'required|string',
            'improvements.*.improvement_deadline' => 'required|date',
        ]);

        foreach ($validated['improvements'] as $resultId => $improvementData) {
            $result = $audit->results->where('id', $resultId)->first();
            if ($result) {
                $result->update([
                    'root_cause' => $improvementData['root_cause'],
                    'corrective_action' => $improvementData['corrective_action'],
                    'improvement_deadline' => $improvementData['improvement_deadline'],
                ]);
            }
        }

        return redirect()->back()->with('success', 'Đã lưu thông tin cải thiện thành công.');
    }
}
