<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'nik',
        'phone',
        'address',
        'institution',
        'position',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Cek apakah akun aktif
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // Cek apakah akun pending
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    // ========== RELATIONSHIPS ==========
    
    // Proposal yang diajukan oleh user (sebagai peneliti)
    public function proposals()
    {
        return $this->hasMany(Proposal::class, 'user_id');
    }

    // Proposal yang diverifikasi (sebagai sekretaris)
    public function verifiedProposals()
    {
        return $this->hasMany(Proposal::class, 'sekretaris_id');
    }

    // Review yang ditugaskan ke user (sebagai reviewer)
    public function reviews()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    // Notifikasi untuk user
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Document logs dari user
    public function documentLogs()
    {
        return $this->hasMany(DocumentLog::class);
    }

    // Ethical documents yang ditandatangani (sebagai ketua)
    public function signedEthicsDocuments()
    {
        return $this->hasMany(EthicsDocument::class, 'ketua_id');
    }

    // Template yang dibuat oleh user
    public function createdTemplates()
    {
        return $this->hasMany(Template::class, 'created_by');
    }

    // Assignment yang dilakukan user (assigner)
    public function assignmentsGiven()
    {
        return $this->hasMany(ProposalAssignment::class, 'assigned_by');
    }

    // Assignment yang diterima user (assignee)
    public function assignmentsReceived()
    {
        return $this->hasMany(ProposalAssignment::class, 'assigned_to');
    }

    // Mendapatkan nama role utama user
    public function getPrimaryRoleAttribute()
    {
        return $this->roles->first()->name ?? 'tidak ada role';
    }
}