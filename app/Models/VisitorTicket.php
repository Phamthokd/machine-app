<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitorTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'guest_unit',
        'purpose',
        'visit_date',
        'status',
        'created_by',
        'closed_by',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
            'closed_at'  => 'datetime',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────────────

    public function guests(): HasMany
    {
        return $this->hasMany(VisitorGuest::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function closer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    // ─── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    // ─── Helpers ────────────────────────────────────────────────────────────────

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }
}
