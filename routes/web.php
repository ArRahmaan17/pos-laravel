<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Dev\AppRoleController;
use App\Http\Controllers\Dev\CustomerRoleController;
use App\Http\Controllers\Dev\RoleController;
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
    Route::name('dev')->as('dev.')->prefix('dev')->group(function () {
        Route::name('app-role')->as('app-role.')->prefix('app-role')->group(function () {
            Route::get('/', [AppRoleController::class, 'index'])->name('index');
            Route::post('/', [AppRoleController::class, 'store'])->name('store');
            Route::put('/{id?}', [AppRoleController::class, 'update'])->name('update');
            Route::get('/data-table', [AppRoleController::class, 'dataTable'])->name('data-table');
            Route::get('/{id?}', [AppRoleController::class, 'show'])->name('show');
            Route::delete('/{id?}', [AppRoleController::class, 'destroy'])->name('delete');
        });
        Route::name('customer-role')->as('customer-role.')->prefix('customer-role')->group(function () {
            Route::get('/', [CustomerRoleController::class, 'index'])->name('index');
            Route::post('/', [CustomerRoleController::class, 'store'])->name('store');
            Route::put('/{id?}', [CustomerRoleController::class, 'update'])->name('update');
            Route::get('/data-table', [CustomerRoleController::class, 'dataTable'])->name('data-table');
            Route::get('/{id?}', [CustomerRoleController::class, 'show'])->name('show');
            Route::delete('/{id?}', [CustomerRoleController::class, 'destroy'])->name('delete');
        });
    });
});
