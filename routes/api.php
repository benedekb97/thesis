<?php

use App\Http\Controllers\Api\AuthenticationController;
use Illuminate\Support\Facades\Route;

Route::post('auth', [AuthenticationController::class, 'authenticate'])->name('authenticate');
