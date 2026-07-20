<?php

namespace App\Http\Controllers;

use App\Models\ItRepair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItRepairController extends Controller
{
    // ─── Index ───────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = ItRepair::with(['reporter', 'resolver', 'machine.department'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('issue_type')) {
            $query->where('issue_type', $request->issue_type);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('department_id')) {
            $dept = \App\Models\Department::find($request->department_id);
            $query->where(function ($q) use ($request, $dept) {
                $q->whereHas('machine', function ($mq) use ($request) {
                    $mq->where('current_department_id', $request->department_id);
                });
                if ($dept) {
                    $q->orWhere('department', $dept->name);
                }
            });
        }
        
        $startDate = $request->input('start_date', $request->input('date_from'));
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        
        $endDate = $request->input('end_date', $request->input('date_to'));
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $tickets = $query->paginate(20)->withQueryString();

        $departments = \App\Models\Department::orderBy('name')->get();

        $stats = [
            'pending'     => ItRepair::where('status', 'pending')->count(),
            'in_progress' => ItRepair::where('status', 'in_progress')->count(),
            'resolved'    => ItRepair::whereIn('status', ['resolved', 'closed'])->count(),
            'total'       => ItRepair::count(),
        ];

        return view('it_repairs.index', compact('tickets', 'stats', 'departments'));
    }

    // ─── Create ──────────────────────────────────────────────────────────────

    public function create(Request $request)
    {
        abort_unless(auth()->user()->canManageItRepairs(), 403);

        $machine = null;
        if ($request->filled('machine')) {
            $machine = \App\Models\Machine::where('ma_thiet_bi', $request->machine)->first();
        }

        $itStaff = \App\Models\User::where('is_active', true)
            ->get()
            ->filter(fn($u) => !$u->isAdminUser() && !$u->hasRole('admin') && $u->canManageItRepairs());

        return view('it_repairs.create', compact('machine', 'itStaff'));
    }

    // ─── Store ───────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        abort_unless(auth()->user()->canManageItRepairs(), 403);

        $request->validate([
            'issue_type'       => ['required', 'in:computer,network,printer,software,phone,other'],
            'title'            => ['nullable', 'string', 'max:255'],
            'description'      => ['required', 'string'],
            'resolution_note'  => ['required', 'string'],
            'location'         => ['nullable', 'string', 'max:255'],
            'priority'         => ['nullable', 'in:low,medium,high,urgent'],
            'started_at'       => ['nullable', 'date'],
            'ended_at'         => ['nullable', 'date'],
            'nguoi_ho_tro'     => ['nullable'],
            'images'           => ['nullable', 'array'],
            'images.*'         => ['image', 'max:10240'],
        ]);

        // Upload images
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $imagePaths[] = $file->store('it_repairs', 'public');
            }
        }

        // Determine status: if resolution is provided, mark as resolved immediately
        $hasResolution = $request->filled('resolution_note');
        $title = $request->filled('title') ? $request->title : \Illuminate\Support\Str::limit($request->description, 50);

        $nguoiHoTro = is_array($request->nguoi_ho_tro) 
            ? implode(', ', array_filter($request->nguoi_ho_tro)) 
            : $request->nguoi_ho_tro;

        $ticket = ItRepair::create([
            'code'            => ItRepair::generateCode(),
            'department'      => auth()->user()->primaryManagedDepartment()
                ?? auth()->user()->managed_department,
            'reporter_id'     => auth()->id(),
            'resolver_id'     => auth()->id(),   // IT staff who fills the form IS the resolver
            'nguoi_ho_tro'    => $nguoiHoTro ?: null,
            'machine_id'      => $request->filled('machine_id') ? $request->machine_id : null,
            'issue_type'      => $request->issue_type,
            'title'           => $title,
            'description'     => $request->description,
            'resolution_note' => $request->resolution_note,
            'location'        => $request->location,
            'priority'        => $request->input('priority', 'medium'),
            'status'          => $hasResolution ? 'resolved' : 'pending',
            'resolved_at'     => $hasResolution ? now() : null,
            'started_at'      => $request->filled('started_at') ? $request->started_at : null,
            'ended_at'        => $request->filled('ended_at') ? $request->ended_at : null,
            'images'          => $imagePaths ?: null,
        ]);

        return redirect("/it-repairs/{$ticket->id}")
            ->with('success', 'Phiếu IT đã được ghi nhận! Mã phiếu: ' . $ticket->code);
    }

    // ─── Show ────────────────────────────────────────────────────────────────

    public function show(ItRepair $itRepair)
    {
        $itRepair->load(['reporter', 'resolver']);
        return view('it_repairs.show', ['ticket' => $itRepair]);
    }

    // ─── Update (IT staff updates status / resolution) ───────────────────────

    public function update(Request $request, ItRepair $itRepair)
    {
        abort_unless(auth()->user()->canManageItRepairs(), 403);

        $request->validate([
            'status'          => ['required', 'in:pending,in_progress,resolved,closed'],
            'resolution_note' => ['nullable', 'string'],
        ]);

        $data = [
            'status'          => $request->status,
            'resolution_note' => $request->resolution_note,
            'resolver_id'     => auth()->id(),
        ];

        if (in_array($request->status, ['resolved', 'closed']) && !$itRepair->resolved_at) {
            $data['resolved_at'] = now();
        }

        $itRepair->update($data);

        return back()->with('success', 'Phiếu IT đã được cập nhật thành công.');
    }

    // ─── Destroy ─────────────────────────────────────────────────────────────

    public function destroy(ItRepair $itRepair)
    {
        abort_unless(auth()->user()->isAdminUser(), 403);

        if ($itRepair->images) {
            foreach ($itRepair->images as $path) {
                Storage::disk('public')->delete($path);
            }
        }

        $itRepair->delete();

        return redirect('/it-repairs')->with('success', 'Phiếu IT đã được xóa.');
    }
}
