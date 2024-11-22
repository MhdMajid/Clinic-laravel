<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PasswordResetController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {

    Route::post('/register_doctor', 'register_doctor')
            ->middleware('auth:admin')
            ->name('register');

    Route::post('/login', 'login')
            //->middleware(['auth:admin','auth:doctor'])
            ->name('login');

    Route::get('/logout', 'logout')
            //->middleware(['auth:admin','auth:doctor'])
            ->name('logout');

    Route::post('/refresh', 'refresh');
});

Route::controller(PasswordResetController::class)->group(function () {

    Route::post('/forgot-password',  'sendResetLink')
            ->middleware('guest:doctor')
            ->name('forgot.password');
    
    Route::post('/reset-password', 'resetPassword')
           // ->middleware('guest:doctor')
            ->name('reset.password');

    Route::post('/change-password', 'change_password')
            ->middleware('auth:doctor')
            ->name('change.password');        
});
