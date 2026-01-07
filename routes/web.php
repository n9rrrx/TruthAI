<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Auth Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

// Dashboard Routes
Route::prefix('dashboard')->group(function () {
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
        return view('dashboard.history');
    })->name('dashboard.history');

    Route::get('/settings', function () {
        return view('dashboard.settings');
    })->name('dashboard.settings');
});
