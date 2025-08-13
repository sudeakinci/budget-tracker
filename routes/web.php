<?php

use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\AuthController;
use \App\Http\Controllers\Web\TransactionController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\PaymentTermController;
use \App\Http\Controllers\Web\LedgerController;
use Illuminate\Support\Facades\Route;

Route::get('/doc', function () {
    return view('doc');
});

Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : view('/login');
});

//authentication routes
Route::controller(AuthController::class)->group(function () {
    Route::get('/', 'showLoginForm')->name('login');
    Route::get('/login', 'showLoginForm')->name('login.form');
    Route::post('/login', 'login')->middleware('throttle:5,1'); // 5 attempts per minute
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register')->middleware('throttle:3,1'); // 3 attempts per minute
    Route::post('/logout', 'logout')->name('logout')->middleware('auth');

    // unlock account routes
    Route::get('/unlock-account', 'showUnlockForm')->name('unlock.account.request');
    Route::post('/unlock-account', 'sendUnlockCode')->name('unlock.account.send')->middleware('throttle:3,1'); // 3 attempts per minute
    Route::post('/unlock-account/verify', 'verifyUnlockCode')->name('unlock.account.verify')->middleware('throttle:5,1'); // 5 attempts per minute

    Route::get('/verify-email', 'verifyEmail')->name('verify.email');
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->middleware('auth')->name('verification.notice');
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])
        ->middleware(['auth', 'throttle:6,1'])
        ->name('verification.send');
});

// protected routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // transaction routes
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::put('/transactions/{id}', [TransactionController::class, 'update'])->name('transactions.update');
    Route::post('/transactions/{id}/update-inclusion', [TransactionController::class, 'updateInclusion'])->name('transactions.update-inclusion');
    Route::delete('/transactions/{id}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
    Route::post('/transactions/{id}/update-inclusion', [TransactionController::class, 'updateInclusion'])->name('transactions.update-inclusion');

    // ledger routes
    Route::get('/ledger', [LedgerController::class, 'index'])->name('ledger');

    // payment terms routes
    Route::get('/payment-terms', [PaymentTermController::class, 'index'])->name('payment-terms');
    Route::post('/ledger', [LedgerController::class, 'store'])->name('ledger.store');

    // profile routes
    Route::get('/profile/{id?}', [ProfileController::class, 'show'])->name('profile');
    Route::post('/profile/{id?}', [ProfileController::class, 'update'])->name('profile');
    Route::delete('/profile/{id}', [ProfileController::class, 'destroy'])->name('profile.delete');
    Route::post('/profile/{id}/balance-update', [ProfileController::class, 'updateBalance'])->name('profile.balance.update');

    // payment terms routes
    Route::put('/payment-terms/{paymentTerm}', [PaymentTermController::class, 'update'])->name('payment-terms.update');
    Route::delete('/payment-terms/{paymentTerm}', [PaymentTermController::class, 'destroy'])->name('payment-terms.destroy');

    Route::get('/users/search', [ProfileController::class, 'search'])->name('users.search');

});