<?php

use App\Models\EthicsDocument;
use App\Models\Proposal;
use App\Models\ProposalAssignment;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

it('stores a pdf version of the ethics certificate when confirming a draft document', function () {
    Storage::fake('public');

    $researcher = User::factory()->create([
        'status' => 'active',
    ]);

    $this->actingAs($researcher);

    $chair = User::factory()->create();

    $proposal = Proposal::create([
        'user_id' => $researcher->id,
        'nama_peneliti' => $researcher->name,
        'asal_instansi' => 'Universitas Test',
        'title' => 'Contoh Judul Penelitian',
        'description' => 'Uji coba',
        'status' => Proposal::STATUS_WAITING_FOR_CONFIRMATION,
        'nomor_ec' => 'EC-2026-06-0004',
        'submission_date' => now()->toDateString(),
    ]);

    ProposalAssignment::create([
        'proposal_id' => $proposal->id,
        'role' => ProposalAssignment::ROLE_KETUA,
        'assigned_to' => $chair->id,
        'assigned_by' => $researcher->id,
        'sent_at' => null,
    ]);

    EthicsDocument::create([
        'proposal_id' => $proposal->id,
        'document_number' => 'EC-2026-06-0004',
        'status' => EthicsDocument::STATUS_DRAFT,
        'file_path' => '',
        'original_name' => 'draft',
        'notes' => json_encode([
            'title' => 'Judul Draft',
            'principal_investigator' => 'Peneliti A',
            'institution' => 'Universitas Test',
            'research_place' => 'Lab',
        ]),
    ]);

    $this->post(route('pengajuan.ethical-clearance.confirm.submit', $proposal));

    $document = EthicsDocument::where('proposal_id', $proposal->id)->latest('created_at')->first();

    expect($document->file_path)->not->toBeEmpty();
    expect($document->original_name)->toContain('.pdf');
    expect(Storage::disk('public')->exists($document->file_path))->toBeTrue();
});

it('uses a unique ethics document number when confirming a draft document', function () {
    $researcher = User::factory()->create([
        'status' => 'active',
    ]);

    $this->actingAs($researcher);

    $chair = User::factory()->create();

    $proposal = Proposal::create([
        'user_id' => $researcher->id,
        'nama_peneliti' => $researcher->name,
        'asal_instansi' => 'Universitas Test',
        'title' => 'Contoh Judul Penelitian',
        'description' => 'Uji coba',
        'status' => Proposal::STATUS_WAITING_FOR_CONFIRMATION,
        'nomor_ec' => 'EC-2026-06-0004',
        'submission_date' => now()->toDateString(),
    ]);

    ProposalAssignment::create([
        'proposal_id' => $proposal->id,
        'role' => ProposalAssignment::ROLE_KETUA,
        'assigned_to' => $chair->id,
        'assigned_by' => $researcher->id,
        'sent_at' => null,
    ]);

    $sourceDocument = EthicsDocument::create([
        'proposal_id' => $proposal->id,
        'document_number' => 'EC-2026-06-0004-draft',
        'status' => EthicsDocument::STATUS_DRAFT,
        'file_path' => '',
        'original_name' => 'draft',
        'notes' => '{}',
    ]);

    EthicsDocument::create([
        'proposal_id' => $proposal->id,
        'document_number' => 'EC-2026-06-0004',
        'status' => EthicsDocument::STATUS_DRAFT,
        'file_path' => '',
        'original_name' => 'conflict',
        'notes' => '{}',
    ]);

    $response = $this->post(route('pengajuan.ethical-clearance.confirm.submit', $proposal));

    $response->assertRedirect(route('pengajuan.riwayat-pengajuan'));

    $updatedDocument = EthicsDocument::where('proposal_id', $proposal->id)
        ->latest('created_at')
        ->first();

    expect($updatedDocument->document_number)->toBe('EC-2026-06-0004-rev-1');
    expect($proposal->fresh()->status)->toBe(Proposal::STATUS_WITH_CHAIR);
});
