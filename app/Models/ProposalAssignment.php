<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'proposal_id',
        'assigned_by',
        'assigned_to',
        'role',
        'notes',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    const ROLE_SEKRETARIS = 'sekretaris';
    const ROLE_REVIEWER   = 'reviewer';
    const ROLE_KETUA      = 'ketua';

    // ── Relationships ─────────────────────────────

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // ── Helpers ───────────────────────────────────

    public function isSent(): bool
    {
        return !is_null($this->sent_at);
    }

    public function getRoleLabelAttribute()
    {
        return [
            self::ROLE_SEKRETARIS => 'Sekretaris',
            self::ROLE_REVIEWER   => 'Reviewer',
            self::ROLE_KETUA      => 'Ketua',
        ][$this->role] ?? $this->role;
    }
}