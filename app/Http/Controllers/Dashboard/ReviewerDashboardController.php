<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;

class ReviewerDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:reviewer']);
    }
    
    public function index()
    {
        $reviewer = Auth::user();

        // Count active reviews (assigned, in_progress, not expired)
        $activeReviewsCount = Review::where('reviewer_id', $reviewer->id)
            ->whereIn('status', [Review::STATUS_ASSIGNED, Review::STATUS_IN_PROGRESS])
            ->count();

        // Count completed reviews
        $completedReviewsCount = Review::where('reviewer_id', $reviewer->id)
            ->where('status', Review::STATUS_COMPLETED)
            ->count();

        // Get all reviews with proposal and feedback data
        $allReviews = Review::where('reviewer_id', $reviewer->id)
            ->with(['proposal', 'feedback'])
            ->orderBy('due_date', 'asc')
            ->get();

        // Find priority/urgent review (closest deadline, in progress)
        $priorityReview = Review::where('reviewer_id', $reviewer->id)
            ->whereIn('status', [Review::STATUS_ASSIGNED, Review::STATUS_IN_PROGRESS])
            ->where('due_date', '>', now())
            ->orderBy('due_date', 'asc')
            ->with('proposal')
            ->first();

        // Get recent activity (completed and returned reviews)
        $recentActivity = Review::where('reviewer_id', $reviewer->id)
            ->where('status', Review::STATUS_COMPLETED)
            ->with(['proposal', 'feedback'])
            ->orderBy('completed_date', 'desc')
            ->take(5)
            ->get();

        // Calculate review velocity metrics
        $completedReviews = Review::where('reviewer_id', $reviewer->id)
            ->where('status', Review::STATUS_COMPLETED)
            ->with('proposal')
            ->get();

        $avgReviewDays = $this->calculateAverageReviewDays($completedReviews);
        $institutionalAvg = 7.0; // Reference value
        $velocityPercentage = $this->calculateVelocityPercentage($avgReviewDays, $institutionalAvg);

        $data = [
            'title' => 'Dashboard Reviewer',
            'reviewer' => $reviewer,
            'activeReviewsCount' => $activeReviewsCount,
            'completedReviewsCount' => $completedReviewsCount,
            'priorityReview' => $priorityReview,
            'allReviews' => $allReviews,
            'recentActivity' => $recentActivity,
            'avgReviewDays' => $avgReviewDays,
            'institutionalAvg' => $institutionalAvg,
            'velocityPercentage' => $velocityPercentage,
        ];
        
        return view('reviewer.dashboard', $data);
    }

    /**
     * Calculate average days to complete a review
     */
    private function calculateAverageReviewDays($reviews)
    {
        if ($reviews->isEmpty()) {
            return 0;
        }

        $totalDays = 0;
        $count = 0;

        foreach ($reviews as $review) {
            if ($review->assigned_date && $review->completed_date) {
                $days = $review->completed_date->diffInDays($review->assigned_date);
                $totalDays += $days;
                $count++;
            }
        }

        return $count > 0 ? round($totalDays / $count, 1) : 0;
    }

    /**
     * Calculate velocity percentage relative to institutional average
     */
    private function calculateVelocityPercentage($reviewerAvg, $institutionalAvg)
    {
        if ($institutionalAvg == 0) {
            return 0;
        }

        $percentage = (($institutionalAvg - $reviewerAvg) / $institutionalAvg) * 100;
        return round($percentage, 0);
    }
}