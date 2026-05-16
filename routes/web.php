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

require __DIR__.'/auth.php';


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
    // Simple login logic - in real app, use proper controller
    $credentials = request()->only('email', 'password');
    if (auth()->attempt($credentials)) {
        $user = auth()->user();
        if ($user->status !== 'active') {
            auth()->logout();
            return back()->withErrors(['email' => 'Akun Anda belum diaktifkan oleh sekretaris.']);
        }
        request()->session()->regenerate();
        return redirect()->intended(route('dashboard'));
    }
    return back()->withErrors(['email' => 'Invalid credentials']);
})->name('login.post')->middleware('guest');

Route::post('/register', function () {
    // Simple register logic - in real app, use proper controller
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

    // Assign default role - peneliti
    $user->assignRole('peneliti');

    // Don't login immediately - wait for activation
    return redirect()->route('login')->with('success', 'Registrasi berhasil. Tunggu aktivasi dari sekretaris.');
})->name('register.post')->middleware('guest');

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout')->middleware('auth');

// Route dashboard peneliti bisa diakses tanpa login
Route::get('/dashboard/peneliti', [ResearcherDashboardController::class, 'index'])
    ->name('peneliti.dashboard');

// ============ ROUTE PANDUAN (Dapat diakses sebelum login) ============
Route::prefix('panduan')->name('panduan.')->group(function () {
    Route::get('/syarat-pendaftaran', [PanduanController::class, 'syaratPendaftaran'])->name('syarat-pendaftaran');
    Route::get('/alur-pengajuan', [PanduanController::class, 'alurPengajuan'])->name('alur-pengajuan');
    Route::get('/panduan-reviewer', [PanduanController::class, 'panduanReviewer'])->name('panduan-reviewer');
});

// ============ ROUTE PANDUAN (Dapat diakses sebelum login) ============
Route::prefix('panduan')->name('panduan.')->group(function () {
    Route::get('/syarat-pendaftaran', [PanduanController::class, 'syaratPendaftaran'])->name('syarat-pendaftaran');
    Route::get('/alur-pengajuan', [PanduanController::class, 'alurPengajuan'])->name('alur-pengajuan');
    Route::get('/panduan-reviewer', [PanduanController::class, 'panduanReviewer'])->name('panduan-reviewer');
});

// ============ ROUTE PENGAJUAN (Dapat diakses sebelum login, tapi isinya pesan login) ============
Route::prefix('pengajuan')->name('pengajuan.')->group(function () {
    Route::get('/upload-proposal', [PengajuanController::class, 'uploadProposal'])->name('upload-proposal');
    Route::get('/download-template', [PengajuanController::class, 'downloadTemplate'])->name('download-template');
    Route::get('/riwayat-pengajuan', [PengajuanController::class, 'riwayatPengajuan'])->name('riwayat-pengajuan');
});
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->hasRole('peneliti')) {
            return redirect()->route('peneliti.dashboard');
        }

        if ($user->hasRole('sekretaris') || $user->hasRole('ketua')) {
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

    // Route untuk Reviewer
    Route::prefix('reviewer')->name('reviewer.')->group(function () {
        // Dashboard reviewer
        Route::get('/dashboard', [ReviewerDashboardController::class, 'index'])
            ->name('dashboard');

        // Proposal Masuk
        Route::get('/proposal-masuk', [ProposalMasukController::class, 'index'])
            ->name('proposal-masuk');

        // Review Proposal
        Route::get('/review-proposal', [ReviewProposalController::class, 'index'])
            ->name('review-proposal');
        Route::post('/review-proposal', [ReviewProposalController::class, 'store'])
            ->name('review-proposal.store');

        // Riwayat Review
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

// Route untuk Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/ethical-clearance', [App\Http\Controllers\Admin\EthicalClearanceController::class, 'index'])->name('ethicalclearance.index');
    Route::get('/publishing', [App\Http\Controllers\Admin\PublishingController::class, 'index'])->name('publishing.index');
    Route::get('/role-permission', [App\Http\Controllers\Admin\RoleAndPermissionController::class, 'index'])->name('role&permission.index');
    Route::get('/system-monitoring', [App\Http\Controllers\Admin\SystemMonitoringController::class, 'index'])->name('systemmonitoring.index');
    Route::get('/template-proposal', [App\Http\Controllers\Admin\TemplateProposalController::class, 'index'])->name('templateproposal.index');
    Route::get('/user-management', [App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('usermanagement.index');

        // ── Template Proposal ────────────────────────
    Route::post  ('templates', [App\Http\Controllers\Admin\TemplateProposalController::class, 'store'])->name('templates.store');
    Route::put   ('templates/{template}', [App\Http\Controllers\Admin\TemplateProposalController::class, 'update'])->name('templates.update');
    Route::delete('templates/{template}', [App\Http\Controllers\Admin\TemplateProposalController::class, 'destroy'])->name('templates.destroy');
    Route::patch ('templates/{template}/toggle', [App\Http\Controllers\Admin\TemplateProposalController::class, 'toggleActive'])->name('templates.toggle');
    Route::get   ('templates/{template}/download', [App\Http\Controllers\Admin\TemplateProposalController::class, 'download'])->name('templates.download');
});

// Route untuk Sekretaris
Route::middleware(['auth', 'role:sekretaris|ketua'])->prefix('sekretaris')->name('sekretaris.')->group(function () {
    Route::get('/  ', [SekretarisController::class, 'dashboard'])->name('dashboard');
    Route::get('/manajemen-proposal', [SekretarisController::class, 'manajemenProposal'])->name('manajemen-proposal');
    Route::get('/assign-reviewer', [SekretarisController::class, 'assignReviewer'])->name('assign-reviewer');
    Route::get('/hasil-review', [SekretarisController::class, 'hasilReview'])->name('hasil-review');
    Route::get('/keputusan', [SekretarisController::class, 'keputusan'])->name('keputusan');
    Route::get('/draf-ethical-clearance', [SekretarisController::class, 'draftEthicalClearance'])->name('draf-ethical-clearance');
    Route::get('/arsip', [SekretarisController::class, 'arsip'])->name('arsip');
    Route::get('/user-management', [SekretarisController::class, 'userManagement'])->name('user-management');
    Route::post('/user-management/activate/{id}', [SekretarisController::class, 'activateUser'])->name('user-management.activate');
});