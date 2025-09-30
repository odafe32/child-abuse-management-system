<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PoliceController;
use App\Http\Controllers\SocialWorkerController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

// Redirect root to appropriate page based on auth status
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Guest Routes Group (only accessible when NOT logged in)
Route::middleware('guest')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        // Login Routes
        Route::get('/login', 'showLogin')->name('login');
        Route::post('/login', 'login');

        // Password Reset Routes
        Route::get('/forgot-password', 'showForgotPassword')->name('password.request');
        Route::post('/forgot-password', 'sendResetLink')->name('password.email');
        Route::get('/reset-password/{token}', 'showResetForm')->name('password.reset');
        Route::post('/reset-password', 'resetPassword')->name('password.update');
    });
});

// Authenticated Routes Group
Route::middleware(['auth', 'verified'])->group(function () {

    // Logout route (available to all authenticated users)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // General dashboard route (redirects to role-specific dashboard)
    Route::get('/dashboard', function () {
        $user = auth()->user();
        return redirect()->route($user->getDashboardRoute());
    })->name('dashboard');

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'showDashboard'])->name('dashboard');

        // Add more admin routes here as needed
        // Route::get('/users', [AdminController::class, 'manageUsers'])->name('users');
        // Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        // Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    });

    // Social Worker Routes
    Route::middleware(['role:social_worker'])->prefix('social-worker')->name('social-worker.')->group(function () {
        Route::get('/dashboard', [SocialWorkerController::class, 'showDashboard'])->name('dashboard');

        // Profile Routes
        Route::get('/profile', [SocialWorkerController::class, 'showProfile'])->name('profile');
        Route::put('/profile', [SocialWorkerController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profile/password', [SocialWorkerController::class, 'updatePassword'])->name('profile.password');
        Route::delete('/profile/avatar', [SocialWorkerController::class, 'removeAvatar'])->name('profile.remove-avatar');

        // Notification Routes
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::get('/api/get', [NotificationController::class, 'getNotifications'])->name('api.get');
            Route::get('/api/unread-count', [NotificationController::class, 'getUnreadCount'])->name('api.unread-count');
            Route::put('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
            Route::put('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('delete');
        });

        // Case Management Routes
        Route::prefix('cases')->name('cases.')->group(function () {
            // Basic CRUD Routes
            Route::get('/', [SocialWorkerController::class, 'showCases'])->name('index');
            Route::get('/create', [SocialWorkerController::class, 'showCreateCase'])->name('create');
            Route::post('/', [SocialWorkerController::class, 'storeCase'])->name('store');
            Route::get('/{case}', [SocialWorkerController::class, 'showCase'])->name('show');
            Route::get('/{case}/edit', [SocialWorkerController::class, 'editCase'])->name('edit');
            Route::put('/{case}', [SocialWorkerController::class, 'updateCase'])->name('update');
            Route::delete('/{case}', [SocialWorkerController::class, 'deleteCase'])->name('delete');

            // Case Actions
            Route::put('/{case}/status', [SocialWorkerController::class, 'updateCaseStatus'])->name('update-status');
            Route::put('/{case}/assign-police', [SocialWorkerController::class, 'assignPoliceOfficer'])->name('assign-police');
            Route::post('/{case}/notes', [SocialWorkerController::class, 'addCaseNote'])->name('add-note');

            // Print Route
            Route::get('/{case}/print', [SocialWorkerController::class, 'printCase'])->name('print');
        });
    });

    // Police Officer Routes
    Route::middleware(['role:police_officer'])->prefix('police')->name('police.')->group(function () {
        Route::get('/dashboard', [PoliceController::class, 'showDashboard'])->name('dashboard');
        // Profile Routes
        Route::get('/profile', [PoliceController::class, 'showProfile'])->name('profile');
        Route::put('/profile', [PoliceController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profile/password', [PoliceController::class, 'updatePassword'])->name('profile.password');
        Route::delete('/profile/avatar', [PoliceController::class, 'removeAvatar'])->name('profile.remove-avatar');


    // Notifications
    Route::get('/notifications', [PoliceController::class, 'showNotifications'])->name('notifications');
    Route::get('/notifications/api/get', [PoliceController::class, 'getNotifications'])->name('notifications.api.get');
    Route::get('/notifications/api/unread-count', [PoliceController::class, 'getUnreadNotificationCount'])->name('notifications.api.unread-count');
    Route::put('/notifications/{notification}/read', [PoliceController::class, 'markNotificationAsRead'])->name('notifications.mark-as-read');
    Route::put('/notifications/mark-all-read', [PoliceController::class, 'markAllNotificationsAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [PoliceController::class, 'deleteNotification'])->name('notifications.delete');


    Route::get('/assigned-cases', [PoliceController::class, 'showAssignedCases'])->name('assigned-cases');
    Route::get('/cases/{id}', [PoliceController::class, 'showCaseDetails'])->name('case-details');
    Route::get('/cases/{id}/data', [PoliceController::class, 'getCaseData'])->name('case-data');
    Route::put('/cases/{id}/update-status', [PoliceController::class, 'updateCaseStatus'])->name('update-case-status');
    Route::post('/cases/{id}/add-note', [PoliceController::class, 'addInvestigationNote'])->name('add-investigation-note');
    Route::get('/cases/{caseId}/evidence/{filename}', [PoliceController::class, 'downloadEvidence'])->name('download-evidence');


       Route::get('/cases-history', [PoliceController::class, 'showCasesHistory'])->name('cases-history');
    Route::get('/cases/{id}/timeline', [PoliceController::class, 'getCaseTimeline'])->name('case-timeline');


    });
});
