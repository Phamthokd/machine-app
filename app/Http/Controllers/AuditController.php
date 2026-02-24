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

        return redirect('/audits')->with('success', "Đã đánh giá thành công bộ phận {$template->department_name}!");
    }

    public function show($id)
    {
        $audit = AuditRecord::with(['template', 'auditor', 'results.criterion'])->findOrFail($id);
        
        return view('audits.show', compact('audit'));
    }

}
