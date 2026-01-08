<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\SocialAuthController;
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
    Route::get('/', function () {
        return view('dashboard.index');
    })->name('dashboard');

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
});
