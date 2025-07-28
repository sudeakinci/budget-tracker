<?php

use App\Http\Controllers\BankController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::apiResource('transactions', TransactionController::class);
Route::apiResource('users', UserController::class);
Route::apiResource('banks', BankController::class);
