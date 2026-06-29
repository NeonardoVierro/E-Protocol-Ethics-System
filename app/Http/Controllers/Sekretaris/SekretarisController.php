<?php

namespace App\Http\Controllers\Sekretaris;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
use App\Models\Proposal;
use App\Models\ProposalAssignment;
use App\Models\ProposalFile;
use App\Models\ProposalRevision;
use App\Models\Review;
use App\Models\ReviewFeedback;
use App\Models\EthicsDocument;
use App\Models\DocumentLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SekretarisController extends Controller
{
    public function dashboard()
    {
        $assignmentConstraint = function ($query) {
            $query->where('role', ProposalAssignment::ROLE_SEKRETARIS)
                  ->where('assigned_to', Auth::id())
                  ->whereNotNull('sent_at');
        };

        $baseQuery = Proposal::with(['researcher', 'assignments' => $assignmentConstraint])
            ->whereHas('assignments', $assignmentConstraint);

        $data = [
            'total_proposal' => $baseQuery->count(),
            'new_proposal' => (clone $baseQuery)->whereIn('status', [Proposal::STATUS_NEW, Proposal::STATUS_IN_PROCESS])->count(),
            'on_review' => (clone $baseQuery)->where('status', Proposal::STATUS_ON_REVIEW)->count(),
            'approved' => (clone $baseQuery)->where('status', Proposal::STATUS_APPROVED)->count(),
            'rejected' => (clone $baseQuery)->where('status', Proposal::STATUS_REJECTED)->count(),
            'recentProposals' => $baseQuery->orderByDesc('submission_date')->orderByDesc('created_at')->take(4)->get(),
        ];

        return view('sekretaris.dashboard', $data);
    }

    public function manajemenProposal(Request $request)
    {
        $status = $request->query('status', 'in_process');
        $search = $request->query('search');

        $statusMap = [
            'in_process' => [Proposal::STATUS_IN_PROCESS],
            'on_review' => [Proposal::STATUS_ON_REVIEW],
            'revision_required' => [],
            'revised' => [Proposal::STATUS_REVISED],
            'approved' => [Proposal::STATUS_APPROVED],
            'disapproved' => [Proposal::STATUS_REJECTED],
        ];

        $assignmentConstraint = function ($query) {
            $query->where('role', ProposalAssignment::ROLE_SEKRETARIS)
                  ->where('assigned_to', Auth::id())
                  ->whereNotNull('sent_at');
        };

        $baseQuery = Proposal::with([
            'researcher',
            'files' => function ($query) {
                $query->where('is_active', true);
            },
            'assignments' => $assignmentConstraint,
            'assignments.assignedBy',
        ])
            ->whereHas('assignments', $assignmentConstraint);

        $statusCounts = [
            'in_process' => (clone $baseQuery)->whereIn('status', $statusMap['in_process'])->count(),
            'on_review' => (clone $baseQuery)->whereIn('status', $statusMap['on_review'])->count(),
            'revision_required' => 0,
            'revised' => (clone $baseQuery)->whereIn('status', $statusMap['revised'])->count(),
            'approved' => (clone $baseQuery)->whereIn('status', $statusMap['approved'])->count(),
            'disapproved' => (clone $baseQuery)->whereIn('status', $statusMap['disapproved'])->count(),
        ];

        if ($search) {
            $baseQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('nama_peneliti', 'like', "%{$search}%")
                      ->orWhereHas('researcher', function ($query) use ($search) {
                          $query->where('name', 'like', "%{$search}%");
                      });
            });
        }

        if (array_key_exists($status, $statusMap) && !empty($statusMap[$status])) {
            $baseQuery->whereIn('status', $statusMap[$status]);
        } elseif ($status === 'revision_required') {
            $baseQuery->whereRaw('0 = 1');
        }

        $proposals = $baseQuery
            ->withCount('revisions')
            ->orderByDesc('submission_date')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($proposal) {
                $activeFilesCount = $proposal->files->count();
                $proposal->files_count = $activeFilesCount;
                $proposal->has_documents = $activeFilesCount > 0;
                $proposal->sekretaris_assignment = $proposal->assignments->first();

                return $proposal;
            });

        return view('sekretaris.manajemen-proposal.index', compact('proposals', 'statusCounts', 'status', 'search'));
    }

    public function showProposal(Proposal $proposal)
    {
        $assigned = $proposal->assignments()
            ->where('role', ProposalAssignment::ROLE_SEKRETARIS)
            ->where('assigned_to', Auth::id())
            ->whereNotNull('sent_at')
            ->exists();

        if (! $assigned) {
            abort(403);
        }

        $proposal->load([
            'researcher',
            'files' => function ($query) {
                $query->where('is_active', true);
            },
            'assignments' => function ($q) {
                $q->where('role', ProposalAssignment::ROLE_SEKRETARIS)
                  ->where('assigned_to', Auth::id())
                  ->whereNotNull('sent_at');
            },
            'assignments.assignedBy',
            'reviews' => function ($q) {
                $q->with('feedback', 'reviewer');
            },
            'revisions',
        ]);

        $proposal->sekretaris_assignment = $proposal->assignments->first();

        $hasSubmittedRevision = $proposal->revisions->where('status', ProposalRevision::STATUS_SUBMITTED)->isNotEmpty();
        $latestSubmittedRevision = $proposal->revisions->where('status', ProposalRevision::STATUS_SUBMITTED)->sortByDesc('submitted_date')->first();
        $previousReviewers = collect();

        if ($latestSubmittedRevision) {
            $previousReviewers = ProposalAssignment::where('proposal_id', $proposal->id)
                ->where('role', ProposalAssignment::ROLE_REVIEWER)
                ->whereNotNull('sent_at')
                ->where('sent_at', '<', $latestSubmittedRevision->submitted_date)
                ->with('assignedTo')
                ->get()
                ->map(function ($assignment) {
                    return [
                        'name' => optional($assignment->assignedTo)->name ?? 'Reviewer',
                        'email' => optional($assignment->assignedTo)->email ?? '-',
                        'assigned_at' => optional($assignment->sent_at)->format('d M Y') ?? '-',
                        'completed_at' => optional($assignment->sent_at)->format('d M Y') ?? '-',
                    ];
                })
                ->unique('email')
                ->values();
        }

        $reviewers = User::whereHas('roles', function ($q) {
                $q->where('name', 'reviewer');
            })
            ->where('status', 'active')
            ->withCount(['assignmentsReceived as review_assignments_count' => function ($q) {
                $q->where('role', ProposalAssignment::ROLE_REVIEWER)
                  ->whereNotNull('sent_at')
                  ->whereDoesntHave('proposal.reviews', function ($q) {
                      $q->whereColumn('reviews.reviewer_id', 'proposal_assignments.assigned_to')
                        ->where(function ($q) {
                            $q->where('status', \App\Models\Review::STATUS_COMPLETED)
                              ->orWhereHas('feedback', function ($q2) {
                                  $q2->where('is_submitted', true);
                              });
                        });
                  });
            }])
            ->orderBy('name')
            ->get();

        $currentReviewer = $proposal->assignments()
            ->where('role', ProposalAssignment::ROLE_REVIEWER)
            ->with('assignedTo')
            ->latest()
            ->first();

        $reviewAssignments = $proposal->assignments()
            ->where('role', ProposalAssignment::ROLE_REVIEWER)
            ->with(['assignedTo', 'assignedBy'])
            ->get()
            ->filter(function ($assignment) use ($proposal) {
                $review = $proposal->reviews->firstWhere('reviewer_id', $assignment->assigned_to);
                $isCompletedReview = $review && $review->isCompleted();
                $hasSubmittedFeedback = $review && optional($review->feedback)->is_submitted;

                return ! ($isCompletedReview || $hasSubmittedFeedback);
            });

        $latestReviewerAssignment = ProposalAssignment::where('proposal_id', $proposal->id)
            ->where('role', ProposalAssignment::ROLE_REVIEWER)
            ->whereNotNull('sent_at')
            ->orderByDesc('sent_at')
            ->first();

        $latestSubmittedRevision = ProposalRevision::where('proposal_id', $proposal->id)
            ->where('status', ProposalRevision::STATUS_SUBMITTED)
            ->orderByDesc('created_at')
            ->first();

        $processingActionHidden = false;
        if ($latestReviewerAssignment) {
            if (! $latestSubmittedRevision || $latestReviewerAssignment->sent_at >= $latestSubmittedRevision->created_at) {
                $processingActionHidden = true;
            }
        }

        $logs = $proposal->documentLogs()->latest()->get();

        return view('sekretaris.manajemen-proposal.show', compact('proposal', 'reviewers', 'currentReviewer', 'reviewAssignments', 'logs', 'processingActionHidden', 'previousReviewers'));
    }

    public function updateReviewType(Request $request, Proposal $proposal)
    {
        $request->validate([
            'review_type' => 'required|in:' . implode(',', [
                Proposal::REVIEW_EXEMPTED,
                Proposal::REVIEW_EXPEDITED,
                Proposal::REVIEW_FULL_BOARD,
            ]),
        ]);

        $assigned = $proposal->assignments()
            ->where('role', ProposalAssignment::ROLE_SEKRETARIS)
            ->where('assigned_to', Auth::id())
            ->whereNotNull('sent_at')
            ->exists();

        if (! $assigned) {
            abort(403);
        }

        $proposal->update(['review_type' => $request->review_type]);

        return redirect()->route('sekretaris.proposal.show', $proposal)
            ->with('success', 'Jenis review proposal berhasil diperbarui.');
    }

    public function assignReviewerToProposal(Request $request, Proposal $proposal)
    {
        $request->validate([
            'reviewer_id' => 'required|exists:users,id',
        ]);

        $assigned = $proposal->assignments()
            ->where('role', ProposalAssignment::ROLE_SEKRETARIS)
            ->where('assigned_to', Auth::id())
            ->whereNotNull('sent_at')
            ->exists();

        if (! $assigned) {
            abort(403);
        }

        $reviewer = User::whereHas('roles', function ($q) {
                    $q->where('name', 'reviewer');
                })
                ->where('status', 'active')
                ->findOrFail($request->reviewer_id);

        ProposalAssignment::create([
            'proposal_id' => $proposal->id,
            'assigned_by' => Auth::id(),
            'assigned_to' => $reviewer->id,
            'role'        => ProposalAssignment::ROLE_REVIEWER,
            'sent_at'     => now(),
        ]);

        $proposal->update(['status' => Proposal::STATUS_ON_REVIEW]);

        DocumentLog::create([
            'proposal_id' => $proposal->id,
            'user_id'     => Auth::id(),
            'activity'    => DocumentLog::ACTIVITY_ASSIGN,
            'description' => 'Reviewer assigned: ' . $reviewer->name,
            'metadata'    => ['reviewer_id' => $reviewer->id],
        ]);

        return redirect()->route('sekretaris.proposal.show', $proposal)
            ->with('success', 'Reviewer berhasil dipilih dan ditandatangani.');
    }

    public function sendProposalToReviewer(Request $request, Proposal $proposal)
    {
        $request->validate([
            'review_type' => 'required|in:' . implode(',', [
                Proposal::REVIEW_EXEMPTED,
                Proposal::REVIEW_EXPEDITED,
                Proposal::REVIEW_FULL_BOARD,
            ]),
            'reviewer_id' => 'required|array|min:1|max:3',
            'reviewer_id.*' => 'required|distinct|exists:users,id',
            'due_date'    => 'required|date|after_or_equal:today',
            'notes'       => 'nullable|string|max:1000',
            'comment_to_review' => 'nullable|string|max:1000',
        ]);

        $assigned = $proposal->assignments()
            ->where('role', ProposalAssignment::ROLE_SEKRETARIS)
            ->where('assigned_to', Auth::id())
            ->whereNotNull('sent_at')
            ->exists();

        if (! $assigned) {
            abort(403);
        }

        $reviewerIds = array_unique($request->input('reviewer_id', []));

        $reviewers = User::whereHas('roles', function ($q) {
                $q->where('name', 'reviewer');
            })
            ->where('status', 'active')
            ->whereIn('id', $reviewerIds)
            ->get();

        $proposal->update(['review_type' => $request->review_type]);
        $proposal->update(['status' => Proposal::STATUS_ON_REVIEW]);

        // Normalize due_date to Y-m-d to avoid timezone/format inconsistencies
        $dueDate = null;
        if ($request->filled('due_date')) {
            try {
                $dueDate = \Carbon\Carbon::parse($request->due_date)->toDateString();
            } catch (\Exception $e) {
                $dueDate = $request->due_date;
            }
        }

        $assignedNames = [];

        foreach ($reviewers as $reviewer) {
            ProposalAssignment::create([
                'proposal_id' => $proposal->id,
                'assigned_by' => Auth::id(),
                'assigned_to' => $reviewer->id,
                'role'        => ProposalAssignment::ROLE_REVIEWER,
                'notes'       => $request->notes,
                'due_date'    => $dueDate,
                'comment_to_review' => $request->comment_to_review,
                'sent_at'     => now(),
            ]);

            $review = \App\Models\Review::where('proposal_id', $proposal->id)
                ->where('reviewer_id', $reviewer->id)
                ->orderByDesc('created_at')
                ->with('feedback')
                ->first();

            if (! $review) {
                \App\Models\Review::create([
                    'proposal_id' => $proposal->id,
                    'reviewer_id' => $reviewer->id,
                    'status' => \App\Models\Review::STATUS_ASSIGNED,
                    'assigned_date' => now(),
                    'due_date' => $dueDate,
                ]);
            } else {
                $review->update([
                    'status' => \App\Models\Review::STATUS_ASSIGNED,
                    'assigned_date' => now(),
                    'due_date' => $dueDate,
                    'completed_date' => null,
                ]);
            }

            \App\Models\Notification::create([
                'user_id' => $reviewer->id,
                'title' => 'Penugasan Review Baru',
                'message' => 'Anda telah ditugaskan untuk mereview proposal: ' . $proposal->title,
                'type' => \App\Models\Notification::TYPE_REVIEW_ASSIGNMENT,
                'status' => \App\Models\Notification::STATUS_UNREAD,
                'data' => [
                    'proposal_id' => $proposal->id,
                    'review_type' => $request->review_type,
                    'due_date' => $request->due_date,
                ],
            ]);

            $assignedNames[] = $reviewer->name;
        }

        DocumentLog::create([
            'proposal_id' => $proposal->id,
            'user_id'     => Auth::id(),
            'activity'    => DocumentLog::ACTIVITY_ASSIGN,
            'description' => 'Submission assigned to reviewers: ' . implode(', ', $assignedNames),
            'metadata'    => [
                'reviewer_ids' => $reviewers->pluck('id')->toArray(),
                'review_type' => $request->review_type,
                'notes'       => $request->notes ?? null,
                'due_date'    => $request->due_date ?? null,
                'comment_to_review' => $request->comment_to_review ?? null,
            ],
        ]);

        return redirect()->route('sekretaris.proposal.show', $proposal)
            ->with('success', 'Proposal berhasil dikirim ke reviewer. Pastikan informasi review dan reviewer sudah benar.');
    }

    public function activityLogs(Proposal $proposal)
    {
        $logs = $proposal->documentLogs()->latest()->get();
        return view('sekretaris.manajemen-proposal._activity_logs', compact('logs', 'proposal'));
    }

    public function viewProposalFile(ProposalFile $file)
    {
        $proposal = $file->proposal;
        $assigned = $proposal->assignments()
            ->where('role', ProposalAssignment::ROLE_SEKRETARIS)
            ->where('assigned_to', Auth::id())
            ->whereNotNull('sent_at')
            ->exists();

        if (! $assigned) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($file->file_path));
    }

    public function downloadProposalFile(ProposalFile $file)
    {
        $proposal = $file->proposal;
        $assigned = $proposal->assignments()
            ->where('role', ProposalAssignment::ROLE_SEKRETARIS)
            ->where('assigned_to', Auth::id())
            ->whereNotNull('sent_at')
            ->exists();

        if (! $assigned) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404);
        }

        return response()->download(Storage::disk('public')->path($file->file_path), $file->original_name);
    }

    public function downloadCertificate(EthicsDocument $document)
    {
        $assigned = $document->proposal->assignments()
            ->where('role', ProposalAssignment::ROLE_SEKRETARIS)
            ->where('assigned_to', Auth::id())
            ->whereNotNull('sent_at')
            ->exists();

        if (! $assigned) {
            abort(403);
        }

        if (! $document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            abort(404);
        }

        return response()->download(
            Storage::disk('public')->path($document->file_path),
            $document->original_name ?: 'ethical-clearance.pdf'
        );
    }

    public function hasilReview(Request $request)
    {
        // Get proposal IDs yang di-assign ke sekretaris saat ini
        $sekretarisProposalIds = ProposalAssignment::where('role', ProposalAssignment::ROLE_SEKRETARIS)
            ->where('assigned_to', Auth::id())
            ->pluck('proposal_id')
            ->toArray();

        // Get unique proposals dengan submitted feedbacks yang di-assign ke sekretaris ini
        $query = ReviewFeedback::where('is_submitted', true)
            ->whereIn('proposal_id', $sekretarisProposalIds)
            ->select('proposal_id')
            ->selectRaw('MAX(submitted_at) as latest_submitted_at')
            ->groupBy('proposal_id')
            ->orderByRaw('MAX(submitted_at) DESC');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->whereHas('proposal', function ($p) use ($q) {
                    $p->where('title', 'like', "%{$q}%")
                      ->orWhere('code', 'like', "%{$q}%");
                })->orWhereHas('proposal.researcher', function ($r) use ($q) {
                    $r->where('name', 'like', "%{$q}%");
                });
            });
        }

        $proposalIds = $query->paginate(12)->withQueryString();

        // Get proposals dengan feedbacks count dan rekomendasi summary
        $proposals = \App\Models\Proposal::with(['researcher', 'assignments' => function($q) {
            $q->where('role', \App\Models\ProposalAssignment::ROLE_REVIEWER);
        }])->whereIn('id', $proposalIds->pluck('proposal_id'))->get();

        // Tambah data summary untuk setiap proposal
        $proposals = $proposals->map(function($proposal) {
            $feedbacks = ReviewFeedback::where('proposal_id', $proposal->id)
                ->where('is_submitted', true)
                ->get();
            
            $proposal->feedbacks_count = $feedbacks->count();
            $proposal->reviewers_count = $proposal->assignments->count();
            $proposal->latest_feedback = $feedbacks->sortByDesc('submitted_at')->first();
            $proposal->recommendations = [
                'approved' => $feedbacks->where('recommendation', 'approved')->count(),
                'revision' => $feedbacks->where('recommendation', 'revision')->count(),
                'rejected' => $feedbacks->where('recommendation', 'rejected')->count(),
            ];
            
            return $proposal;
        });

        // Stats hanya untuk proposal yang di-assign ke sekretaris saat ini
        $stats = [
            'total' => ReviewFeedback::where('is_submitted', true)->whereIn('proposal_id', $sekretarisProposalIds)->count(),
            'approved' => ReviewFeedback::where('is_submitted', true)->where('recommendation', ReviewFeedback::RECOMMENDATION_APPROVED)->whereIn('proposal_id', $sekretarisProposalIds)->count(),
            'revision' => ReviewFeedback::where('is_submitted', true)->where('recommendation', ReviewFeedback::RECOMMENDATION_REVISION)->whereIn('proposal_id', $sekretarisProposalIds)->count(),
            'rejected' => ReviewFeedback::where('is_submitted', true)->where('recommendation', ReviewFeedback::RECOMMENDATION_REJECTED)->whereIn('proposal_id', $sekretarisProposalIds)->count(),
        ];

        return view('sekretaris.hasil-review.index', compact('proposals', 'proposalIds', 'stats'));
    }

    public function hasilReviewShow(Proposal $proposal)
    {
        // Check apakah proposal ini di-assign ke sekretaris saat ini
        $isAuthorized = ProposalAssignment::where('role', ProposalAssignment::ROLE_SEKRETARIS)
            ->where('proposal_id', $proposal->id)
            ->where('assigned_to', Auth::id())
            ->exists();

        if (!$isAuthorized) {
            abort(403, 'Anda tidak memiliki akses untuk melihat proposal ini.');
        }

        $proposal->load(['researcher', 'files' => function ($q) { $q->where('is_active', true); }]);

        $feedbacks = ReviewFeedback::with(['review.reviewer'])
            ->where('proposal_id', $proposal->id)
            ->where('is_submitted', true)
            ->orderByDesc('submitted_at')
            ->get();

        // Get all submitted revisions for this proposal
        $submittedRevisions = ProposalRevision::where('proposal_id', $proposal->id)
            ->where('status', ProposalRevision::STATUS_SUBMITTED)
            ->get();

        $summary = [
            'approved' => $feedbacks->where('recommendation', ReviewFeedback::RECOMMENDATION_APPROVED)->count(),
            'revision' => $feedbacks->where('recommendation', ReviewFeedback::RECOMMENDATION_REVISION)->count(),
            'rejected' => $feedbacks->where('recommendation', ReviewFeedback::RECOMMENDATION_REJECTED)->count(),
            'total_reviewers' => $feedbacks->count(),
        ];

        $feedbacks->transform(function ($fb) use ($submittedRevisions) {
            $parsed = null;
            if (is_array($fb->feedback_text)) {
                $parsed = $fb->feedback_text;
            } elseif (is_string($fb->feedback_text)) {
                $decoded = json_decode($fb->feedback_text, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $parsed = $decoded;
                } else {
                    $parsed = ['summary' => $fb->feedback_text];
                }
            } else {
                $parsed = ['summary' => (string) $fb->feedback_text];
            }

            $fb->parsed_feedback = $parsed;

            // Determine if this feedback is for initial review or revision review
            $isRevisionReview = false;
            if ($submittedRevisions->isNotEmpty()) {
                // Check if there's any submitted revision before this feedback was submitted
                $revisionBeforeFeedback = $submittedRevisions->filter(function ($revision) use ($fb) {
                    return $revision->submitted_date && $fb->submitted_at && 
                           $revision->submitted_date <= $fb->submitted_at->toDateString();
                })->isNotEmpty();
                $isRevisionReview = $revisionBeforeFeedback;
            }

            $fb->is_revision_review = $isRevisionReview;
            $fb->review_type_label = $isRevisionReview ? 'Review Revisi' : 'Reviewer Awal';

            return $fb;
        });

        return view('sekretaris.hasil-review.show', compact('proposal', 'feedbacks', 'summary'));
    }

    public function keputusan(Request $request)
    {
        $assignmentConstraint = function ($query) {
            $query->where('role', ProposalAssignment::ROLE_SEKRETARIS)
                ->where('assigned_to', Auth::id())
                ->whereNotNull('sent_at');
        };

        $keputusan = Proposal::with(['researcher'])
            ->withCount('revisions')
            ->whereHas('assignments', $assignmentConstraint)
            ->whereHas('reviewFeedbacks', function ($query) {
                $query->where('is_submitted', true);
            })
            // Include proposals that already have a decision as well so they remain
            // visible on the Keputusan page (action replaced by "Sudah diputuskan").
            ->orderByRaw("CASE status
                WHEN 'on_review' THEN 1
                WHEN 'revised' THEN 2
                WHEN 'approved' THEN 3
                WHEN 'rejected' THEN 4
                WHEN 'waiting_for_publish' THEN 5
                WHEN 'published' THEN 6
                ELSE 7
            END")
            ->orderByDesc('submission_date')
            ->get();

        $autoOpenProposalId = $request->query('proposal_id');
        $autoOpenProposal = null;

        if ($autoOpenProposalId) {
            $autoOpenProposal = $keputusan->firstWhere('id', (int) $autoOpenProposalId);
        }

        return view('sekretaris.keputusan.index', compact('keputusan', 'autoOpenProposalId', 'autoOpenProposal'));
    }

    public function updateDecision(Request $request)
    {
        $request->validate([
            'proposal_id'      => 'required|integer|exists:proposals,id',
            'status'           => 'required|in:approved,revised,rejected',
            'rejection_reason' => 'required_if:status,rejected,revised|nullable|string|max:1000',
        ], [
            'rejection_reason.required_if' => 'Alasan wajib diisi untuk keputusan revisi atau penolakan.',
        ]);

        $proposal = Proposal::findOrFail($request->proposal_id);

        $finalStatuses = [Proposal::STATUS_APPROVED, Proposal::STATUS_REJECTED, Proposal::STATUS_REVISED, Proposal::STATUS_WAITING_FOR_PUBLISH, Proposal::STATUS_PUBLISHED];
        if (in_array($proposal->status, $finalStatuses) && $proposal->decision_date !== null) {
            return redirect()->route('sekretaris.keputusan')
                ->with('error', 'Keputusan untuk proposal ini sudah dibuat dan tidak dapat diubah. Jika peneliti mengirim revisi, akan dibuat dokumen versi baru (vol2).');
        }

        $assigned = $proposal->assignments()
            ->where('role', ProposalAssignment::ROLE_SEKRETARIS)
            ->where('assigned_to', Auth::id())
            ->whereNotNull('sent_at')
            ->exists();

        if (!$assigned) {
            abort(403, 'Anda tidak memiliki akses untuk memutuskan proposal ini.');
        }

        $proposal->status = $request->status;
        $proposal->rejection_reason = in_array($request->status, ['rejected', 'revised'])
            ? $request->rejection_reason
            : null;
        $proposal->decision_date = now();
        $proposal->save();

        if ($request->rejection_reason) {
            \App\Models\ProposalNote::create([
                'proposal_id' => $proposal->id,
                'user_id'     => Auth::id(),
                'note_type'   => \App\Models\ProposalNote::TYPE_SECRETARY,
                'content'     => $request->rejection_reason,
            ]);
        }

        if ($request->status === 'revised') {
            $nextRevisionNumber = $proposal->revisions()->max('revision_number') + 1;

            \App\Models\ProposalRevision::create([
                'proposal_id'       => $proposal->id,
                'revision_number'   => $nextRevisionNumber,
                'revision_note'     => null,
                'requested_date'    => now(),
                'submitted_date'    => null,
                'status'            => 'requested',
                'file_id'           => null,
            ]);

            \App\Models\Notification::create([
                'user_id' => $proposal->user_id,
                'title'   => 'Permintaan Revisi Proposal',
                'message' => 'Proposal "' . $proposal->title . '" memerlukan revisi. Silakan upload dokumen revisi (Vol.' . $nextRevisionNumber . ').',
                'type'    => \App\Models\Notification::TYPE_REVISION_REQUEST,
                'status'  => \App\Models\Notification::STATUS_UNREAD ?? 'unread',
                'data'    => json_encode([
                    'proposal_id'     => $proposal->id,
                    'revision_number' => $nextRevisionNumber,
                ]),
            ]);
        }

        if ($request->status === 'approved') {
            $nomorDraft = 'DRAFT-EC-' . now()->format('Ymd') . '-' . str_pad($proposal->id, 4, '0', STR_PAD_LEFT);

            $ethicsDocument = \App\Models\EthicsDocument::firstOrCreate(
                ['proposal_id' => $proposal->id],
                [
                    'document_number' => $nomorDraft,
                    'status'          => \App\Models\EthicsDocument::STATUS_DRAFT,
                    'file_path'       => '',
                    'original_name'   => '',
                    'notes'           => 'Draft otomatis dibuat saat proposal disetujui oleh sekretaris.',
                ]
            );

            $proposal->update(['nomor_ec' => $nomorDraft]);
        }

        if ($request->status !== 'revised') {
            \App\Models\Notification::create([
                'user_id' => $proposal->user_id,
                'title'   => 'Keputusan Proposal: ' . match ($request->status) {
                    'approved' => 'Disetujui',
                    'rejected' => 'Ditolak',
                },
                'message' => match ($request->status) {
                    'approved' => 'Selamat! Proposal "' . $proposal->title . '" Anda telah disetujui oleh sekretaris.',
                    'rejected' => 'Proposal "' . $proposal->title . '" ditolak. Alasan: ' . $request->rejection_reason,
                },
                'type'   => \App\Models\Notification::TYPE_PROPOSAL_STATUS ?? 'proposal_status',
                'status' => \App\Models\Notification::STATUS_UNREAD ?? 'unread',
                'data'   => json_encode([
                    'proposal_id' => $proposal->id,
                    'status'      => $request->status,
                ]),
            ]);
        }

        // If sekretaris rejected the proposal, return to management list instead
        if ($request->status === 'rejected') {
            return redirect()->route('sekretaris.manajemen-proposal')
                ->with('success', 'Proposal "' . $proposal->title . '" berhasil ditolak.');
        }

        return redirect()->route('sekretaris.keputusan')
            ->with('success', 'Keputusan untuk proposal "' . $proposal->title . '" berhasil disimpan.');
    }

    public function draftEthicalClearance()
    {
        $docs = EthicsDocument::with('proposal')
            ->where('status', EthicsDocument::STATUS_DRAFT)
            ->orderByDesc('created_at')
            ->get();

        $drafts = $docs->map(function ($d) {
            return [
                'id' => $d->document_number ?? ('EC' . str_pad($d->id, 3, '0', STR_PAD_LEFT)),
                'proposal_id' => $d->proposal?->id ?? null,
                'judul' => $d->proposal?->title ?? ($d->original_name ?? 'Untitled'),
                'status' => $d->status,
                'tanggal' => $d->created_at?->toDateString() ?? now()->toDateString(),
            ];
        })->toArray();

        $admins = User::whereHas('roles', function ($q) {
                $q->where('name', 'admin');
            })
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $approvedProposals = Proposal::whereHas('assignments', function ($q) {
                $q->where('role', ProposalAssignment::ROLE_SEKRETARIS)
                  ->where('assigned_to', Auth::id())
                  ->whereNotNull('sent_at');
            })
            ->where('status', Proposal::STATUS_APPROVED)
            ->whereDoesntHave('ethicsDocument', function ($docQ) {
                $docQ->where('status', EthicsDocument::STATUS_DRAFT)
                     ->whereHas('documentLogs', function ($logQ) {
                         $logQ->where('activity', DocumentLog::ACTIVITY_ASSIGN);
                     });
            })
            ->with('researcher')
            ->orderByDesc('submission_date')
            ->get();

        return view('sekretaris.draf-ethical-clearance.new', compact('drafts', 'admins', 'approvedProposals'));
    }

    public function storeDraft(Request $request)
    {
        $request->validate([
            'proposal_id' => 'nullable|exists:proposals,id',
            'title' => 'required|string|max:1024',
            'principal_investigator' => 'required|string|max:255',
            'members' => 'nullable|string|max:2000',
            'institution' => 'required|string|max:255',
            'research_place' => 'required|string|max:255',
            'admin_id' => 'nullable|exists:users,id',
        ]);

        $payload = [
            'title' => $request->title,
            'principal_investigator' => $request->principal_investigator,
            'members' => $request->members,
            'institution' => $request->institution,
            'research_place' => $request->research_place,
            'admin_id' => $request->admin_id,
        ];

        EthicsDocument::updateOrCreate(
            ['proposal_id' => $request->proposal_id],
            [
                'document_number' => '',
                'ketua_id' => null,
                'status' => EthicsDocument::STATUS_DRAFT,
                'file_path' => '',
                'original_name' => '',
                'notes' => json_encode($payload),
            ]
        );

        return redirect()->route('sekretaris.draf-ethical-clearance')->with('success', 'Draft berhasil disimpan. Anda dapat mengedit atau mengirimkannya ke pemohon.');
    }

    public function sendDraft(Request $request, EthicsDocument $document)
    {
        $document->update(['status' => EthicsDocument::STATUS_DRAFT]);

        if ($document->proposal && $document->proposal->user_id) {
            \App\Models\Notification::create([
                'user_id' => $document->proposal->user_id,
                'title' => 'Draft Ethical Clearance Tersedia',
                'message' => 'Draft sertifikat kelaikan etik untuk proposal "' . ($document->proposal->title ?? '—') . '" telah dibuat oleh sekretariat.',
                'type' => \App\Models\Notification::TYPE_PROPOSAL_STATUS ?? 'proposal_status',
                'status' => \App\Models\Notification::STATUS_UNREAD ?? 'unread',
                'data' => json_encode(['document_id' => $document->id, 'proposal_id' => $document->proposal->id ?? null]),
            ]);
        }

        return redirect()->route('sekretaris.draf-ethical-clearance')->with('success', 'Draft berhasil dikirimkan ke pemohon (notifikasi dibuat).');
    }

    public function sendToAdmin(Request $request)
    {
        $request->validate([
            'proposal_id' => 'required|exists:proposals,id',
            'admin_id' => 'required|exists:users,id',
            'title' => 'nullable|string|max:1024',
            'principal_investigator' => 'nullable|string|max:255',
            'members' => 'nullable|string|max:2000',
            'institution' => 'nullable|string|max:255',
            'research_place' => 'nullable|string|max:255',
        ]);

        $proposal = Proposal::findOrFail($request->proposal_id);

        // Get existing document to preserve biodata from storeDraft()
        $document = EthicsDocument::where('proposal_id', $proposal->id)->first();
        
        if (!$document) {
            // If no document exists, create one
            $document = EthicsDocument::create([
                'proposal_id' => $proposal->id,
                'document_number' => '',
                'ketua_id' => null,
                'status' => EthicsDocument::STATUS_DRAFT,
                'file_path' => '',
                'original_name' => '',
                'notes' => json_encode([]),
            ]);
        }

        $document->ketua_id = $request->admin_id;
        $document->status = EthicsDocument::STATUS_DRAFT;

        $notes = [];
        try {
            $decoded = json_decode($document->notes ?: '{}', true);
            if (is_array($decoded)) {
                $notes = $decoded;
            } else {
                $notes = ['notes' => (string) $document->notes];
            }
        } catch (\Throwable $e) {
            $notes = ['notes' => (string) $document->notes];
        }

        foreach (['title', 'principal_investigator', 'members', 'institution', 'research_place'] as $field) {
            if ($request->filled($field)) {
                $notes[$field] = $request->input($field);
            }
        }

        $notes['assigned_admin_id'] = $request->admin_id;
        $notes['assigned_at'] = now()->toDateTimeString();
        $document->notes = json_encode($notes);
        $document->save();

        \App\Models\Notification::create([
            'user_id' => $request->admin_id,
            'title' => 'Draft Ethical Clearance Diterima',
            'message' => 'Draft untuk proposal "' . ($proposal->title ?? '—') . '" telah dikirim untuk ditinjau.',
            'type' => \App\Models\Notification::TYPE_DOCUMENT_READY,
            'status' => \App\Models\Notification::STATUS_UNREAD,
            'data' => json_encode(['document_id' => $document->id, 'proposal_id' => $proposal->id]),
        ]);

        DocumentLog::create([
            'ethics_document_id' => $document->id,
            'proposal_id' => $proposal->id,
            'user_id' => Auth::id(),
            'activity' => DocumentLog::ACTIVITY_SENT_TO_ADMIN,
            'ip_address' => request()->ip(),
            'description' => 'Draft dikirim ke admin ID ' . $request->admin_id,
            'metadata' => ['assigned_admin_id' => $request->admin_id],
        ]);

        return response()->json(['status' => 'ok', 'message' => 'Draft berhasil dikirim ke admin.']);
    }

    public function arsipDokumen(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $documents = EthicsDocument::with(['proposal.files' => function ($query) {
                $query->where('is_active', true);
            }, 'ketua'])
            ->where(function ($q) {
                $q->whereRaw("JSON_VALID(notes) = 1 AND JSON_EXTRACT(notes, '$.assigned_admin_id') IS NOT NULL")
                  ->orWhereExists(function ($sub) {
                      $sub->select(\DB::raw(1))
                          ->from('document_logs')
                          ->whereColumn('document_logs.ethics_document_id', 'ethics_documents.id')
                          ->where('document_logs.activity', DocumentLog::ACTIVITY_ASSIGN);
                  });
            })
            ->when($search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('document_number', 'like', "%{$search}%")
                        ->orWhereHas('proposal', function ($proposalQuery) use ($search) {
                            $proposalQuery->where('title', 'like', "%{$search}%")
                                ->orWhere('nama_peneliti', 'like', "%{$search}%")
                                ->orWhereHas('researcher', function ($researcherQuery) use ($search) {
                                    $researcherQuery->where('name', 'like', "%{$search}%");
                                });
                        });
                });
            })
            ->when($status, function ($q, $status) {
                $q->where('status', $status);
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('sekretaris.arsip-dokumen.index', compact('documents'));
    }

    public function arsip()
    {
        $documents = EthicsDocument::with(['proposal.files' => function ($query) {
                $query->where('is_active', true);
            }, 'ketua'])
            ->where(function ($q) {
                $q->whereRaw("JSON_VALID(notes) = 1 AND JSON_EXTRACT(notes, '$.assigned_admin_id') IS NOT NULL")
                  ->orWhereExists(function ($sub) {
                      $sub->select(\DB::raw(1))
                          ->from('document_logs')
                          ->whereColumn('document_logs.ethics_document_id', 'ethics_documents.id')
                          ->where('document_logs.activity', DocumentLog::ACTIVITY_ASSIGN);
                  });
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('sekretaris.arsip-dokumen.index', compact('documents'));
    }

    public function userManagement()
    {
        $pendingUsers = User::where('status', 'pending')->get();
        return view('sekretaris.user-management.index', compact('pendingUsers'));
    }

    public function activateUser($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'active']);

        Notification::create([
            'user_id' => $user->id,
            'title' => 'Akun Anda Telah Diaktivasi',
            'message' => 'Selamat! Akun Anda telah diaktivasi oleh sekretariat. Anda sekarang dapat login dan mengajukan ethical clearance.',
            'status' => 'unread',
            'type' => Notification::TYPE_ACCOUNT_ACTIVATION,
            'data' => json_encode(['activated_at' => now()->toDateTimeString()]),
        ]);

        return redirect()->route('sekretaris.user-management')->with('success', 'Akun berhasil diaktifkan dan notifikasi telah dikirim ke peneliti.');
    }
}