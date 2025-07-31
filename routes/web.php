<?php

use Illuminate\Support\Facades\Route;

Route::get('/doc', function () {
    return view('doc');
});

Route::get("/", function () {
    return view('welcome');
});