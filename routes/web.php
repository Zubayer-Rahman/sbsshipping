<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ForwardingController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobGroupController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PaymentAccountController;
use App\Http\Controllers\AdditionalExpenseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\IouController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BillController;
use Illuminate\Support\Facades\Route;

// ── Auth Routes ─────────────────────────────────────────────────────────────
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//Registration
Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

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

    //job groups
    Route::prefix('job-groups')->name('job-groups.')->group(function () {
        Route::get('/', [JobGroupController::class, 'index'])->name('index');
        Route::get('/create', [JobGroupController::class, 'create'])->name('create');
        Route::post('/', [JobGroupController::class, 'store'])->name('store');
        Route::post('/quick-store', [JobGroupController::class, 'quickStore'])->name('quickStore');
        Route::get('/{jobGroup}', [JobGroupController::class, 'show'])->name('show');
        Route::get('/{jobGroup}/edit', [JobGroupController::class, 'edit'])->name('edit');
        Route::put('/{jobGroup}', [JobGroupController::class, 'update'])->name('update');
        Route::delete('/{jobGroup}', [JobGroupController::class, 'destroy'])->name('destroy');
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
        Route::get('/user-list',         [UserController::class, 'index'])->name('user');
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
        Route::get('/additional',          [ExpenseController::class, 'additionalExpenses'])->name('additionalExpenses');
        Route::get('/create',              [ExpenseController::class, 'create'])->name('create');
        Route::post('/store',              [ExpenseController::class, 'store'])->name('store');
        Route::get('/{expense}/edit',      [ExpenseController::class, 'edit'])->name('edit');
        Route::put('/{expense}',           [ExpenseController::class, 'update'])->name('update');
        Route::delete('/{expense}',        [ExpenseController::class, 'destroy'])->name('destroy');
        Route::get('/subcategories/ajax',  [ExpenseController::class, 'subcategories'])->name('subcategories');
        Route::get('/show/{expense}',        [ExpenseController::class, 'show'])->name('show');
    });

    // ── Expense Categories ────────────────────────────────────────────────────
    Route::prefix('expense-categories')->name('expense-categories.')->group(function () {
        Route::get('/',              [ExpenseCategoryController::class, 'index'])->name('list');
        Route::post('/store',        [ExpenseCategoryController::class, 'store'])->name('store');
        Route::put('/{expenseCategory}',    [ExpenseCategoryController::class, 'update'])->name('update');
        Route::delete('/{expenseCategory}', [ExpenseCategoryController::class, 'destroy'])->name('destroy');
    });

    // Purchases
    Route::prefix('purchases')->name('purchases.')->group(function () {
        Route::get('/',                  [PurchaseController::class, 'index'])->name('list');
        Route::get('/create',            [PurchaseController::class, 'create'])->name('create');
        Route::post('/store',            [PurchaseController::class, 'store'])->name('store');
        Route::get('/{purchase}',        [PurchaseController::class, 'show'])->name('show');
        Route::delete('/{purchase}',     [PurchaseController::class, 'destroy'])->name('destroy');
        Route::get('/items/search',      [PurchaseController::class, 'searchItems'])->name('items.search');
    });


    // IOU Management Routes
    Route::prefix('ious')->name('ious.')->group(function () {
        Route::get('/', [IouController::class, 'index'])->name('index');
        Route::get('/create', [IouController::class, 'create'])->name('create');
        Route::post('/', [IouController::class, 'store'])->name('store');
        Route::get('/released', [IouController::class, 'releaseList'])->name('release-list'); // ADD THIS
        Route::get('/iou-expenses', [IouController::class, 'iouExpenseList'])->name('expense-list'); // ADD THIS
        Route::post('/{iou}/release-instant', [IouController::class, 'releaseInstant'])->name('release-instant');
        Route::get('/{iou}', [IouController::class, 'show'])->name('show');
        Route::get('/{iou}/edit', [IouController::class, 'edit'])->name('edit');
        Route::put('/{iou}', [IouController::class, 'update'])->name('update');
        Route::delete('/{iou}', [IouController::class, 'destroy'])->name('destroy');
        Route::post('/{iou}/payment', [IouController::class, 'addPayment'])->name('payment');
        Route::get('/{iou}/release', [IouController::class, 'release'])->name('release');      // ADD THIS
        Route::post('/{iou}/release', [IouController::class, 'processRelease'])->name('process-release'); // ADD THIS
    });

    // Payment Accounts
    Route::prefix('accounts')->name('accounts.')->group(function () {
        Route::get('/', [PaymentAccountController::class, 'index'])->name('index');
        Route::get('/create', [PaymentAccountController::class, 'create'])->name('create');
        Route::get('/cashflow/view', [PaymentAccountController::class, 'cashFlow'])->name('cashflow');
        Route::post('/', [PaymentAccountController::class, 'store'])->name('store');
        Route::delete('/{account}', [PaymentAccountController::class, 'destroy'])->name('destroy');
        Route::get('/{account}', [PaymentAccountController::class, 'show'])->name('show');
        Route::post('/{account}/toggle', [PaymentAccountController::class, 'toggleActive'])->name('toggle');
    });


    // Bills
    Route::prefix('bills')->name('bills.')->group(function () {
        Route::get('/', [BillController::class, 'index'])->name('list');
        Route::get('/create', [BillController::class, 'create'])->name('create');
        Route::post('/store', [BillController::class, 'store'])->name('store');
        Route::get('/{bill}/print', [BillController::class, 'print'])->name('print');
        Route::post('/{bill}/addpayment', [BillController::class, 'addPayment'])->name('add-payment');
        Route::get('/{bill}', [BillController::class, 'show'])->name('show');
        Route::delete('/{bill}', [BillController::class, 'destroy'])->name('destroy');
        Route::get('/{bill}/edit', [BillController::class, 'edit'])->name('edit');
        Route::put('/{bill}', [BillController::class, 'update'])->name('update');
        Route::get('/items/search', [BillController::class, 'searchItems'])->name('items.search');
        Route::post('/{bill}/payment', [BillController::class, 'addPayment'])->name('addPayment');
        Route::get('/get-job-details/{jobId}', [BillController::class, 'getJobDetails'])->name('getJobDetails');
        Route::get('/bills/{bill}/print', [BillController::class, 'print'])->name('bills.print');
    });

    // Additional Expenses
    Route::prefix('additional-expenses')->name('additional-expenses.')->group(function () {
        Route::get('/', [AdditionalExpenseController::class, 'index'])->name('index');
        Route::get('/create', [AdditionalExpenseController::class, 'create'])->name('create');
        Route::post('/', [AdditionalExpenseController::class, 'store'])->name('store');
        Route::get('/get-by-jobs', [AdditionalExpenseController::class, 'getByJobs'])->name('getByJobs');
        Route::get('/{additionalExpense}', [AdditionalExpenseController::class, 'show'])->name('show');
        Route::get('/{additionalExpense}/edit', [AdditionalExpenseController::class, 'edit'])->name('edit');
        Route::put('/{additionalExpense}', [AdditionalExpenseController::class, 'update'])->name('update');
        Route::delete('/{additionalExpense}', [AdditionalExpenseController::class, 'destroy'])->name('destroy');
    });

    //reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/clients', [ReportController::class, 'clientReport'])->name('clients');
        Route::get('/suppliers', [ReportController::class, 'supplierReport'])->name('suppliers');
        Route::get('/contact/{contact}', [ReportController::class, 'contactLedger'])->name('contact.ledger');
        Route::get('/expenses', [ReportController::class, 'expenseReport'])->name('expense');
        Route::get('/income', [ReportController::class, 'incomeReport'])->name('income');
    });
});
