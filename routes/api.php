<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PaymentTermController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SmsIntegrationController;
use Illuminate\Support\Facades\Route;


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::apiResource('payment-terms', PaymentTermController::class);
    Route::apiResource('transactions', TransactionController::class)->except(['update']);
    Route::apiResource('users', UserController::class)->except(['store']);
    Route::post('sms-integration/{smsSetting}', [SmsIntegrationController::class, 'receiveSms']);
});

