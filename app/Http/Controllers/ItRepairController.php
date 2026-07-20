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
        $query = ItRepair::with(['reporter', 'resolver'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('issue_type')) {
            $query->where('issue_type', $request->issue_type);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $tickets = $query->paginate(20)->withQueryString();

        $stats = [
            'pending'     => ItRepair::where('status', 'pending')->count(),
            'in_progress' => ItRepair::where('status', 'in_progress')->count(),
            'resolved'    => ItRepair::whereIn('status', ['resolved', 'closed'])->count(),
            'total'       => ItRepair::count(),
        ];

        return view('it_repairs.index', compact('tickets', 'stats'));
    }

    // ─── Create ──────────────────────────────────────────────────────────────

    public function create(Request $request)
    {
        abort_unless(auth()->user()->canManageItRepairs(), 403);

        $machine = null;
        if ($request->filled('machine')) {
            $machine = \App\Models\Machine::where('ma_thiet_bi', $request->machine)->first();
        }

        return view('it_repairs.create', compact('machine'));
    }

    // ─── Store ───────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        abort_unless(auth()->user()->canManageItRepairs(), 403);

        $request->validate([
            'issue_type'  => ['required', 'in:computer,network,printer,software,other'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'location'    => ['nullable', 'string', 'max:255'],
            'priority'    => ['required', 'in:low,medium,high,urgent'],
            'images'      => ['nullable', 'array'],
            'images.*'    => ['image', 'max:10240'],
        ]);

        // Upload images
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $imagePaths[] = $file->store('it_repairs', 'public');
            }
        }

        $ticket = ItRepair::create([
            'code'        => ItRepair::generateCode(),
            'department'  => auth()->user()->primaryManagedDepartment()
                ?? auth()->user()->managed_department,
            'reporter_id' => auth()->id(),
            'machine_id'  => $request->filled('machine_id') ? $request->machine_id : null,
            'issue_type'  => $request->issue_type,
            'title'       => $request->title,
            'description' => $request->description,
            'location'    => $request->location,
            'priority'    => $request->priority,
            'status'      => 'pending',
            'images'      => $imagePaths ?: null,
        ]);

        return redirect("/it-repairs/{$ticket->id}")
            ->with('success', 'Phiếu IT đã được tạo thành công! Mã phiếu: ' . $ticket->code);
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
