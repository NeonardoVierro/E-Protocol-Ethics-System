<?php

use App\Models\EthicsDocument;
use App\Models\Proposal;
use App\Models\User;

it('allows admin to save certificate preview data', function () {
    $admin = User::factory()->create(['status' => 'active']);
    $admin->assignRole('admin');

    $researcher = User::factory()->create(['status' => 'active']);

    $proposal = Proposal::create([
        'user_id' => $researcher->id,
        'title' => 'Proposal Awal',
        'description' => 'Deskripsi awal',
        'status' => Proposal::STATUS_APPROVED,
        'nomor_ec' => 'EC-2026-06-0001',
    ]);

    $document = EthicsDocument::create([
        'proposal_id' => $proposal->id,
        'document_number' => 'EC-2026-06-0001',
        'status' => EthicsDocument::STATUS_DRAFT,
        'file_path' => '',
        'original_name' => 'draft.pdf',
        'notes' => json_encode(['title' => 'Judul Lama']),
    ]);

    $response = $this->actingAs($admin)
        ->postJson(route('admin.ethicalclearance.save-preview-data', $proposal), [
            'title' => 'Judul Baru',
            'principal_investigator' => 'Budi Santoso',
            'members' => "Andi\nSari",
            'institution' => 'Universitas Digital Indonesia',
            'research_place' => 'Lab Etik',
        ]);

    $response->assertOk();
    $response->assertJsonPath('success', true);

    $document->refresh();
    $notes = json_decode($document->notes, true);

    expect($notes['title'])->toBe('Judul Baru');
    expect($notes['principal_investigator'])->toBe('Budi Santoso');
    expect($notes['members'])->toBe("Andi\nSari");
    expect($notes['institution'])->toBe('Universitas Digital Indonesia');
    expect($notes['research_place'])->toBe('Lab Etik');
});

it('creates a new draft revision when a researcher updates the certificate data', function () {
    $researcher = User::factory()->create(['status' => 'active']);

    $proposal = Proposal::create([
        'user_id' => $researcher->id,
        'title' => 'Proposal Revisi',
        'description' => 'Deskripsi revisi',
        'status' => Proposal::STATUS_WAITING_FOR_CONFIRMATION,
        'nomor_ec' => 'EC-2026-06-0002',
    ]);

    $originalDraft = EthicsDocument::create([
        'proposal_id' => $proposal->id,
        'document_number' => 'EC-2026-06-0002',
        'status' => EthicsDocument::STATUS_DRAFT,
        'file_path' => '',
        'original_name' => 'draft.pdf',
        'notes' => json_encode([
            'title' => 'Judul Lama',
            'principal_investigator' => 'PI Lama',
            'members' => 'Anggota Lama',
            'institution' => 'Institusi Lama',
            'research_place' => 'Tempat Lama',
            'assigned_admin_id' => 99,
            'assigned_at' => now()->toISOString(),
        ]),
    ]);

    $response = $this->actingAs($researcher)
        ->postJson(route('pengajuan.ethical-clearance.save-preview-data', $proposal), [
            'title' => 'Judul Baru',
            'principal_investigator' => 'PI Baru',
            'members' => "Anggota Baru\nTim Baru",
            'institution' => 'Institusi Baru',
            'research_place' => 'Tempat Baru',
        ]);

    $response->assertOk();
    $response->assertJsonPath('success', true);

    $documents = EthicsDocument::where('proposal_id', $proposal->id)
        ->orderBy('created_at')
        ->get();

    expect($documents)->toHaveCount(2);

    $latestDraft = $documents->last();
    $notes = json_decode($latestDraft->notes, true);

    expect($latestDraft->id)->not->toBe($originalDraft->id);
    expect($notes['title'])->toBe('Judul Baru');
    expect($notes['principal_investigator'])->toBe('PI Baru');
    expect($notes['members'])->toBe("Anggota Baru\nTim Baru");
    expect($notes['institution'])->toBe('Institusi Baru');
    expect($notes['research_place'])->toBe('Tempat Baru');
});
