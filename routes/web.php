<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('', [DashboardController::class, 'index'])->name('index');

Route::group(
    [
        'as' => 'auth.',
        'prefix' => 'auth',
    ],
    static function () {
        Route::get('redirect', [AuthenticationController::class, 'redirect'])->name('redirect');
        Route::get('callback', [AuthenticationController::class, 'callback'])->name('callback');

        Route::get('logout', [AuthenticationController::class, 'logout'])->name('logout');
    }
);

Route::group(
    [
        'middleware' => 'auth',
    ],
    static function () {
        Route::get('profile', [DashboardController::class, 'profile'])->name('profile');
    }
);

