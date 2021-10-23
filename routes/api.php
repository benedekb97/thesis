<?php

use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\MachineController;
use Illuminate\Support\Facades\Route;

Route::post('auth', [AuthenticationController::class, 'authenticate'])->name('authenticate');


Route::group(
    [
        'as' => 'machines.',
        'prefix' => 'machines',
    ],
    static function () {
        Route::get('', [MachineController::class, 'index'])->name('index');
    }
);

Route::patch('status', [MachineController::class, 'status'])->name('status');
Route::post('design', [MachineController::class, 'design'])->name('design');
