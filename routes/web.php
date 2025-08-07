<?php

use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\AuthController;
use \App\Http\Controllers\Web\TransactionController;
use App\Http\Controllers\Web\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/doc', function () {
    return view('doc');
});

Route::get("/", function () {
    return view('welcome');
});

//Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard route (protected by auth middleware)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// Redirect root to dashboard if authenticated
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return view('welcome');
});

Route::get('/transactions', [TransactionController::class, 'index'])
    ->middleware('auth')
    ->name('transactions');

Route::post('/transactions', [TransactionController::class, 'store'])
    ->middleware('auth')
    ->name('transactions.store');

Route::delete('/transactions/{id}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

Route::get('/profile/{id?}', [ProfileController::class, 'show'])
    ->middleware('auth')
    ->name('profile');

Route::post('/profile/{id?}', [ProfileController::class, 'update'])
    ->middleware('auth')
    ->name('profile');

Route::delete('/profile/{id}', [ProfileController::class, 'destroy'])
    ->middleware('auth')
    ->name('profile.delete');