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

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Candidate::with('seniorManagers')->latest();

        // Scope to assigned candidates for senior_manager
        if ($user->hasRole('senior_manager')) {
            $query->whereHas('seniorManagers', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
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

        $candidates = $query->paginate(20)->withQueryString();

        return view('candidates.index', compact('candidates'));
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

        Candidate::create($validated);

        return redirect()->route('candidates.index')
            ->with('success', __('messages.candidate_submit_success'));
    }

    public function show($id)
    {
        $candidate = Candidate::with('seniorManagers')->findOrFail($id);

        // Access control for senior manager
        if (auth()->user()->hasRole('senior_manager')) {
            if (!$candidate->seniorManagers->contains(auth()->id())) {
                abort(403, 'Bạn không được phép truy cập hồ sơ này.');
            }
        }

        $seniorManagers = [];
        if (auth()->user()->hasAnyRole(['admin', 'hr'])) {
            $seniorManagers = \App\Models\User::role('senior_manager')->get();
        }

        $allSeniorManagers = \App\Models\User::role('senior_manager')->get();

        return view('candidates.show', compact('candidate', 'seniorManagers', 'allSeniorManagers'));
    }

    public function destroy($id)
    {
        $candidate = Candidate::findOrFail($id);

        if ($candidate->photo_path) {
            $physicalPath = str_replace('storage/', '', ltrim($candidate->photo_path, '/'));
            Storage::disk('public')->delete($physicalPath);
        }

        $candidate->delete();

        return redirect()->route('candidates.index')
            ->with('success', __('messages.candidate_deleted'));
    }

    public function exportPrint($id)
    {
        $candidate = Candidate::with('seniorManagers')->findOrFail($id);

        // Access control for senior manager
        if (auth()->user()->hasRole('senior_manager')) {
            if (!$candidate->seniorManagers->contains(auth()->id())) {
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
            'senior_manager_ids' => ['nullable', 'array'],
            'senior_manager_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $candidate->seniorManagers()->sync($request->input('senior_manager_ids', []));

        return back()->with('success', 'Đã chuyển đơn ứng tuyển thành công.');
    }

    public function saveReview(Request $request, $id)
    {
        // Only the assigned senior_manager or admin can review
        $user = auth()->user();
        $candidate = Candidate::findOrFail($id);

        $isAssigned = $candidate->seniorManagers->contains($user->id);
        abort_unless($user->isAdminUser() || ($user->hasRole('senior_manager') && $isAssigned), 403);

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
        abort_unless($user->isAdminUser() || ($user->hasRole('senior_manager') && $isAssigned), 403);

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
            'id_number'            => 'nullable|string|max:20',
            'education'            => 'nullable|string|max:255',
            'language_skills'      => 'nullable|string|max:255',
            'department_applied'   => 'nullable|string|max:255',
            'position_applied'     => 'required|string|max:255',
            'phone'                => 'required|string|max:20',
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
            'emergency_phone'      => 'nullable|string|max:20',
            'expected_salary'      => 'nullable|string|max:50',
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
