<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::apiResource('banks', BankController::class);
    Route::apiResource('transactions', TransactionController::class)->except(['update', 'destroy']);
    Route::post('sms-transaction', [TransactionController::class, 'createFromSms']);
    Route::apiResource('users', UserController::class)->except(['index', 'store']);
});
