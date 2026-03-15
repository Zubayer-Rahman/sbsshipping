<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobController;
use Illuminate\Support\Facades\Route;

// ── Auth Routes ──────────────────────────────────────────────────────────────
Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register',[AuthController::class, 'register']);
Route::post('/logout',  [AuthController::class, 'logout'])->name('logout');

// ── Protected Routes (must be logged in) ─────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/', fn() => redirect('/dashboard'));

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Jobs Manager
    Route::prefix('jobs')->name('jobs.')->group(function () {
        Route::get('/',                [JobController::class, 'index'])->name('list');
        Route::get('/create',          [JobController::class, 'create'])->name('create');
        Route::post('/store',          [JobController::class, 'store'])->name('store');
        Route::get('/forwarding/page', [JobController::class, 'forwarding'])->name('forwarding');
        Route::get('/{job}',           [JobController::class, 'show'])->name('show');
        Route::get('/{job}/edit',      [JobController::class, 'edit'])->name('edit');
        Route::put('/{job}',           [JobController::class, 'update'])->name('update');
        Route::delete('/{job}',        [JobController::class, 'destroy'])->name('destroy');
    });

});