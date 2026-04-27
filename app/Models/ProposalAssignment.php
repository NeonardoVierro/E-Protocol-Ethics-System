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
    ];

    // Konstanta role assignment
    const ROLE_SEKRETARIS = 'sekretaris';
    const ROLE_REVIEWER = 'reviewer';
    const ROLE_KETUA = 'ketua';

    // ========== RELATIONSHIPS ==========
    
    // Proposal yang di-assign
    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    // User yang melakukan assign
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    // User yang di-assign
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // ========== HELPER METHODS ==========
    
    // Mendapatkan label role
    public function getRoleLabelAttribute()
    {
        return [
            self::ROLE_SEKRETARIS => 'Sekretaris',
            self::ROLE_REVIEWER => 'Reviewer',
            self::ROLE_KETUA => 'Ketua',
        ][$this->role] ?? $this->role;
    }
}