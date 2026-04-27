<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'proposal_id',
        'reviewer_id',
        'status',
        'assigned_date',
        'due_date',
        'completed_date',
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'due_date' => 'date',
        'completed_date' => 'date',
    ];

    // Konstanta status review
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_EXPIRED = 'expired';

    // ========== RELATIONSHIPS ==========
    
    // Proposal yang direview
    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    // Reviewer (user)
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    // Feedback dari review ini
    public function feedback()
    {
        return $this->hasOne(ReviewFeedback::class);
    }

    // ========== HELPER METHODS ==========
    
    // Cek apakah review sudah selesai
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    // Update status review
    public function updateStatus($newStatus)
    {
        $this->status = $newStatus;
        
        if ($newStatus === self::STATUS_COMPLETED) {
            $this->completed_date = now();
        }
        
        $this->save();
    }

    // Mendapatkan label status
    public function getStatusLabelAttribute()
    {
        return [
            self::STATUS_ASSIGNED => 'Ditugaskan',
            self::STATUS_IN_PROGRESS => 'Dalam Proses',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_EXPIRED => 'Kadaluarsa',
        ][$this->status] ?? $this->status;
    }
}