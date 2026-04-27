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
        'title',
        'description',
        'status',
        'review_type',
        'sekretaris_id',
        'submission_date',
        'review_date',
        'decision_date',
        'rejection_reason',
    ];

    protected $casts = [
        'submission_date' => 'date',
        'review_date' => 'date',
        'decision_date' => 'date',
    ];

    // Konstanta status proposal
    const STATUS_NEW = 'new_proposal';
    const STATUS_ON_REVIEW = 'on_review';
    const STATUS_REVISED = 'revised';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // Konstanta tipe review
    const REVIEW_EXEMPTED = 'exempted';
    const REVIEW_EXPEDITED = 'expedited';
    const REVIEW_FULL_BOARD = 'full_board';

    // ========== RELATIONSHIPS ==========
    
    // Peneliti yang mengajukan proposal
    public function researcher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Sekretaris yang menangani proposal
    public function sekretaris()
    {
        return $this->belongsTo(User::class, 'sekretaris_id');
    }

    // File-file proposal
    public function files()
    {
        return $this->hasMany(ProposalFile::class);
    }

    // Review untuk proposal ini
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Feedback review untuk proposal
    public function reviewFeedbacks()
    {
        return $this->hasMany(ReviewFeedback::class);
    }

    // Dokumen ethical clearance
    public function ethicsDocument()
    {
        return $this->hasOne(EthicsDocument::class);
    }

    // Riwayat revisi proposal
    public function revisions()
    {
        return $this->hasMany(ProposalRevision::class);
    }

    // Assignment proposal
    public function assignments()
    {
        return $this->hasMany(ProposalAssignment::class);
    }

    // Log dokumen terkait proposal
    public function documentLogs()
    {
        return $this->hasMany(DocumentLog::class);
    }

    // ========== HELPER METHODS ==========
    
    // Cek apakah proposal bisa direview
    public function canBeReviewed()
    {
        return $this->status === self::STATUS_NEW || $this->status === self::STATUS_REVISED;
    }

    // Update status proposal
    public function updateStatus($newStatus)
    {
        $this->status = $newStatus;
        
        if ($newStatus === self::STATUS_APPROVED) {
            $this->decision_date = now();
        } elseif ($newStatus === self::STATUS_REJECTED) {
            $this->decision_date = now();
        } elseif ($newStatus === self::STATUS_ON_REVIEW) {
            $this->review_date = now();
        }
        
        $this->save();
    }

    // Mendapatkan label status
    public function getStatusLabelAttribute()
    {
        return [
            self::STATUS_NEW => 'Proposal Baru',
            self::STATUS_ON_REVIEW => 'Sedang Direview',
            self::STATUS_REVISED => 'Revisi',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
        ][$this->status] ?? $this->status;
    }

    // Mendapatkan badge status untuk UI
    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_NEW => 'bg-blue-100 text-blue-800',
            self::STATUS_ON_REVIEW => 'bg-yellow-100 text-yellow-800',
            self::STATUS_REVISED => 'bg-orange-100 text-orange-800',
            self::STATUS_APPROVED => 'bg-green-100 text-green-800',
            self::STATUS_REJECTED => 'bg-red-100 text-red-800',
        ];
        
        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }
}