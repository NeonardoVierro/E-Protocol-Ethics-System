<?php

use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\Dashboard\ResearcherDashboardController;
use App\Http\Controllers\Dashboard\SecretaryDashboardController;
use App\Http\Controllers\Dashboard\ReviewerDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Reviewer\ProposalMasukController;
use App\Http\Controllers\Reviewer\ReviewProposalController;
use App\Http\Controllers\Reviewer\RiwayatReviewController;
use App\Http\Controllers\Sekretaris\SekretarisController;
use App\Http\Controllers\Peneliti\PanduanController;
use App\Http\Controllers\Peneliti\PengajuanController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('home');

    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
});

Route::post('/login', function () {
    $credentials = request()->only('email', 'password');
    if (auth()->attempt($credentials)) {
        request()->session()->regenerate();
        return redirect()->intended(route('dashboard'));
    }
    return back()->withErrors(['email' => 'Invalid credentials']);
})->name('login.post')->middleware('guest');

Route::post('/register', function () {
    $data = request()->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required|confirmed|min:8',
    ]);

    $user = \App\Models\User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => bcrypt($data['password']),
    ]);

    $user->assignRole('peneliti');

    return redirect()->route('login')->with('success', 'Registrasi berhasil. Tunggu aktivasi dari sekretaris.');
})->name('register.post')->middleware('guest');

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout')->middleware('auth');

// Dashboard peneliti bisa diakses tanpa login
Route::get('/dashboard/peneliti', [ResearcherDashboardController::class, 'index'])
    ->name('peneliti.dashboard');

// ============ ROUTE PANDUAN ============
Route::prefix('panduan')->name('panduan.')->group(function () {
    Route::get('/syarat-pendaftaran', [PanduanController::class, 'syaratPendaftaran'])->name('syarat-pendaftaran');
    Route::get('/alur-pengajuan', [PanduanController::class, 'alurPengajuan'])->name('alur-pengajuan');
    Route::get('/panduan-reviewer', [PanduanController::class, 'panduanReviewer'])->name('panduan-reviewer');
});

// ============ ROUTE PENGAJUAN ============
Route::prefix('pengajuan')->name('pengajuan.')->group(function () {
    Route::get('/upload-proposal', [PengajuanController::class, 'uploadProposal'])->name('upload-proposal');
    Route::post('/store', [PengajuanController::class, 'store'])->name('store');
    Route::get('/upload-berkas', [PengajuanController::class, 'uploadBerkas'])->name('upload-berkas');
    Route::post('/submit-berkas', [PengajuanController::class, 'submitBerkas'])->name('submit-berkas');
    Route::get('/review', [PengajuanController::class, 'review'])->name('review');
    Route::post('/final-submit', [PengajuanController::class, 'finalSubmit'])->name('final-submit');
    Route::get('/ethical-clearance', [PengajuanController::class, 'ethicalClearance'])->name('ethical-clearance');
    Route::get('/ethical-clearance/{proposal}/confirm', [PengajuanController::class, 'showEthicalClearanceConfirmation'])->name('ethical-clearance.confirm');
    Route::get('/ethical-clearance/{proposal}/preview', [PengajuanController::class, 'previewEthicalClearance'])->name('ethical-clearance.preview');
    Route::post('/ethical-clearance/{proposal}/save-preview-data', [PengajuanController::class, 'saveEthicalClearancePreviewData'])->name('ethical-clearance.save-preview-data');
    Route::post('/ethical-clearance/{proposal}/confirm', [PengajuanController::class, 'confirmEthicalClearance'])->name('ethical-clearance.confirm.submit');
    Route::get('/success', [PengajuanController::class, 'success'])->name('success');
    Route::get('/download-template', [PengajuanController::class, 'downloadTemplate'])->name('download-template');
    Route::get('/riwayat-pengajuan', [PengajuanController::class, 'riwayatPengajuan'])->name('riwayat-pengajuan');
    Route::get('/riwayat-pengajuan/{proposal}', [PengajuanController::class, 'showProposalFeedback'])->name('riwayat-pengajuan.show');
    Route::post('/riwayat-pengajuan/{proposal}/submit-revision', [PengajuanController::class, 'submitRevision'])->name('riwayat-pengajuan.submit-revision');
    Route::get('/riwayat-pengajuan/{proposal}/revisi', [PengajuanController::class, 'showRevisionForm'])->name('riwayat-pengajuan.revision');
    Route::get('/riwayat-pengajuan/{proposal}/revisi/{file}/view', [PengajuanController::class, 'viewRevisionFile'])->name('riwayat-pengajuan.revision-file.view');
    Route::get('/riwayat-pengajuan/{proposal}/revisi/{file}/download', [PengajuanController::class, 'downloadRevisionFile'])->name('riwayat-pengajuan.revision-file.download');
    Route::get('/riwayat-pengajuan/{proposal}/download-ethics-document', [PengajuanController::class, 'downloadEthicsDocument'])->name('riwayat-pengajuan.download-ethics-document');
    Route::get('/download-template/{template}', [PengajuanController::class, 'downloadFile'])->name('download-template.file');
});

// ============ ROUTE NOTIFIKASI PENELITI ============
Route::middleware(['auth'])->prefix('peneliti')->name('peneliti.')->group(function () {
    Route::get('/notifikasi', [App\Http\Controllers\Peneliti\NotificationController::class, 'index'])->name('notifikasi.index');
    Route::get('/notifikasi/latest', [App\Http\Controllers\Peneliti\NotificationController::class, 'getLatest'])->name('notifikasi.latest');
    Route::post('/notifikasi/mark-read/{id}', [App\Http\Controllers\Peneliti\NotificationController::class, 'markAsRead'])->name('notifikasi.mark-read');
    Route::post('/notifikasi/mark-all-read', [App\Http\Controllers\Peneliti\NotificationController::class, 'markAllAsRead'])->name('notifikasi.mark-all-read');
    Route::delete('/notifikasi/{id}', [App\Http\Controllers\Peneliti\NotificationController::class, 'destroy'])->name('notifikasi.destroy');
    Route::post('/notifikasi/clear-read', [App\Http\Controllers\Peneliti\NotificationController::class, 'clearRead'])->name('notifikasi.clear-read');
    Route::get('/notifikasi/redirect/{id}', [App\Http\Controllers\Peneliti\NotificationController::class, 'redirectFromNotification'])->name('notifikasi.redirect');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->hasRole('peneliti')) {
            return redirect()->route('peneliti.dashboard');
        }

        if ($user->hasRole('ketua')) {
            return redirect()->route('ketua.dashboard');
        }

        if ($user->hasRole('sekretaris')) {
            return redirect()->route('sekretaris.dashboard');
        }

        if ($user->hasRole('reviewer')) {
            return redirect()->route('reviewer.dashboard');
        }

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
    })->name('dashboard');

    Route::get('/dashboard/sekretaris', [SecretaryDashboardController::class, 'index'])
        ->name('sekretaris.dashboard');

    Route::prefix('reviewer')->name('reviewer.')->group(function () {
        Route::get('/dashboard', [ReviewerDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/proposal-masuk', [ProposalMasukController::class, 'index'])
            ->name('proposal-masuk');

        Route::redirect('/review-proposal', '/reviewer/proposal-masuk')->name('review-proposal');
        Route::get('/review-proposal/{id}', [ReviewProposalController::class, 'show'])->name('review-proposal.show');
        Route::post('/review-proposal', [ReviewProposalController::class, 'store'])->name('review-proposal.store');

        Route::get('/proposal-file/{file}/download', [ReviewProposalController::class, 'downloadProposalFile'])
            ->name('proposal-file.download');
        Route::get('/proposal-file/{file}/preview', [ReviewProposalController::class, 'previewProposalFile'])
            ->name('proposal-file.preview');

        Route::get('/notifikasi', [App\Http\Controllers\Reviewer\NotificationController::class, 'index'])->name('notifikasi.index');
        Route::get('/notifikasi/latest', [App\Http\Controllers\Reviewer\NotificationController::class, 'getLatest'])->name('notifikasi.latest');
        Route::post('/notifikasi/mark-read/{id}', [App\Http\Controllers\Reviewer\NotificationController::class, 'markAsRead'])->name('notifikasi.mark-read');
        Route::post('/notifikasi/mark-all-read', [App\Http\Controllers\Reviewer\NotificationController::class, 'markAllAsRead'])->name('notifikasi.mark-all-read');
        Route::delete('/notifikasi/{id}', [App\Http\Controllers\Reviewer\NotificationController::class, 'destroy'])->name('notifikasi.destroy');
        Route::post('/notifikasi/clear-read', [App\Http\Controllers\Reviewer\NotificationController::class, 'clearRead'])->name('notifikasi.clear-read');
        Route::get('/notifikasi/redirect/{id}', [App\Http\Controllers\Reviewer\NotificationController::class, 'redirectFromNotification'])->name('notifikasi.redirect');

        Route::get('/riwayat-review', [RiwayatReviewController::class, 'index'])
            ->name('riwayat-review');
        Route::get('/riwayat-review/{id}', [RiwayatReviewController::class, 'show'])
            ->name('riwayat-review.show');
    });

    Route::get('/dashboard/admin', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ============ ROUTE ADMIN ============
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/ethical-clearance', [App\Http\Controllers\Admin\EthicalClearanceController::class, 'index'])->name('ethicalclearance.index');
    Route::get('/publishing', [App\Http\Controllers\Admin\PublishingController::class, 'index'])->name('publishing.index');
    Route::get('/role-permission', [App\Http\Controllers\Admin\RoleAndPermissionController::class, 'index'])->name('role&permission.index');
    Route::get('/system-monitoring', [App\Http\Controllers\Admin\SystemMonitoringController::class, 'index'])->name('systemmonitoring.index');
    Route::get('/template-proposal', [App\Http\Controllers\Admin\TemplateProposalController::class, 'index'])->name('templateproposal.index');
    Route::get('/user-management', [App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('usermanagement.index');

    Route::post('templates', [App\Http\Controllers\Admin\TemplateProposalController::class, 'store'])->name('templates.store');
    Route::put('templates/{template}', [App\Http\Controllers\Admin\TemplateProposalController::class, 'update'])->name('templates.update');
    Route::delete('templates/{template}', [App\Http\Controllers\Admin\TemplateProposalController::class, 'destroy'])->name('templates.destroy');
    Route::patch('templates/{template}/toggle', [App\Http\Controllers\Admin\TemplateProposalController::class, 'toggleActive'])->name('templates.toggle');
    Route::get('templates/{template}/download', [App\Http\Controllers\Admin\TemplateProposalController::class, 'download'])->name('templates.download');

    Route::prefix('ethical-clearance')->name('ethicalclearance.')->group(function () {
        Route::get('/ketua-list', [App\Http\Controllers\Admin\EthicalClearanceController::class, 'getKetuaList'])->name('ketua-list');
        Route::get('/get-assignment', [App\Http\Controllers\Admin\EthicalClearanceController::class, 'getAssignment'])->name('get-assignment');
        Route::post('/pilih-ketua', [App\Http\Controllers\Admin\EthicalClearanceController::class, 'pilihKetua'])->name('pilih-ketua');
        Route::post('/{proposal}/save-preview-data', [App\Http\Controllers\Admin\EthicalClearanceController::class, 'savePreviewData'])->name('save-preview-data');
        Route::post('/kirim-ketua', [App\Http\Controllers\Admin\EthicalClearanceController::class, 'kirimKetua'])->name('kirim-ketua');
        Route::post('/generate-nomor-ec', [App\Http\Controllers\Admin\EthicalClearanceController::class, 'generateNomorEc'])->name('generate-nomor-ec');
    });

    Route::prefix('publishing')->name('publishing.')->group(function () {
        Route::post('/{document}/publish', [App\Http\Controllers\Admin\PublishingController::class, 'publish'])->name('publish');
        Route::post('/bulk-publish', [App\Http\Controllers\Admin\PublishingController::class, 'bulkPublish'])->name('bulk-publish');
    });

    Route::prefix('proposal-assignment')->name('proposal-assignment.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ProposalAssignmentController::class, 'index'])->name('index');
        Route::get('/sekretaris-list', [App\Http\Controllers\Admin\ProposalAssignmentController::class, 'getSekretarisList'])->name('sekretaris-list');
        Route::post('/{proposal}/pilih-sekretaris', [App\Http\Controllers\Admin\ProposalAssignmentController::class, 'pilihSekretaris'])->name('pilih-sekretaris');
        Route::post('/{proposal}/kirim-sekretaris', [App\Http\Controllers\Admin\ProposalAssignmentController::class, 'kirimSekretaris'])->name('kirim-sekretaris');
    });

    Route::get('/proposal/{proposal}/preview', [App\Http\Controllers\Admin\ProposalAssignmentController::class, 'previewProposal'])->name('proposal.preview');
});

// ============ ROUTE SEKRETARIS ============
Route::middleware(['auth', 'role:sekretaris|ketua'])->prefix('sekretaris')->name('sekretaris.')->group(function () {
    Route::get('/', [SekretarisController::class, 'dashboard'])->name('dashboard');
    Route::get('/manajemen-proposal', [SekretarisController::class, 'manajemenProposal'])->name('manajemen-proposal');
    Route::get('/proposal/{proposal}', [SekretarisController::class, 'showProposal'])->name('proposal.show');
    Route::post('/proposal/{proposal}/review-type', [SekretarisController::class, 'updateReviewType'])->name('proposal.update-review-type');
    Route::post('/proposal/{proposal}/assign-reviewer', [SekretarisController::class, 'assignReviewerToProposal'])->name('proposal.assign-reviewer');
    Route::post('/proposal/{proposal}/send-to-reviewer', [SekretarisController::class, 'sendProposalToReviewer'])->name('proposal.send-to-reviewer');
    Route::get('/proposal/{proposal}/activity-logs', [SekretarisController::class, 'activityLogs'])->name('proposal.activity-logs');
    Route::get('/proposal-file/{file}/view', [SekretarisController::class, 'viewProposalFile'])->name('proposal-file.view');
    Route::get('/proposal-file/{file}/download', [SekretarisController::class, 'downloadProposalFile'])->name('proposal-file.download');
    Route::get('/hasil-review', [SekretarisController::class, 'hasilReview'])->name('hasil-review');
    Route::get('/hasil-review/{proposal}', [SekretarisController::class, 'hasilReviewShow'])->name('hasil-review.show');
    Route::get('/keputusan', [SekretarisController::class, 'keputusan'])->name('keputusan');
    Route::post('/keputusan/update', [SekretarisController::class, 'updateDecision'])->name('keputusan.update');
    Route::get('/draf-ethical-clearance', [SekretarisController::class, 'draftEthicalClearance'])->name('draf-ethical-clearance');
    Route::post('/draf-ethical-clearance', [SekretarisController::class, 'storeDraft'])->name('draf-ethical-clearance.store');
    Route::post('/draf-ethical-clearance/{document}/send', [SekretarisController::class, 'sendDraft'])->name('draf-ethical-clearance.send');
    Route::post('/draf-ethical-clearance/send-to-admin', [SekretarisController::class, 'sendToAdmin'])->name('draf-ethical-clearance.sendToAdmin');
    Route::get('/arsip', [SekretarisController::class, 'arsip'])->name('arsip');
    Route::get('/arsip-dokumen', [SekretarisController::class, 'arsipDokumen'])->name('arsip-dokumen');
    Route::get('/user-management', [SekretarisController::class, 'userManagement'])->name('user-management');
    Route::post('/user-management/activate/{id}', [SekretarisController::class, 'activateUser'])->name('user-management.activate');
});

// ============ ROUTE KETUA ============
Route::middleware(['auth', 'role:ketua'])->prefix('ketua')->name('ketua.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Ketua\KetuaController::class, 'dashboard'])->name('dashboard');
    Route::get('/persetujuan-ttd', [App\Http\Controllers\Ketua\KetuaController::class, 'persetujuanTtd'])->name('persetujuan-ttd');
    Route::get('/ethics-preview/{document}/download', [App\Http\Controllers\Ketua\KetuaController::class, 'downloadPreviewPdf'])->name('ethics-preview-download');
    Route::post('/sign-document', [App\Http\Controllers\Ketua\KetuaController::class, 'signDocument'])->name('sign-document');
});
