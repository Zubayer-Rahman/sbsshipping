<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ForwardingController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\JobController;
use Illuminate\Support\Facades\Route;

// ── Auth Routes ─────────────────────────────────────────────────────────────
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

// ── Protected Routes ─────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/', fn() => redirect('/dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Jobs Manager ─────────────────────────────────────────────────────────
    Route::prefix('jobs')->name('jobs.')->group(function () {
        Route::get('/',                [JobController::class, 'index'])->name('list');
        Route::get('/create',          [JobController::class, 'create'])->name('create');
        Route::post('/store',          [JobController::class, 'store'])->name('store');
        Route::get('/forwarding/page', [ForwardingController::class, 'create'])->name('forwarding');
        Route::get('/{job}',           [JobController::class, 'show'])->name('show');
        Route::get('/{job}/edit',      [JobController::class, 'edit'])->name('edit');
        Route::put('/{job}',           [JobController::class, 'update'])->name('update');
        Route::delete('/{job}',        [JobController::class, 'destroy'])->name('destroy');
    });

    // ── Forwarding Letters ───────────────────────────────────────────────────
    Route::prefix('forwarding')->name('forwarding.')->group(function () {
        Route::get('/list',             [ForwardingController::class, 'index'])->name('list');
        Route::post('/store',           [ForwardingController::class, 'store'])->name('store');
        Route::get('/preview/{letter}', [ForwardingController::class, 'preview'])->name('preview');
        Route::delete('/{letter}',      [ForwardingController::class, 'destroy'])->name('destroy');
        Route::get('/jobs-for-contact', [ForwardingController::class, 'jobsForContact'])->name('jobs');
    });

    // ── Items ────────────────────────────────────────────────────────────────
    Route::prefix('items')->name('items.')->group(function () {
        Route::get('/',            [ItemController::class, 'index'])->name('list');
        Route::get('/create',      [ItemController::class, 'create'])->name('create');
        Route::post('/store',      [ItemController::class, 'store'])->name('store');
        Route::get('/{item}/edit', [ItemController::class, 'edit'])->name('edit');
        Route::put('/{item}',      [ItemController::class, 'update'])->name('update');
        Route::delete('/{item}',   [ItemController::class, 'destroy'])->name('destroy');
    });

    // ── Contacts ─────────────────────────────────────────────────────────────
    Route::prefix('contacts')->name('contacts.')->group(function () {
        Route::get('/create/new',        [ContactController::class, 'create'])->name('create');
        Route::post('/store',            [ContactController::class, 'store'])->name('store');
        Route::get('/show/{contact}',    [ContactController::class, 'show'])->name('show');
        Route::get('/{contact}/edit',    [ContactController::class, 'edit'])->name('edit');
        Route::put('/{contact}',         [ContactController::class, 'update'])->name('update');
        Route::delete('/{contact}',      [ContactController::class, 'destroy'])->name('destroy');
        Route::post('/{contact}/toggle', [ContactController::class, 'toggleActive'])->name('toggle');
        Route::get('/{type}',            [ContactController::class, 'index'])
            ->where('type', 'supplier|client')
            ->name('index');
    });

    // ── Expenses ─────────────────────────────────────────────────────────────
    Route::prefix('expenses')->name('expenses.')->group(function () {
        Route::get('/',                    [ExpenseController::class, 'index'])->name('list');
        Route::get('/create',              [ExpenseController::class, 'create'])->name('create');
        Route::post('/store',              [ExpenseController::class, 'store'])->name('store');
        Route::get('/{expense}/edit',      [ExpenseController::class, 'edit'])->name('edit');
        Route::put('/{expense}',           [ExpenseController::class, 'update'])->name('update');
        Route::delete('/{expense}',        [ExpenseController::class, 'destroy'])->name('destroy');
        Route::get('/subcategories/ajax',  [ExpenseController::class, 'subcategories'])->name('subcategories');
    });

    // ── Expense Categories ────────────────────────────────────────────────────
    Route::prefix('expense-categories')->name('expense-categories.')->group(function () {
        Route::get('/',              [ExpenseCategoryController::class, 'index'])->name('list');
        Route::post('/store',        [ExpenseCategoryController::class, 'store'])->name('store');
        Route::put('/{expenseCategory}',    [ExpenseCategoryController::class, 'update'])->name('update');
        Route::delete('/{expenseCategory}', [ExpenseCategoryController::class, 'destroy'])->name('destroy');
    });

});