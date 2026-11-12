<div class="d-flex align-items-center gap-2">
    <button type="submit" name="submit_action" value="save" formnovalidate class="btn btn-warning text-dark py-3 px-3 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 flex-shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
            <polyline points="17 21 17 13 7 13 7 21" />
            <polyline points="7 3 7 8 15 8" />
        </svg>
        <span>{{ __('messages.save_draft_btn') }}</span>
    </button>
    <button type="submit" name="submit_action" value="complete" class="btn btn-primary py-3 px-3 rounded-pill fw-bold shadow-lg d-flex align-items-center justify-content-center gap-2 flex-grow-1">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
            <polyline points="22 4 12 14.01 9 11.01" />
        </svg>
        <span>{{ __('messages.save_ticket_btn') }}</span>
    </button>
</div>

