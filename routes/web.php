<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DesignController;
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

        Route::post('profile/password', [DashboardController::class, 'password'])->name('profile.password');

        Route::get('design/{design}/colors', [DesignController::class, 'colors'])->name('design.colors');
        Route::post('design/{design}/colors', [DesignController::class, 'saveColors'])->name('design.colors');
    }
);

