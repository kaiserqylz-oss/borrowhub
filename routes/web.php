<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\BorrowRequestController;
use App\Http\Controllers\MyBorrowRequestController;
use App\Http\Controllers\OwnerBorrowRequestController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Browse available items
Route::get('/browse', [ItemController::class, 'browse'])
    ->name('items.browse');

// View details of an available item
Route::get('/items/{item}', [ItemController::class, 'show'])
    ->whereNumber('item')
    ->name('items.show');


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->group(function () {
    // Admin dashboard
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Manage reports
    Route::get('/admin/reports', [ReportController::class, 'adminIndex'])
        ->name('admin.reports.index');

    Route::patch('/admin/reports/{report}', [ReportController::class, 'update'])
        ->whereNumber('report')
        ->name('admin.reports.update');
});


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', function () {
        $items = \App\Models\Item::with(['owner', 'category'])
            ->where('status', 'available')
            ->where('owner_id', '!=', auth()->id())
            ->whereDoesntHave('borrowRequests', fn ($requests) => $requests
                ->where('status', 'approved')
                ->whereHas('transaction', fn ($transaction) => $transaction->where('status', 'active')))
            ->latest()
            ->paginate(8);

        return view('dashboard', compact('items'));
    })->middleware('verified')->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | Profile Routes
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [ReportController::class, 'createGeneral'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'storeGeneral'])->name('reports.store');
    Route::get('/reviews', [RatingController::class, 'index'])->name('reviews.index');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');


    /*
    |--------------------------------------------------------------------------
    | BorrowHub Item Management
    |--------------------------------------------------------------------------
    */

    // View My Items
    Route::get('/items', [ItemController::class, 'index'])
        ->name('items.index');

    // Show Add Item Form
    Route::get('/items/create', [ItemController::class, 'create'])
        ->name('items.create');

    // Save New Item
    Route::post('/items', [ItemController::class, 'store'])
        ->name('items.store');

    // Show Edit Item Form
    Route::get('/items/{item}/edit', [ItemController::class, 'edit'])
        ->whereNumber('item')
        ->name('items.edit');

    // Update Item
    Route::put('/items/{item}', [ItemController::class, 'update'])
        ->whereNumber('item')
        ->name('items.update');

    // Deactivate an item
    Route::patch('/items/{item}/deactivate', [ItemController::class, 'deactivate'])
        ->whereNumber('item')
        ->name('items.deactivate');

    // Reactivate an item
    Route::patch('/items/{item}/activate', [ItemController::class, 'activate'])
        ->whereNumber('item')
        ->name('items.activate');


    /*
    |--------------------------------------------------------------------------
    | Borrowing Request Routes
    |--------------------------------------------------------------------------
    */

    // Show the borrowing request form
    Route::get('/items/{item}/borrow', [BorrowRequestController::class, 'create'])
        ->whereNumber('item')
        ->name('borrow-requests.create');

    // Submit a borrowing request
    Route::post('/items/{item}/borrow', [BorrowRequestController::class, 'store'])
        ->whereNumber('item')
        ->name('borrow-requests.store');

    Route::get('/items/{item}/report', [ReportController::class, 'create'])->whereNumber('item')->name('items.report.create');
    Route::post('/items/{item}/report', [ReportController::class, 'store'])->whereNumber('item')->name('items.report.store');
    Route::post('/transactions/{transaction}/rating', [RatingController::class, 'store'])->whereNumber('transaction')->name('transactions.rating.store');

    // View my borrowing requests
    Route::get('/my-borrow-requests', [MyBorrowRequestController::class, 'index'])
        ->name('borrow-requests.index');


    /*
    |--------------------------------------------------------------------------
    | Owner Borrowing Request Management
    |--------------------------------------------------------------------------
    */

    // View requests for items owned by the logged-in user
    Route::get('/owner/borrow-requests', [OwnerBorrowRequestController::class, 'index'])
        ->name('owner.borrow-requests.index');

    // Approve a borrowing request
    Route::patch('/owner/borrow-requests/{borrowRequest}/approve', [OwnerBorrowRequestController::class, 'approve'])
        ->name('owner.borrow-requests.approve');

    // Reject a borrowing request
    Route::patch('/owner/borrow-requests/{borrowRequest}/reject', [OwnerBorrowRequestController::class, 'reject'])
        ->name('owner.borrow-requests.reject');

    // Mark a borrowed item as returned
    Route::patch('/owner/borrow-requests/{borrowRequest}/return', [OwnerBorrowRequestController::class, 'markReturned'])
        ->name('owner.borrow-requests.return');

});


/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
