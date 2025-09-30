<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PoliceController;
use App\Http\Controllers\SocialWorkerController;
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


    });

    // Social Worker Routes
    Route::middleware(['role:social_worker'])->prefix('social-worker')->name('social-worker.')->group(function () {
        Route::get('/dashboard', [SocialWorkerController::class, 'showDashboard'])->name('dashboard');

        // Profile Routes
        Route::get('/profile', [SocialWorkerController::class, 'showProfile'])->name('profile');
        Route::put('/profile', [SocialWorkerController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profile/password', [SocialWorkerController::class, 'updatePassword'])->name('profile.password');
        Route::delete('/profile/avatar', [SocialWorkerController::class, 'removeAvatar'])->name('profile.remove-avatar');
          Route::get('/add-cases', [SocialWorkerController::class, 'showAddCases'])->name('add-cases');
        Route::get('/cases', [SocialWorkerController::class, 'showCases'])->name('cases');


    });

    // Police Officer Routes
    Route::middleware(['role:police_officer'])->prefix('police')->name('police.')->group(function () {
        Route::get('/dashboard', [PoliceController::class, 'showDashboard'])->name('dashboard');


    });
});
