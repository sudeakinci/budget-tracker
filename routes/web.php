<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/doc', function () {
    return view('doc');
});

Route::get("/", function () {
    return view('welcome');
});


//Login
Route::post('/login', function (AuthController $authController) {
    return $authController->login(request());
});
Route::get('/login', function () {
    return view('auth.login');
});

//Register
Route::post('/register', function (AuthController $authController) {
    return $authController->register(request());
});
Route::get('/register', function () {
    return view('auth.register');
});