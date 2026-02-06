@extends('layouts.app-simple')
@section('title', 'Chuyển tổ - ' . $machine->ma_thiet_bi)

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="/m/{{ $machine->ma_thiet_bi }}" class="text-decoration-none text-secondary d-flex align-items-center gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Quay lại
    </a>
    <h4 class="mb-0 fw-bold">Chuyển máy sang tổ khác</h4>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        
        <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-3">
            <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
            <div>
                <div class="fw-bold fs-5">{{ $machine->ma_thiet_bi }}</div>
                <div class="text-muted small">{{ $machine->ten_thiet_bi }}</div>
            </div>
        </div>

        <form method="POST" action="/machines/{{ $machine->id }}/move">
            @csrf
            
            <div class="mb-4">
                <label class="form-label fw-medium text-secondary">Tổ hiện tại</label>
                <div class="form-control bg-light border-0 text-secondary">
                    {{ $machine->department->name }}
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Chọn tổ mới <span class="text-danger">*</span></label>
                <div class="position-relative" id="dropdown-container">
                    <!-- Search Input -->
                    <input type="text" class="form-control" id="dept-search" placeholder="Nhập tên tổ để tìm..." autocomplete="off">
                    
                    <!-- Hidden Input for Form Submission -->
                    <input type="hidden" name="department_id" id="dept-id" required>
                    
                    <!-- Dropdown List -->
                    <div class="position-absolute w-100 bg-white border rounded shadow-sm mt-1 overflow-auto d-none" id="dept-list" style="max-height: 250px; z-index: 1050;">
                        @foreach($departments as $d)
                            <div class="p-3 border-bottom dept-item cursor-pointer" data-id="{{ $d->id }}" data-name="{{ $d->name }}" style="cursor: pointer;">
                                {{ $d->name }} 
                                @if($d->id == $machine->department_id) <span class="text-muted small">(Hiện tại)</span> @endif
                            </div>
                        @endforeach
                        <div id="no-result" class="p-3 text-muted text-center d-none">Không tìm thấy tổ nào</div>
                    </div>
                </div>

                <style>
                    .dept-item:hover { background-color: #f8f9fa; }
                    .dept-item.active { background-color: #e0e7ff; color: #4338ca; font-weight: 500; }
                </style>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const input = document.getElementById('dept-search');
                        const hiddenInput = document.getElementById('dept-id');
                        const list = document.getElementById('dept-list');
                        const items = document.querySelectorAll('.dept-item');
                        const noResult = document.getElementById('no-result');
                        const currentDeptId = "{{ $machine->department_id }}";

                        // Toggle list display
                        input.addEventListener('focus', () => list.classList.remove('d-none'));
                        
                        // Close list when clicking outside
                        document.addEventListener('click', function(e) {
                            if (!document.getElementById('dropdown-container').contains(e.target)) {
                                list.classList.add('d-none');
                                // Verify validation
                                checkSelection();
                            }
                        });

                        // Filter functionality
                        input.addEventListener('input', function() {
                            const term = this.value.toLowerCase();
                            let hasResult = false;
                            list.classList.remove('d-none'); // Show list on type

                            items.forEach(item => {
                                const name = item.getAttribute('data-name').toLowerCase();
                                if (name.includes(term)) {
                                    item.classList.remove('d-none');
                                    hasResult = true;
                                } else {
                                    item.classList.add('d-none');
                                }
                            });

                            if (!hasResult) {
                                noResult.classList.remove('d-none');
                            } else {
                                noResult.classList.add('d-none');
                            }
                        });

                        // Select item
                        items.forEach(item => {
                            // Disabled current department logic handled visually, but preventing selection if STRICTLY needed
                            if (item.getAttribute('data-id') === currentDeptId) {
                                item.classList.add('bg-light', 'text-muted');
                                item.style.cursor = 'not-allowed';
                                return;
                            }

                            item.addEventListener('click', function() {
                                const name = this.getAttribute('data-name');
                                const id = this.getAttribute('data-id');
                                
                                input.value = name;
                                hiddenInput.value = id;
                                list.classList.add('d-none');
                                
                                // Reset Validation
                                input.setCustomValidity("");
                            });
                        });

                        // Validation on submit or blur
                        function checkSelection() {
                             // If input has text but no ID is set, or ID doesn't match name (basic check)
                             // Logic: Valid only if hiddenInput has value
                             if (!hiddenInput.value) {
                                // Clear input if invalid to force user to select
                                // input.value = ''; 
                                // Or use CustomValidity
                             }
                        }
                        
                        // Prevent form submit if invalid
                        document.querySelector('form').addEventListener('submit', function(e) {
                            if (!hiddenInput.value) {
                                e.preventDefault();
                                alert('Vui lòng chọn tổ từ danh sách gợi ý!');
                                input.focus();
                            }
                        });
                    });
                </script>
            
            <div class="mb-4">
                <label class="form-label fw-bold">Ghi chú vị trí (Optional)</label>
                <textarea class="form-control" name="note" rows="3" placeholder="Ví dụ: Chuyển cho chuyền 2 mượn...">{{ $machine->vi_tri_text }}</textarea>
                <div class="form-text">Ghi chú này sẽ cập nhật vào trường "Vị trí" của máy.</div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow-sm tap">
                XÁC NHẬN CHUYỂN
            </button>
        </form>
    </div>
</div>
@endsection
