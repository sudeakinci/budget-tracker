<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentTermController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return view('welcome');
});


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::apiResource('payment-terms', PaymentTermController::class);
Route::apiResource('transactions', TransactionController::class)->except(['update']);
Route::apiResource('users', UserController::class)->except(['store']);


// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('logout', [AuthController::class, 'logout']);
//     Route::apiResource('banks', BankController::class);
//     Route::apiResource('transactions', TransactionController::class)->except(['update', 'destroy']);
//     Route::apiResource('user', UserController::class)->except(['store']);
// });

Route::get('endpoints', function () {
    $documentation = config('documentation');
    $authEndpoints = ['register', 'login', 'logout'];
    $resourceEndpoints = ['banks', 'accounts', 'transactions', 'transactionTypes', 'users'];
    $routes = [];

    foreach ($documentation as $key => $value) {
        if (in_array($key, $authEndpoints)) {
            $routes[] = [
                'uri' => 'api/' . $key,
                'method' => 'POST', // Assuming auth routes are POST
                'description' => $value['description'],
                'request' => $value['request'] ?? null,
                'responses' => $value['responses'] ?? [],
            ];
        } elseif (in_array($key, $resourceEndpoints)) {
            $routes[] = [
                'uri' => 'api/' . $key,
                'endpoints' => $value,
            ];
        }
    }

    return response()->json($routes);
});