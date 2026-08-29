<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitorGuest extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_ticket_id',
        'full_name',
        'id_number',
        'guest_card_number',
        'baggage_checked',
        'checked_in_at',
        'checked_out_at',
        'note',
        'processed_by',
    ];

    protected function casts(): array
    {
        return [
            'baggage_checked' => 'boolean',
            'checked_in_at'   => 'datetime',
            'checked_out_at'  => 'datetime',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────────────

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(VisitorTicket::class, 'visitor_ticket_id');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // ─── Helpers ────────────────────────────────────────────────────────────────

    public function hasCheckedIn(): bool
    {
        return !is_null($this->checked_in_at);
    }

    public function hasCheckedOut(): bool
    {
        return !is_null($this->checked_out_at);
    }
}
