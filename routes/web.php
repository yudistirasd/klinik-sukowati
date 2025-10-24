<?php

use App\Http\Controllers\AuthenticationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthenticationController::class, 'showLoginForm']);
Route::post('/login', [AuthenticationController::class, 'authenticate'])->name('login');


Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticationController::class, 'logout'])->name('logout');
    Route::get('/dashboard', function () {
        return view('layouts.app');
    })->name('dashboard');
});
