<?php

use App\Models\Proposal;
use App\Models\ProposalAssignment;
use App\Models\Review;
use App\Models\ReviewFeedback;
use App\Models\User;
use Spatie\Permission\Models\Role;

it('shows proposals with submitted reviewer feedback on the secretary decision page', function () {
    Role::firstOrCreate(['name' => 'sekretaris']);

    $secretary = User::factory()->create([
        'status' => 'active',
    ]);
    $secretary->assignRole('sekretaris');

    $proposal = Proposal::create([
        'user_id' => $secretary->id,
        'nama_peneliti' => $secretary->name,
        'asal_instansi' => 'Universitas Test',
        'title' => 'Proposal Uji Keputusan Sekretaris',
        'description' => 'Uji coba keputusan sekretaris',
        'status' => Proposal::STATUS_APPROVED,
        'submission_date' => now()->toDateString(),
        'decision_date' => null,
    ]);

    ProposalAssignment::create([
        'proposal_id' => $proposal->id,
        'assigned_by' => $secretary->id,
        'assigned_to' => $secretary->id,
        'role' => ProposalAssignment::ROLE_SEKRETARIS,
        'sent_at' => now(),
    ]);

    $review = Review::create([
        'proposal_id' => $proposal->id,
        'reviewer_id' => $secretary->id,
        'status' => Review::STATUS_COMPLETED,
        'assigned_date' => now(),
        'due_date' => now()->addDays(7),
        'completed_date' => now(),
    ]);

    ReviewFeedback::create([
        'review_id' => $review->id,
        'proposal_id' => $proposal->id,
        'feedback_text' => json_encode([
            'general_comments' => 'Feedback sudah masuk',
        ], JSON_UNESCAPED_UNICODE),
        'recommendation' => ReviewFeedback::RECOMMENDATION_APPROVED,
        'is_submitted' => true,
        'submitted_at' => now(),
    ]);

    $this->actingAs($secretary);

    $response = $this->get(route('sekretaris.keputusan'));

    $response->assertOk();
    $response->assertSee($proposal->title);
});

it('does not set decision_date when proposal status changes from reviewer feedback', function () {
    $user = User::factory()->create();

    $proposal = Proposal::create([
        'user_id' => $user->id,
        'nama_peneliti' => $user->name,
        'asal_instansi' => 'Universitas Test',
        'title' => 'Proposal Reviewer Status Change',
        'description' => 'Uji review status update',
        'status' => Proposal::STATUS_ON_REVIEW,
        'submission_date' => now()->toDateString(),
        'decision_date' => null,
    ]);

    $proposal->updateStatus(Proposal::STATUS_APPROVED);

    $this->assertNull($proposal->fresh()->decision_date);
});

it('shows putuskan button for reviewer-approved proposals without secretary decision', function () {
    Role::firstOrCreate(['name' => 'sekretaris']);

    $secretary = User::factory()->create([
        'status' => 'active',
    ]);
    $secretary->assignRole('sekretaris');

    $proposal = Proposal::create([
        'user_id' => $secretary->id,
        'nama_peneliti' => $secretary->name,
        'asal_instansi' => 'Universitas Test',
        'title' => 'Proposal Untuk Putuskan',
        'description' => 'Uji tombol putuskan',
        'status' => Proposal::STATUS_APPROVED,
        'submission_date' => now()->toDateString(),
        'decision_date' => null,
    ]);

    ProposalAssignment::create([
        'proposal_id' => $proposal->id,
        'assigned_by' => $secretary->id,
        'assigned_to' => $secretary->id,
        'role' => ProposalAssignment::ROLE_SEKRETARIS,
        'sent_at' => now(),
    ]);

    $review = Review::create([
        'proposal_id' => $proposal->id,
        'reviewer_id' => $secretary->id,
        'status' => Review::STATUS_COMPLETED,
        'assigned_date' => now(),
        'due_date' => now()->addDays(7),
        'completed_date' => now(),
    ]);

    ReviewFeedback::create([
        'review_id' => $review->id,
        'proposal_id' => $proposal->id,
        'feedback_text' => json_encode([
            'general_comments' => 'Feedback sudah masuk',
        ], JSON_UNESCAPED_UNICODE),
        'recommendation' => ReviewFeedback::RECOMMENDATION_APPROVED,
        'is_submitted' => true,
        'submitted_at' => now(),
    ]);

    $this->actingAs($secretary);

    $response = $this->get(route('sekretaris.keputusan'));

    $response->assertOk();
    $response->assertSee('Putuskan');
});

it('hides proposals after secretary submits a decision', function () {
    Role::firstOrCreate(['name' => 'sekretaris']);

    $secretary = User::factory()->create([
        'status' => 'active',
    ]);
    $secretary->assignRole('sekretaris');

    $proposal = Proposal::create([
        'user_id' => $secretary->id,
        'nama_peneliti' => $secretary->name,
        'asal_instansi' => 'Universitas Test',
        'title' => 'Proposal Setelah Keputusan',
        'description' => 'Uji keputusan sekretaris hilang dari halaman keputusan',
        'status' => Proposal::STATUS_APPROVED,
        'submission_date' => now()->toDateString(),
        'decision_date' => null,
    ]);

    ProposalAssignment::create([
        'proposal_id' => $proposal->id,
        'assigned_by' => $secretary->id,
        'assigned_to' => $secretary->id,
        'role' => ProposalAssignment::ROLE_SEKRETARIS,
        'sent_at' => now(),
    ]);

    $review = Review::create([
        'proposal_id' => $proposal->id,
        'reviewer_id' => $secretary->id,
        'status' => Review::STATUS_COMPLETED,
        'assigned_date' => now(),
        'due_date' => now()->addDays(7),
        'completed_date' => now(),
    ]);

    ReviewFeedback::create([
        'review_id' => $review->id,
        'proposal_id' => $proposal->id,
        'feedback_text' => json_encode([
            'general_comments' => 'Feedback sudah masuk',
        ], JSON_UNESCAPED_UNICODE),
        'recommendation' => ReviewFeedback::RECOMMENDATION_APPROVED,
        'is_submitted' => true,
        'submitted_at' => now(),
    ]);

    $this->actingAs($secretary)
        ->post(route('sekretaris.keputusan.update'), [
            'proposal_id' => $proposal->id,
            'status' => Proposal::STATUS_APPROVED,
        ])
        ->assertRedirect(route('sekretaris.keputusan'));

    expect($proposal->fresh()->decision_date)->not->toBeNull();

    $response = $this->get(route('sekretaris.keputusan'));
    $response->assertOk();
    $response->assertSee($proposal->title);
    $response->assertDontSee('Putuskan');
    $response->assertSee('Sudah diputuskan');
});

it('does not show already-decided proposals on the secretary decision page', function () {
    Role::firstOrCreate(['name' => 'sekretaris']);

    $secretary = User::factory()->create([
        'status' => 'active',
    ]);
    $secretary->assignRole('sekretaris');

    $proposal = Proposal::create([
        'user_id' => $secretary->id,
        'nama_peneliti' => $secretary->name,
        'asal_instansi' => 'Universitas Test',
        'title' => 'Proposal Sudah Diputuskan',
        'description' => 'Uji coba hasil keputusan',
        'status' => Proposal::STATUS_APPROVED,
        'submission_date' => now()->toDateString(),
        'decision_date' => now(),
    ]);

    ProposalAssignment::create([
        'proposal_id' => $proposal->id,
        'assigned_by' => $secretary->id,
        'assigned_to' => $secretary->id,
        'role' => ProposalAssignment::ROLE_SEKRETARIS,
        'sent_at' => now(),
    ]);

    $review = Review::create([
        'proposal_id' => $proposal->id,
        'reviewer_id' => $secretary->id,
        'status' => Review::STATUS_COMPLETED,
        'assigned_date' => now(),
        'due_date' => now()->addDays(7),
        'completed_date' => now(),
    ]);

    ReviewFeedback::create([
        'review_id' => $review->id,
        'proposal_id' => $proposal->id,
        'feedback_text' => json_encode([
            'general_comments' => 'Feedback sudah masuk',
        ], JSON_UNESCAPED_UNICODE),
        'recommendation' => ReviewFeedback::RECOMMENDATION_APPROVED,
        'is_submitted' => true,
        'submitted_at' => now(),
    ]);

    $this->actingAs($secretary);

    $response = $this->get(route('sekretaris.keputusan'));

    $response->assertOk();
    $response->assertSee($proposal->title);
    $response->assertDontSee('Putuskan');
    $response->assertSee('Sudah diputuskan');
});
