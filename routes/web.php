<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\HumanizerController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Auth Routes (Guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    
    // Google OAuth
    Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
});

// Logout (Auth only)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard Routes (Auth only)
Route::prefix('dashboard')->middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/detector', function () {
        return view('dashboard.detector');
    })->name('dashboard.detector');

    Route::get('/humanizer', function () {
        return view('dashboard.humanizer');
    })->name('dashboard.humanizer');

    Route::get('/history', function () {
        $scans = auth()->user()->scans()->latest()->paginate(15);
        return view('dashboard.history', compact('scans'));
    })->name('dashboard.history');

    Route::get('/settings', function () {
        return view('dashboard.settings');
    })->name('dashboard.settings');

    // Scan API Routes
    Route::post('/scan', [ScanController::class, 'store'])->name('scan.store');
    Route::get('/scan/{scan}', [ScanController::class, 'show'])->name('scan.show');
    Route::delete('/scan/{scan}', [ScanController::class, 'destroy'])->name('scan.destroy');
    Route::get('/providers', [ScanController::class, 'providers'])->name('scan.providers');

    // Humanizer API Routes
    Route::post('/humanize', [HumanizerController::class, 'humanize'])->name('humanize');
    Route::post('/humanize/regenerate', [HumanizerController::class, 'regenerate'])->name('humanize.regenerate');

    // Image/Video Analysis Routes
    Route::post('/analyze-image', [ImageController::class, 'analyze'])->name('analyze.image');
    Route::post('/analyze-video', [ImageController::class, 'analyzeVideo'])->name('analyze.video');
});
