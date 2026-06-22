<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proposal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'nama_peneliti',
        'asal_instansi',
        'title',
        'description',
        'status',
        'review_type',
        'sekretaris_id',
        'nomor_ec',
        'ketua_id',
        'submission_date',
        'review_date',
        'decision_date',
        'rejection_reason',
    ];

    protected $attributes = [
        'status' => self::STATUS_NEW,
    ];

    protected $casts = [
        'submission_date' => 'date',
        'review_date'     => 'date',
        'decision_date'   => 'date',
    ];

    // ── Status constants ──────────────────────────
    const STATUS_NEW                = 'new_proposal';
    const STATUS_IN_PROCESS         = 'in_process';
    const STATUS_ON_REVIEW          = 'on_review';
    const STATUS_REVISED            = 'revised';
    const STATUS_APPROVED           = 'approved';
    const STATUS_REJECTED           = 'rejected';
    const STATUS_WAITING_FOR_CONFIRMATION = 'waiting_for_confirmation';
    const STATUS_WAITING_FOR_PUBLISH      = 'waiting_for_publish';
    const STATUS_PUBLISHED                = 'published';

    // ── Review type constants ─────────────────────
    const REVIEW_EXEMPTED   = 'exempted';
    const REVIEW_EXPEDITED  = 'expedited';
    const REVIEW_FULL_BOARD = 'full_board';

    // ── Relationships ─────────────────────────────

    public function researcher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sekretaris()
    {
        return $this->belongsTo(User::class, 'sekretaris_id');
    }

    public function ketua()
    {
        return $this->belongsTo(User::class, 'ketua_id');
    }

    public function files()
    {
        return $this->hasMany(ProposalFile::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function reviewFeedbacks()
    {
        return $this->hasMany(ReviewFeedback::class);
    }

    public function ethicsDocument()
    {
        return $this->hasOne(EthicsDocument::class);
    }

    public function revisions()
    {
        return $this->hasMany(ProposalRevision::class);
    }

    public function assignments()
    {
        return $this->hasMany(ProposalAssignment::class);
    }

    public function documentLogs()
    {
        return $this->hasMany(DocumentLog::class);
    }

    public function notes()
    {
        return $this->hasMany(ProposalNote::class);
    }

    // Assignment sekretaris yang sudah dikirim
    public function sekretarisAssignment()
    {
        return $this->hasOne(ProposalAssignment::class)
            ->where('role', ProposalAssignment::ROLE_SEKRETARIS)
            ->whereNotNull('sent_at')
            ->latest();
    }

    // Assignment ketua yang sudah dikirim
    public function ketuaAssignment()
    {
        return $this->hasOne(ProposalAssignment::class)
            ->where('role', ProposalAssignment::ROLE_KETUA)
            ->whereNotNull('sent_at')
            ->latest();
    }

    // ── Helpers ───────────────────────────────────

    public function canBeReviewed()
    {
        return in_array($this->status, [self::STATUS_NEW, self::STATUS_IN_PROCESS, self::STATUS_REVISED]);
    }

    public function isDocumentComplete()
    {
        return $this->files()->where('is_active', true)->exists();
    }

    public function getDocumentStatusAttribute()
    {
        return $this->isDocumentComplete() ? 'lengkap' : 'kurang';
    }

    public function getReviewRoundAttribute()
    {
        if ($this->relationLoaded('reviews')) {
            return $this->reviews->where('status', Review::STATUS_COMPLETED)->count();
        }

        return $this->reviews()->where('status', Review::STATUS_COMPLETED)->count();
    }

    public function getProcessLabelAttribute()
    {
        return match ($this->status) {
            self::STATUS_NEW, self::STATUS_IN_PROCESS => 'PROCESS',
            self::STATUS_ON_REVIEW, self::STATUS_REVISED => 'PROCESS',
            self::STATUS_APPROVED => 'COMPLETE',
            self::STATUS_REJECTED => 'CLOSED',
            default => strtoupper(str_replace('_', ' ', $this->status)),
        };
    }

    public function getProgressLabelAttribute()
    {
        return match ($this->status) {
            self::STATUS_NEW => 'READY',
            self::STATUS_IN_PROCESS => 'PROCESSING',
            self::STATUS_ON_REVIEW => 'PROGRESS',
            self::STATUS_REVISED => 'REVISING',
            self::STATUS_APPROVED => 'COMPLETED',
            self::STATUS_REJECTED => 'REJECTED',
            default => strtoupper(str_replace('_', ' ', $this->status)),
        };
    }

    public function getRoundLabelAttribute()
    {
        return 'Round ' . max(0, $this->review_round);
    }

    public function getProcessBadgeClassesAttribute()
    {
        return match ($this->status) {
            self::STATUS_NEW => 'bg-blue-600 text-white',
            self::STATUS_IN_PROCESS => 'bg-cyan-600 text-white',
            self::STATUS_ON_REVIEW, self::STATUS_REVISED => 'bg-indigo-600 text-white',
            self::STATUS_APPROVED => 'bg-emerald-600 text-white',
            self::STATUS_REJECTED => 'bg-red-600 text-white',
            default => 'bg-gray-900 text-white',
        };
    }

    public function getProgressBadgeClassesAttribute()
    {
        return match ($this->status) {
            self::STATUS_NEW => 'bg-slate-400 text-white',
            self::STATUS_IN_PROCESS => 'bg-yellow-400 text-gray-900',
            self::STATUS_ON_REVIEW => 'bg-amber-400 text-gray-900',
            self::STATUS_REVISED => 'bg-orange-400 text-gray-900',
            self::STATUS_APPROVED => 'bg-emerald-400 text-gray-900',
            self::STATUS_REJECTED => 'bg-red-400 text-white',
            default => 'bg-gray-900 text-white',
        };
    }

    public function getRoundBadgeClassesAttribute()
    {
        return $this->review_round > 0
            ? 'bg-gray-900 text-white'
            : 'bg-slate-700 text-white';
    }

    public function updateStatus($newStatus)
    {
        $this->status = $newStatus;
        if (in_array($newStatus, [self::STATUS_APPROVED, self::STATUS_REJECTED])) {
            $this->decision_date = now();
        } elseif ($newStatus === self::STATUS_ON_REVIEW) {
            $this->review_date = now();
        }
        $this->save();
    }

    public function getStatusLabelAttribute()
    {
        return [
            self::STATUS_NEW                 => 'New Proposal',
            self::STATUS_IN_PROCESS          => 'In Process',
            self::STATUS_ON_REVIEW           => 'On Review',
            self::STATUS_REVISED             => 'Revisi',
            self::STATUS_APPROVED                 => 'Approved',
            self::STATUS_REJECTED                 => 'Rejected',
            self::STATUS_WAITING_FOR_CONFIRMATION => 'Waiting For Confirmation',
            self::STATUS_WAITING_FOR_PUBLISH      => 'Waiting For Publish',
            self::STATUS_PUBLISHED                => 'Published',
        ][$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute()
    {
        return [
            self::STATUS_NEW                 => 'bg-blue-100 text-blue-800',
            self::STATUS_IN_PROCESS          => 'bg-cyan-100 text-cyan-800',
            self::STATUS_ON_REVIEW           => 'bg-yellow-100 text-yellow-800',
            self::STATUS_REVISED             => 'bg-orange-100 text-orange-800',
            self::STATUS_APPROVED            => 'bg-green-100 text-green-800',
            self::STATUS_REJECTED            => 'bg-red-100 text-red-800',
            self::STATUS_WAITING_FOR_CONFIRMATION => 'bg-amber-100 text-amber-800',
            self::STATUS_WAITING_FOR_PUBLISH => 'bg-purple-100 text-purple-800',
            self::STATUS_PUBLISHED           => 'bg-teal-100 text-teal-800',
        ][$this->status] ?? 'bg-gray-100 text-gray-800';
    }
}