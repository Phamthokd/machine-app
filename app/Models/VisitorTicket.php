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
        'visitor_type',
        'purpose',
        'visit_date',
        'visit_time',
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

    public function canDelete(?User $user = null): bool
    {
        $user = $user ?? auth()->user();
        if (!$user) {
            return false;
        }

        return $user->isAdminUser() || (int) $this->created_by === (int) $user->id;
    }

    public function isSecurityProcessed(): bool
    {
        return $this->guests()->where(function ($q) {
            $q->whereNotNull('guest_card_number')
              ->orWhereNotNull('checked_in_at')
              ->orWhereNotNull('checked_out_at')
              ->orWhereNotNull('processed_by');
        })->exists();
    }

    public function canEdit(?User $user = null): bool
    {
        $user = $user ?? auth()->user();
        if (!$user) {
            return false;
        }

        if (!$user->isAdminUser() && (int) $this->created_by !== (int) $user->id) {
            return false;
        }

        if ($this->isClosed()) {
            return false;
        }

        return !$this->isSecurityProcessed();
    }

    public static function visitorTypeOptions(): array
    {
        return [
            'vip_no_reg'          => 'messages.visitor_type_vip_no_reg',
            'vip'                 => 'messages.visitor_type_vip',
            'regular'             => 'messages.visitor_type_regular',
            'candidate'           => 'messages.visitor_type_candidate',
            'construction'        => 'messages.visitor_type_construction',
            'contractor_03'       => 'messages.visitor_type_contractor_03',
            'contractor_regular'  => 'messages.visitor_type_contractor_regular',
        ];
    }

    public function getVisitorTypeLabelAttribute(): string
    {
        $options = self::visitorTypeOptions();
        if ($this->visitor_type && isset($options[$this->visitor_type])) {
            return __($options[$this->visitor_type]);
        }
        return $this->visitor_type ?? '—';
    }
}
