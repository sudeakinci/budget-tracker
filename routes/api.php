<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionTypeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::post('logout', [AuthController::class, 'logout']);
Route::apiResource('banks', BankController::class);
Route::apiResource('accounts', AccountController::class);
Route::apiResource('transactionTypes', TransactionTypeController::class);
Route::apiResource('transactions', TransactionController::class);
Route::apiResource('users', UserController::class)->except(['store']);


// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('logout', [AuthController::class, 'logout']);
//     Route::apiResource('banks', BankController::class);
//     Route::apiResource('transactions', TransactionController::class)->except(['update', 'destroy']);
//     Route::apiResource('user', UserController::class)->except(['store']);
// });