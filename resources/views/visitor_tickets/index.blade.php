@php
    $maxWidth = '100%';
@endphp
@extends('layouts.app-simple')
@section('title', __('messages.visitor_tickets'))

@section('content')
@php $currentUser = auth()->user(); @endphp

<div class="d-flex align-items-center justify-content-between mb-3 mb-md-4 gap-2 flex-wrap">
    <div>
        <h4 class="fw-bold mb-1 fs-5 fs-md-4 d-flex align-items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            {{ __('messages.visitor_tickets') }}
        </h4>
        <div class="text-muted small d-none d-sm-block">{{ __('messages.visitor_ticket_subtitle') }}</div>
    </div>

    @if(\App\Support\FeatureAccess::allows($currentUser, 'visitors.create'))
    <a href="{{ route('visitor-tickets.create') }}" class="btn btn-primary btn-sm btn-md d-flex align-items-center gap-2 rounded-pill px-3 py-2 shadow-sm fw-semibold">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        <span>{{ __('messages.create_new_ticket') }}</span>
    </a>
    @endif
</div>

{{-- Search & Filter --}}
<form method="GET" action="{{ route('visitor-tickets.index') }}" class="card border-0 shadow-sm rounded-4 mb-3 mb-md-4">
    <div class="card-body p-3">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-3">
                <label class="form-label small fw-semibold mb-1">{{ __('messages.search') }}</label>
                <input type="text" class="form-control form-control-sm" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.search_placeholder') }}">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small fw-semibold mb-1">{{ __('messages.visitor_type') }}</label>
                <select class="form-select form-select-sm" name="visitor_type">
                    <option value="">{{ __('messages.all') }}</option>
                    @foreach(\App\Models\VisitorTicket::visitorTypeOptions() as $val => $transKey)
                        <option value="{{ $val }}" @selected(request('visitor_type') === $val)>{{ __($transKey) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold mb-1">{{ __('messages.visit_date') }}</label>
                <input type="date" class="form-control form-control-sm" name="date" value="{{ request('date') }}">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small fw-semibold mb-1">{{ __('messages.status_filter') }}</label>
                <select class="form-select form-select-sm" name="status">
                    <option value="">{{ __('messages.all') }}</option>
                    <option value="open" @selected(request('status') === 'open')>{{ __('messages.status_open') }}</option>
                    <option value="closed" @selected(request('status') === 'closed')>{{ __('messages.status_closed') }}</option>
                </select>
            </div>
            <div class="col-6 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-fill rounded-3 py-2 fw-semibold">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="me-1"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    {{ __('messages.filter_button') }}
                </button>
                @if(request()->hasAny(['search','visitor_type','date','status']))
                <a href="{{ route('visitor-tickets.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 py-2 px-3">✕</a>
                @endif
            </div>
        </div>
    </div>
</form>

{{-- Table Desktop / Card Mobile --}}
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        @if($tickets->isEmpty())
            <div class="text-center py-5 text-muted">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-3 opacity-25"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                <div class="fw-semibold">{{ __('messages.no_visitor_tickets') }}</div>
                <div class="small">{{ __('messages.no_visitor_tickets_hint') }}</div>
            </div>
        @else
            {{-- Desktop Table --}}
            <div class="table-responsive d-none d-lg-block">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light border-bottom">
                        <tr class="small text-uppercase text-secondary fw-semibold">
                            <th class="px-4 py-3" style="width:60px">{{ __('messages.stt') }}</th>
                            <th class="py-3">{{ __('messages.guest_unit') }}</th>
                            <th class="py-3">{{ __('messages.purpose') }}</th>
                            <th class="py-3">{{ __('messages.visit_date') }}</th>
                            <th class="py-3 text-center">{{ __('messages.guest_list') }}</th>
                            <th class="py-3 text-center">{{ __('messages.status_filter') }}</th>
                            <th class="py-3">{{ __('messages.created_by') }}</th>
                            <th class="py-3 pe-4 text-end"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                        <tr class="border-bottom">
                            <td class="px-4 text-muted small">{{ $loop->iteration + ($tickets->currentPage() - 1) * $tickets->perPage() }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $ticket->guest_unit }}</div>
                                @if($ticket->visitor_type)
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2 py-0 fw-normal" style="font-size:0.75rem;">
                                        {{ $ticket->visitor_type_label }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width:260px" title="{{ $ticket->purpose }}">{{ $ticket->purpose }}</div>
                            </td>
                            <td class="small text-muted">
                                <div>{{ $ticket->visit_date->format('d/m/Y') }}</div>
                                @if($ticket->visit_time)
                                    <span class="badge bg-light border text-secondary fw-normal px-1 py-0" style="font-size:0.75rem;">{{ $ticket->visit_time }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">
                                    {{ __('messages.guest_count_people', ['count' => $ticket->guests->count()]) }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($ticket->isOpen())
                                    <span class="badge rounded-pill" style="background:#dcfce7;color:#15803d;font-size:0.78rem;">
                                        ● {{ __('messages.status_open') }}
                                    </span>
                                @else
                                    <span class="badge rounded-pill" style="background:#f1f5f9;color:#64748b;font-size:0.78rem;">
                                        ✓ {{ __('messages.status_closed') }}
                                    </span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $ticket->creator->name ?? '—' }}</td>
                            <td class="pe-4 text-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <a href="{{ route('visitor-tickets.show', $ticket->id) }}" class="btn btn-sm btn-outline-primary rounded-3 px-3 d-inline-flex align-items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        {{ __('messages.view_ticket_action') }}
                                    </a>
                                    @if($ticket->canEdit($currentUser))
                                    <a href="{{ route('visitor-tickets.edit', $ticket->id) }}" class="btn btn-sm btn-outline-secondary rounded-3 px-2 d-inline-flex align-items-center gap-1" title="{{ __('messages.edit') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </a>
                                    @endif
                                    @if($ticket->canDelete($currentUser))
                                    <form action="{{ route('visitor-tickets.destroy', $ticket->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.delete_ticket_confirm') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-3 px-2 d-inline-flex align-items-center gap-1" title="{{ __('messages.delete') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="d-block d-lg-none p-3">
                <div class="d-flex flex-column gap-3">
                    @foreach($tickets as $ticket)
                    <div class="card border rounded-4 shadow-sm overflow-hidden">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="text-secondary small fw-semibold">#{{ $ticket->id }}</span>
                                    <h6 class="fw-bold mb-0 text-dark mt-1">{{ $ticket->guest_unit }}</h6>
                                    @if($ticket->visitor_type)
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2 py-0 fw-normal mt-1" style="font-size:0.75rem;">
                                            {{ $ticket->visitor_type_label }}
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    @if($ticket->isOpen())
                                        <span class="badge rounded-pill px-2 py-1" style="background:#dcfce7;color:#15803d;font-size:0.78rem;">
                                            ● {{ __('messages.status_open') }}
                                        </span>
                                    @else
                                        <span class="badge rounded-pill px-2 py-1" style="background:#f1f5f9;color:#64748b;font-size:0.78rem;">
                                            ✓ {{ __('messages.status_closed') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <p class="text-secondary small mb-3 text-truncate" style="max-width: 100%;">
                                <span class="fw-semibold text-dark">{{ __('messages.purpose') }}:</span> {{ $ticket->purpose }}
                            </p>

                            <div class="bg-light rounded-3 p-2 d-flex justify-content-between text-muted small mb-3">
                                <div>📅 {{ $ticket->visit_date->format('d/m/Y') }}{{ $ticket->visit_time ? ' ' . $ticket->visit_time : '' }}</div>
                                <div>👥 <span class="fw-bold text-dark">{{ $ticket->guests->count() }}</span> {{ __('messages.guest_item') }}</div>
                                <div>👤 {{ $ticket->creator->name ?? '—' }}</div>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('visitor-tickets.show', $ticket->id) }}" class="btn btn-primary flex-fill rounded-3 py-2 fw-bold d-flex align-items-center justify-content-center gap-2 tap">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    {{ __('messages.ticket_detail_btn') }}
                                </a>
                                @if($ticket->canEdit($currentUser))
                                <a href="{{ route('visitor-tickets.edit', $ticket->id) }}" class="btn btn-outline-secondary rounded-3 px-3 py-2 d-flex align-items-center justify-content-center tap" title="{{ __('messages.edit') }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>
                                @endif
                                @if($ticket->canDelete($currentUser))
                                <form action="{{ route('visitor-tickets.destroy', $ticket->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.delete_ticket_confirm') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger rounded-3 px-3 py-2 d-flex align-items-center justify-content-center tap" title="{{ __('messages.delete') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            @if($tickets->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $tickets->links() }}
            </div>
            @endif
        @endif
    </div>
</div>
@endsection
