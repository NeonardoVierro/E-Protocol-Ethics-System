<?php

use App\Models\EthicsDocument;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

it('shows the admin publishing page without errors', function () {
    Storage::fake('public');

    Role::firstOrCreate(['name' => 'admin']);

    $admin = User::factory()->create([
        'status' => 'active',
    ]);
    $admin->assignRole('admin');

    $this->actingAs($admin);

    $response = $this->get(route('admin.publishing.index'));

    $response->assertOk();
    $response->assertSee('Certificates Ready for Publishing');
});

it('requires a signed pdf upload before a document can be marked as signed', function () {
    Storage::fake('public');

    Role::firstOrCreate(['name' => 'ketua']);

    $chair = User::factory()->create([
        'status' => 'active',
    ]);
    $chair->assignRole('ketua');

    $proposal = Proposal::create([
        'user_id' => $chair->id,
        'nama_peneliti' => $chair->name,
        'asal_instansi' => 'Universitas Test',
        'title' => 'Contoh Judul Penelitian',
        'description' => 'Uji coba',
        'status' => Proposal::STATUS_WAITING_FOR_CONFIRMATION,
        'submission_date' => now()->toDateString(),
    ]);

    $document = EthicsDocument::create([
        'proposal_id' => $proposal->id,
        'document_number' => 'EC-2026-06-9998',
        'ketua_id' => $chair->id,
        'status' => EthicsDocument::STATUS_DRAFT,
        'file_path' => 'ethics-documents/old.pdf',
        'original_name' => 'old.pdf',
        'notes' => '{}',
    ]);

    $this->actingAs($chair);

    $response = $this->post(route('ketua.sign-document'), [
        'document_id' => $document->id,
    ]);

    $response->assertStatus(422);
    $response->assertJsonPath('error', 'Silakan unggah file PDF hasil tanda tangan terlebih dahulu.');
});

it('publishes a signed ethics certificate and makes it downloadable for the researcher', function () {
    Storage::fake('public');

    foreach (['peneliti', 'ketua', 'admin'] as $roleName) {
        Role::firstOrCreate(['name' => $roleName]);
    }

    $researcher = User::factory()->create([
        'status' => 'active',
    ]);
    $researcher->assignRole('peneliti');

    $chair = User::factory()->create([
        'status' => 'active',
    ]);
    $chair->assignRole('ketua');

    $admin = User::factory()->create([
        'status' => 'active',
    ]);
    $admin->assignRole('admin');

    $proposal = Proposal::create([
        'user_id' => $researcher->id,
        'nama_peneliti' => $researcher->name,
        'asal_instansi' => 'Universitas Test',
        'title' => 'Contoh Judul Penelitian',
        'description' => 'Uji coba',
        'status' => Proposal::STATUS_WAITING_FOR_CONFIRMATION,
        'submission_date' => now()->toDateString(),
    ]);

    $document = EthicsDocument::create([
        'proposal_id' => $proposal->id,
        'document_number' => 'EC-2026-06-9999',
        'ketua_id' => $chair->id,
        'status' => EthicsDocument::STATUS_DRAFT,
        'file_path' => '',
        'original_name' => 'draft.pdf',
        'notes' => json_encode([
            'title' => 'Judul Draft',
            'principal_investigator' => 'Peneliti A',
            'institution' => 'Universitas Test',
            'research_place' => 'Lab',
        ]),
    ]);

    $this->actingAs($chair);
    $uploadedFile = UploadedFile::fake()->create('final-certificate.pdf', 100, 'application/pdf');

    $signResponse = $this->post(route('ketua.sign-document'), [
        'document_id' => $document->id,
        'signed_file' => $uploadedFile,
    ]);

    $signResponse->assertJson(['success' => true]);
    $document->refresh();

    expect($document->status)->toBe(EthicsDocument::STATUS_SIGNED);
    expect(Storage::disk('public')->exists($document->file_path))->toBeTrue();
    expect(Storage::disk('public')->get($document->file_path))->toBe(file_get_contents($uploadedFile->getRealPath()));

    $this->actingAs($admin);
    $publishResponse = $this->post(route('admin.publishing.publish', $document));

    $publishResponse->assertJson(['success' => true]);
    $document->refresh();

    expect($document->status)->toBe(EthicsDocument::STATUS_PUBLISHED);
    expect($proposal->fresh()->status)->toBe(Proposal::STATUS_PUBLISHED);

    $this->actingAs($researcher);
    $ethicalClearancePage = $this->get(route('pengajuan.ethical-clearance'));

    $ethicalClearancePage->assertOk();
    $ethicalClearancePage->assertSee('Unduh Sertifikat Final');

    $this->get(route('pengajuan.riwayat-pengajuan.download-ethics-document', $proposal))
        ->assertDownload('final-certificate.pdf');
});
