<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('throttle:100,1')->group(function () {
    Route::get('/check-available-user', [AuthController::class, 'checkAvailableUser'])
        ->name('check-available-user');
    Route::get('/company-types', [AuthController::class, 'companyTypes'])
        ->name('company-types');
    Route::get('/check-company-availability', [AuthController::class, 'checkAvailableCompany'])
        ->name('check-company-availability');
    Route::post('/registration', [AuthController::class, 'registration'])
        ->name('registration');
});
