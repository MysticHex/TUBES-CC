<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GroupMemberController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

// Guest (authentication) routes.
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated routes.
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('members', GroupMemberController::class)
        ->parameters(['members' => 'member'])
        ->except(['show']);
    Route::get('/members/{member}', [GroupMemberController::class, 'show'])->name('members.show');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
