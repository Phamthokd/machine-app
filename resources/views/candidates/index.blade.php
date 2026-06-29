@extends('layouts.app-simple', ['maxWidth' => '1100px'])
@section('title', __('messages.candidates'))

@section('content')
<style>
    .candidate-card {
        background: white;
        border-radius: 1rem;
        border: 1px solid #f1f5f9;
        transition: all 0.2s;
    }
    .candidate-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0,0,0,.06);
    }
    .avatar-circle {
        width: 44px; height: 44px; border-radius: 50%;
        background: linear-gradient(135deg, #4f46e5, #2563eb);
        color: white; font-weight: 700; font-size: 1.1rem;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .avatar-circle img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
    .badge-pos { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: .75rem; border-radius: .4rem; padding: .2rem .55rem; font-weight: 600; }
    .badge-gender-m { background: #eff6ff; color: #1e40af; }
    .badge-gender-f { background: #fdf2f8; color: #9d174d; }
</style>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">{{ __('messages.candidates') }}</h2>
        <p class="text-muted small mb-0">{{ __('messages.candidates_subtitle') }}</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('candidates.create') }}" class="btn btn-primary rounded-3 fw-bold px-4 d-flex align-items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            {{ __('messages.candidate_add') }}
        </a>
    </div>
</div>

{{-- Filter --}}
<form method="GET" class="card border-0 shadow-sm rounded-3 p-3 mb-4">
    <div class="row g-2 align-items-end">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control rounded-3" placeholder="{{ __('messages.search_name_phone_position') }}" value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <input type="date" name="start_date" class="form-control rounded-3" value="{{ request('start_date') }}">
        </div>
        <div class="col-md-3">
            <input type="date" name="end_date" class="form-control rounded-3" value="{{ request('end_date') }}">
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-primary w-100 rounded-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            </button>
        </div>
    </div>
</form>

@forelse($candidates as $c)
<div class="candidate-card p-3 mb-3 d-flex align-items-center gap-3">
    {{-- Avatar --}}
    <div class="avatar-circle">
        @if($c->photo_path)
            <img src="/{{ $c->photo_path }}" alt="{{ $c->full_name }}">
        @else
            {{ mb_substr($c->full_name, 0, 1) }}
        @endif
    </div>
    {{-- Info --}}
    <div class="flex-grow-1 min-w-0">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="fw-bold text-dark">{{ $c->full_name }}</span>
            <span class="badge {{ $c->gender === 'male' ? 'badge-gender-m' : 'badge-gender-f' }}">
                {{ $c->gender === 'male' ? '♂ ' . __('messages.gender_male') : '♀ ' . __('messages.gender_female') }}
            </span>
        </div>
        <div class="d-flex flex-wrap gap-2 mt-1">
            <span class="badge-pos">{{ $c->position_applied }}</span>
            <span class="text-muted small">📞 {{ $c->phone }}</span>
            @if($c->dob)
            <span class="text-muted small">🎂 {{ $c->dob->format('d/m/Y') }}</span>
            @endif
        </div>
    </div>
    {{-- Date + Actions --}}
    <div class="text-end flex-shrink-0">
        <div class="text-muted small mb-2">{{ $c->created_at->format('d/m/Y') }}</div>
        <div class="d-flex gap-1 justify-content-end">
            <a href="{{ route('candidates.show', $c->id) }}" class="btn btn-sm btn-outline-primary rounded-2 px-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </a>
            <a href="{{ route('candidates.print', $c->id) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-2 px-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            </a>
            @if(auth()->user()->isAdminUser())
            <form action="{{ route('candidates.destroy', $c->id) }}" method="POST"
                  onsubmit="return confirm('{{ __('messages.candidate_delete_confirm') }}')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger rounded-2 px-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
@empty
<div class="text-center py-5">
    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
    <div class="text-muted mt-3">{{ __('messages.no_candidates') }}</div>
    <a href="{{ route('candidates.create') }}" class="btn btn-primary rounded-3 mt-3">{{ __('messages.candidate_add') }}</a>
</div>
@endforelse

{{ $candidates->links() }}

@endsection
