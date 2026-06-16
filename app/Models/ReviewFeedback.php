<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewFeedback extends Model
{
    use HasFactory;

    /**
     * Explicit table name to avoid pluralization mismatches.
     */
    protected $table = 'review_feedbacks';

    protected $fillable = [
        'review_id',
        'proposal_id',
        'feedback_text',
        'recommendation',
        'file_path',
        'original_name',
        'is_submitted',
        'submitted_at',
    ];

    protected $casts = [
        'is_submitted' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    // Konstanta rekomendasi
    const RECOMMENDATION_APPROVED = 'approved';
    const RECOMMENDATION_REVISION = 'revision';
    const RECOMMENDATION_REJECTED = 'rejected';

    // ========== RELATIONSHIPS ==========
    
    // Review yang memberikan feedback
    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    // Proposal yang direview
    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    // ========== HELPER METHODS ==========
    
    // Submit feedback
    public function submit()
    {
        $this->is_submitted = true;
        $this->submitted_at = now();
        $this->save();
        
        // Update status review menjadi completed
        $this->review->updateStatus(Review::STATUS_COMPLETED);
    }

    // Mendapatkan label rekomendasi
    public function getRecommendationLabelAttribute()
    {
        return [
            self::RECOMMENDATION_APPROVED => 'Disetujui',
            self::RECOMMENDATION_REVISION => 'Perlu Revisi',
            self::RECOMMENDATION_REJECTED => 'Ditolak',
        ][$this->recommendation] ?? $this->recommendation;
    }

    // Mendapatkan badge rekomendasi
    public function getRecommendationBadgeAttribute()
    {
        $badges = [
            self::RECOMMENDATION_APPROVED => 'bg-green-100 text-green-800',
            self::RECOMMENDATION_REVISION => 'bg-yellow-100 text-yellow-800',
            self::RECOMMENDATION_REJECTED => 'bg-red-100 text-red-800',
        ];
        
        return $badges[$this->recommendation] ?? 'bg-gray-100 text-gray-800';
    }

    // Cek apakah ini review revisi atau review awal
    public function isRevisionReview()
    {
        if (!$this->submitted_at || !$this->proposal) {
            return false;
        }

        // Cek apakah ada revisi yang submitted sebelum feedback ini
        $submittedRevisions = $this->proposal->revisions()
            ->where('status', 'submitted')
            ->where('submitted_date', '<', $this->submitted_at)
            ->exists();

        return $submittedRevisions;
    }

    // Get label untuk jenis review
    public function getReviewTypeLabel()
    {
        return $this->isRevisionReview() ? 'Review Revisi' : 'Review Awal';
    }

    // Get badge color untuk jenis review
    public function getReviewTypeBadgeClasses()
    {
        return $this->isRevisionReview() 
            ? 'bg-purple-100 text-purple-700' 
            : 'bg-blue-100 text-blue-700';
    }

    // Parsed feedback data helper for views
    public function getParsedFeedbackAttribute()
    {
        if (is_array($this->feedback_text)) {
            return $this->feedback_text;
        }

        if (is_string($this->feedback_text)) {
            $decoded = json_decode($this->feedback_text, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }

            return ['summary' => $this->feedback_text];
        }

        return ['summary' => (string) $this->feedback_text];
    }
}
