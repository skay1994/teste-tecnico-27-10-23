<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ViaCepController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\{LoginController, RegisterController, LogoutController};
use App\Http\Controllers\PatientController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::name('auth.')->prefix('auth')->group(function () {
    Route::post('login', LoginController::class)->name('login');
    Route::post('register', RegisterController::class)->name('register');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('auth/logout', LogoutController::class)->name('auth.logout');
    Route::get('via-cep', ViaCepController::class)->name('via-cep');

    Route::prefix('user')->name('user.')->group(function () {
        Route::get('profile', [UserController::class, 'profile'])->name('profile');
    });

    Route::apiResource('patients', PatientController::class);
});
