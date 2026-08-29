<?php

namespace App\Http\Controllers;

use App\Models\VisitorTicket;
use App\Models\VisitorGuest;
use App\Support\FeatureAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class VisitorTicketController extends Controller
{
    // ─── INDEX ───────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = VisitorTicket::with(['guests', 'creator'])->latest();

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo ngày
        if ($request->filled('date')) {
            $query->whereDate('visit_date', $request->date);
        }

        // Tìm kiếm theo đơn vị hoặc mục đích
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('guest_unit', 'like', "%$search%")
                  ->orWhere('purpose', 'like', "%$search%")
                  ->orWhereHas('guests', function ($q2) use ($search) {
                      $q2->where('full_name', 'like', "%$search%")
                         ->orWhere('id_number', 'like', "%$search%")
                         ->orWhere('guest_card_number', 'like', "%$search%");
                  });
            });
        }

        $tickets = $query->paginate(20)->appends($request->all());

        return view('visitor_tickets.index', compact('tickets'));
    }

    // ─── CREATE ──────────────────────────────────────────────────────────────────

    public function create()
    {
        return view('visitor_tickets.create');
    }

    // ─── STORE ───────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'guest_unit'          => ['required', 'string', 'max:255'],
            'purpose'             => ['required', 'string', 'max:500'],
            'visit_date'          => ['required', 'date'],
            'guests'              => ['required', 'array', 'min:1'],
            'guests.*.full_name'  => ['required', 'string', 'max:255'],
            'guests.*.id_number'  => ['nullable', 'string', 'max:50'],
            'guests.*.guest_card_number' => ['nullable', 'string', 'max:50'],
        ], [
            'guest_unit.required'         => 'Vui lòng nhập đơn vị khách.',
            'purpose.required'            => 'Vui lòng nhập mục đích vào công ty.',
            'visit_date.required'         => 'Vui lòng chọn ngày.',
            'guests.required'             => 'Vui lòng thêm ít nhất 1 khách.',
            'guests.min'                  => 'Vui lòng thêm ít nhất 1 khách.',
            'guests.*.full_name.required' => 'Họ và tên khách là bắt buộc.',
        ]);

        $ticket = VisitorTicket::create([
            'guest_unit'  => $request->guest_unit,
            'purpose'     => $request->purpose,
            'visit_date'  => $request->visit_date,
            'status'      => 'open',
            'created_by'  => auth()->id(),
        ]);

        foreach ($request->guests as $guestData) {
            if (empty(trim($guestData['full_name'] ?? ''))) {
                continue;
            }
            $ticket->guests()->create([
                'full_name'        => $guestData['full_name'],
                'id_number'        => $guestData['id_number'] ?? null,
                'guest_card_number' => $guestData['guest_card_number'] ?? null,
            ]);
        }

        return redirect()->route('visitor-tickets.show', $ticket->id)
            ->with('success', 'Tạo phiếu đăng ký khách thành công!');
    }

    // ─── SHOW ────────────────────────────────────────────────────────────────────

    public function show(int $id)
    {
        $ticket = VisitorTicket::with(['guests.processor', 'creator', 'closer'])->findOrFail($id);
        $canSecurity = FeatureAccess::allows(auth()->user(), 'visitors.security');
        $canCreate   = FeatureAccess::allows(auth()->user(), 'visitors.create');

        return view('visitor_tickets.show', compact('ticket', 'canSecurity', 'canCreate'));
    }

    // ─── SECURITY UPDATE (bảo vệ cập nhật hành lý, giờ vào/ra, ghi chú) ────────

    public function securityUpdate(Request $request, int $id)
    {
        $ticket = VisitorTicket::findOrFail($id);
        abort_if($ticket->isClosed(), 403, 'Phiếu đã đóng, không thể chỉnh sửa.');

        $request->validate([
            'guests'                        => ['required', 'array'],
            'guests.*.id'                   => ['required', 'integer', 'exists:visitor_guests,id'],
            'guests.*.baggage_checked'      => ['nullable', 'in:0,1,'],
            'guests.*.checked_in_at'        => ['nullable', 'date_format:Y-m-d\TH:i'],
            'guests.*.checked_out_at'       => ['nullable', 'date_format:Y-m-d\TH:i'],
            'guests.*.note'                 => ['nullable', 'string', 'max:500'],
        ]);

        foreach ($request->guests as $guestData) {
            $guest = VisitorGuest::where('id', $guestData['id'])
                ->where('visitor_ticket_id', $ticket->id)
                ->first();

            if (!$guest) continue;

            $update = [
                'processed_by'   => auth()->id(),
            ];

            // Kiểm tra hành lý: chỉ cập nhật nếu được gửi lên
            if (isset($guestData['baggage_checked']) && $guestData['baggage_checked'] !== '') {
                $update['baggage_checked'] = (bool) $guestData['baggage_checked'];
            }

            if (!empty($guestData['checked_in_at'])) {
                $update['checked_in_at'] = Carbon::parse($guestData['checked_in_at']);
            }

            if (!empty($guestData['checked_out_at'])) {
                $update['checked_out_at'] = Carbon::parse($guestData['checked_out_at']);
            }

            if (isset($guestData['note'])) {
                $update['note'] = $guestData['note'];
            }

            $guest->update($update);
        }

        return redirect()->route('visitor-tickets.show', $ticket->id)
            ->with('success', 'Cập nhật thông tin khách thành công!');
    }

    // ─── CLOSE ───────────────────────────────────────────────────────────────────

    public function close(int $id)
    {
        $ticket = VisitorTicket::findOrFail($id);
        abort_if($ticket->isClosed(), 403, 'Phiếu đã được đóng trước đó.');

        $ticket->update([
            'status'    => 'closed',
            'closed_by' => auth()->id(),
            'closed_at' => now(),
        ]);

        return redirect()->route('visitor-tickets.show', $ticket->id)
            ->with('success', 'Phiếu đã được đóng thành công!');
    }
}
