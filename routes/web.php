<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Middleware\loggedInUser;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('guest')->name('auth.')->prefix('auth')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
    Route::get('/registration', [AuthController::class, 'register'])->name('registration');
    Route::post('/registration', [AuthController::class, 'registration'])->name('registration.process');
});
Route::middleware([loggedInUser::class])->group(function () {
    Route::get('/', function () {
        return view('home');
    })->name('home');
});
