@extends('layouts.app-simple', ['maxWidth' => '100%'])
@section('title', __('messages.repair_requests_title'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="/dashboard" class="btn btn-light rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h4 class="mb-0 fw-bold">{{ __('messages.repair_requests_title') }}</h4>
            <div class="text-secondary small">{{ __('messages.repair_requests_subtitle') }}</div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="min-width: 800px; font-size: 0.9rem;">
            <thead class="bg-light text-secondary">
                <tr class="text-uppercase text-xs fw-bold">
                    <th class="py-3 px-3">{{ __('messages.machine_code_header') }}</th>
                    <th class="py-3 px-3">{{ __('messages.machine_name_header') }}</th>
                    <th class="py-3 px-3">{{ __('messages.dept_header') }}</th>
                    <th class="py-3 px-3">{{ __('messages.issue_header') }}</th>
                    <th class="py-3 px-3">{{ __('messages.reporter_header') }}</th>
                    <th class="py-3 px-3">{{ __('messages.report_time_header') }}</th>
                    <th class="py-3 px-3 text-end">{{ __('messages.action_header') }}</th>
                </tr>
            </thead>
            <tbody class="border-top-0">
                @forelse($requests as $r)
                <tr>
                    <td class="px-3 fw-bold text-primary">{{ $r->machine->ma_thiet_bi ?? '—' }}</td>
                    <td class="px-3">{{ $r->machine->ten_thiet_bi ?? '—' }}</td>
                    <td class="px-3">
                        <span class="badge bg-light text-secondary border">{{ $r->machine->department->name ?? '—' }}</span>
                    </td>
                    <td class="px-3 text-wrap" style="max-width: 300px;">
                        {{ $r->nguyen_nhan }}
                    </td>
                    <td class="px-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-sm rounded-circle bg-light text-secondary d-flex align-items-center justify-content-center fw-bold" style="width: 30px; height: 30px; font-size: 0.75rem;">
                                {{ substr($r->createdBy->name ?? 'U', 0, 1) }}
                            </div>
                            <span class="fw-medium">{{ $r->createdBy->name ?? __('messages.unknown_user') }}</span>
                        </div>
                    </td>
                    <td class="px-3 text-secondary">
                        {{ $r->created_at->format('H:i d/m/Y') }}
                    </td>
                    <td class="px-3 text-end">
                        <div class="d-flex align-items-center justify-content-end gap-2">
                            @if(auth()->user()->hasAnyRole(['team_leader', 'audit']))
                                @if($r->mechanic_id)
                                    @php
                                    $parts = explode(' ', $r->mechanic->name ?? __('messages.unknown_user'));
                                    $shortName = end($parts);
                                    @endphp
                                    <span class="badge bg-warning text-dark border px-3 py-2" title="{{ $r->mechanic->name ?? '' }}">
                                        🔒 {{ __('messages.being_repaired_by') }} {{ $shortName }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-secondary border px-3 py-2">{{ __('messages.only_view') }}</span>
                                @endif
                            @else
                                @if($r->mechanic_id)
                                    @if($r->mechanic_id === auth()->id())
                                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 accept-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#scanAcceptModal"
                                            data-machine-code="{{ $r->machine->ma_thiet_bi }}"
                                            data-redirect-url="/repairs/{{ $r->id }}/edit">
                                            {{ __('messages.process_resume_btn') }}
                                        </button>
                                    @else
                                        @php
                                        $parts = explode(' ', $r->mechanic->name ?? __('messages.unknown_user'));
                                        $shortName = end($parts);
                                        @endphp
                                        <span class="badge bg-warning text-dark border px-3 py-2" title="{{ $r->mechanic->name ?? '' }}">
                                            🔒 {{ __('messages.being_repaired_by') }} {{ $shortName }}
                                        </span>
                                    @endif
                                @else
                                    <form action="{{ route('repairs.accept', $r->id) }}" id="accept-form-{{ $r->id }}" method="POST" class="d-inline">
                                        @csrf
                                    </form>
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 accept-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#scanAcceptModal"
                                        data-machine-code="{{ $r->machine->ma_thiet_bi }}"
                                        data-form-id="accept-form-{{ $r->id }}">
                                        {{ __('messages.process_btn') }}
                                    </button>
                                @endif
                            @endif

                            @if(auth()->user()->isAdminUser())
                            <form action="/repairs/{{ $r->id }}" method="POST" onsubmit="return confirm('{{ __('messages.delete_confirm') }}');" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3">
                                    {{ __('messages.delete_btn') }}
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-secondary">
                        <div class="mb-3 opacity-50">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </div>
                        {{ __('messages.no_requests') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Scanner Modal -->
<div class="modal fade" id="scanAcceptModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" class="me-2 text-primary" stroke="currentColor" stroke-width="2.5">
                        <path d="M4 4h4v4H4z" />
                        <path d="M4 16h4v4H4z" />
                        <path d="M16 4h4v4h-4z" />
                        <rect x="14" y="14" width="6" height="6" />
                    </svg>
                    {{ __('messages.scan_to_accept_title') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pt-2">
                <p class="text-muted small mb-3">{{ __('messages.scan_to_accept_desc') }}</p>
                <div class="fw-bold text-dark mb-3" style="font-size: 1.1rem;" id="targetMachineLabel"></div>

                <div id="acceptQrReader" class="mx-auto rounded-3 overflow-hidden shadow-sm border" style="width: 100%; max-width: 300px;"></div>

                <div id="acceptScanError" class="alert alert-danger mt-3 mb-0 d-none text-start small">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    <span>{{ __('messages.scan_to_accept_error') }}</span>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let acceptHtml5QrCode = null;
        let targetMachineCode = '';
        let targetFormId = '';
        let targetRedirectUrl = '';
        const scanModal = document.getElementById('scanAcceptModal');
        const errorDiv = document.getElementById('acceptScanError');
        const machineLabel = document.getElementById('targetMachineLabel');

        function stopScanner() {
            if (acceptHtml5QrCode && acceptHtml5QrCode.isScanning) {
                acceptHtml5QrCode.stop().then(() => {
                    acceptHtml5QrCode.clear();
                }).catch(err => console.log('Stop error', err));
            }
        }

        scanModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            targetMachineCode = button.getAttribute('data-machine-code');
            targetFormId = button.getAttribute('data-form-id');
            targetRedirectUrl = button.getAttribute('data-redirect-url');
            machineLabel.textContent = targetMachineCode;
            errorDiv.classList.add('d-none');

            // Initialize scanner
            setTimeout(() => {
                if (!acceptHtml5QrCode) {
                    acceptHtml5QrCode = new Html5Qrcode("acceptQrReader");
                }

                Html5Qrcode.getCameras().then(cameras => {
                    if (cameras && cameras.length > 0) {
                        const backCam = cameras.find(c => (c.label || '').toLowerCase().includes('back')) || cameras[cameras.length - 1];

                        acceptHtml5QrCode.start({
                                deviceId: {
                                    exact: backCam.id
                                }
                            }, {
                                fps: 10,
                                qrbox: {
                                    width: 220,
                                    height: 220
                                },
                                aspectRatio: 1.0
                            },
                            (decodedText) => {
                                // Normalize code
                                let scannedCode = decodedText.trim();
                                try {
                                    if (scannedCode.startsWith('http')) {
                                        const parts = new URL(scannedCode).pathname.split('/').filter(Boolean);
                                        scannedCode = parts[parts.length - 1] || scannedCode;
                                    }
                                } catch (e) {}

                                if (scannedCode === targetMachineCode) {
                                    // Match! Stop scanner and proceed
                                    stopScanner();
                                    if (targetRedirectUrl) {
                                        window.location.href = targetRedirectUrl;
                                    } else if (targetFormId) {
                                        document.getElementById(targetFormId).submit();
                                    }
                                } else {
                                    // Mismatch
                                    errorDiv.classList.remove('d-none');
                                    // Hide error after 3 seconds
                                    setTimeout(() => errorDiv.classList.add('d-none'), 3000);
                                }
                            },
                            (errorMessage) => {}
                        ).catch(err => console.error(err));
                    }
                }).catch(err => console.error(err));

            }, 500); // UI render delay
        });

        scanModal.addEventListener('hidden.bs.modal', function() {
            stopScanner();
            targetMachineCode = '';
            targetFormId = '';
            targetRedirectUrl = '';
        });
    });
</script>
@endsection
