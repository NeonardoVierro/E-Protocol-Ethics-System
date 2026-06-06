<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ReviewFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatReviewController extends Controller
{
    /**
     * Menampilkan daftar proposal yang sudah direview oleh reviewer ini.
     */
    public function index(Request $request)
    {
        // Allowed review types for filter
        $allowedTypes = ['exempted', 'expedited', 'full_board'];

        // Base query for reviews
        $baseQuery = Review::with(['proposal', 'feedback'])
            ->where('reviewer_id', Auth::id())
            ->where('status', Review::STATUS_COMPLETED);

        // Apply review_type filter (on related proposal.review_type)
        if ($request->filled('review_type') && in_array($request->input('review_type'), $allowedTypes)) {
            $baseQuery->whereHas('proposal', function ($q) use ($request) {
                $q->where('review_type', $request->input('review_type'));
            });
        }

        // Apply date range filter on review completed_date
        if ($request->filled('start_date')) {
            $baseQuery->whereDate('completed_date', '>=', $request->input('start_date'));
        }
        if ($request->filled('end_date')) {
            $baseQuery->whereDate('completed_date', '<=', $request->input('end_date'));
        }

        // Clone queries for pagination and stats
        $reviews = (clone $baseQuery)->orderByDesc('completed_date')->paginate(15)->withQueryString();

        // Stats should reflect current filters
        $totalReviews = (clone $baseQuery)->count();
        $approvedCount = (clone $baseQuery)->whereHas('feedback', function ($q) {
            $q->where('recommendation', 'approved');
        })->count();

        $approvalRate = $totalReviews > 0 ? round(($approvedCount / $totalReviews) * 100) : 0;

        return view('reviewer.riwayat-review.index', compact('reviews', 'totalReviews', 'approvalRate'));
    }

    /**
     * Menampilkan detail review tertentu.
     */
    public function show($id)
    {
        $review = Review::with(['proposal', 'feedback'])
            ->where('id', $id)
            ->where('reviewer_id', Auth::id())
            ->findOrFail($id);

        return view('reviewer.riwayat-review.show', compact('review'));
    }
}
