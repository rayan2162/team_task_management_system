<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    return view('testPage');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::get('/adminPage', function () {
    return view('adminPage');
})->middleware(['auth', \App\Http\Middleware\IsAdmin::class]);

Route::get('/userPage', function () {
    return view('userPage');
})->middleware('auth');