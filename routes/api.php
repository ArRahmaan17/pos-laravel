<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Dev\AppGoodUnitController;
use App\Http\Controllers\Api\Dev\AppMenuController;
use App\Http\Controllers\Api\Dev\AppRoleController;
use App\Http\Controllers\Api\Dev\AppSubscriptionController;
use App\Http\Controllers\Api\Man\CustomerCompanyGoodController;
use App\Http\Controllers\Api\Man\CustomerProductTypeController;
use App\Http\Controllers\Api\Man\CustomerTemporaryProductController;
use App\Http\Controllers\Api\Man\UserCustomerController;
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

// Public routes (no authentication required)
Route::middleware('throttle:100,1')->group(function () {
    Route::get('/', function () {
        return response()->json([
            'message' => 'Welcome to the API',
        ], 200);
    });
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->name('api.login');
        Route::post('/register', [AuthController::class, 'register'])->name('api.register');
        Route::get('/check-available-user', [AuthController::class, 'checkAvailableUser'])->name('check-available-user');
        Route::get('/company-types', [AuthController::class, 'companyTypes'])->name('company-types');
        Route::get('/check-company-availability', [AuthController::class, 'checkAvailableCompany'])->name('check-company-availability');
    });
});

// Protected routes (authentication required)
Route::middleware(['auth:sanctum', 'throttle:100,1'])->group(function () {
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
        Route::post('/logout-all', [AuthController::class, 'logoutAll'])->name('api.logout-all');
        Route::get('/me', [AuthController::class, 'me'])->name('api.me');
        Route::post('/select-company', [AuthController::class, 'selectCompany'])->name('api.select-company');
        Route::post('/login-as/{id}', [AuthController::class, 'loginAs'])->name('api.login-as');
        Route::post('/change-password', [AuthController::class, 'changePassword'])->name('api.change-password');
        Route::post('/activate-access-pin', [AuthController::class, 'activateAccessPin'])->name('api.activate-access-pin');
        Route::post('/unlock-screen', [AuthController::class, 'unlockScreen'])->name('api.unlock-screen');
        Route::get('/customer-companies', [AuthController::class, 'customerCompany'])->name('api.customer-companies');
    });
    Route::prefix('man')->name('man.')->group(function () {
        Route::prefix('customer-user')->name('customer-user.')->group(function () {
            Route::get('/', [UserCustomerController::class, 'index'])->name('index');
            Route::post('/', [UserCustomerController::class, 'store'])->name('store');
            Route::post('/update-profile', [UserCustomerController::class, 'updateProfile'])->name('update-profile');
            Route::patch('/generate-affiliate-code', [UserCustomerController::class, 'generateAffiliateCode'])->name('generate-affiliate-code');
            Route::post('/generate-link', [UserCustomerController::class, 'generateRegistrationLink'])->name('registration-link');
            Route::put('/{id?}', [UserCustomerController::class, 'update'])->name('update');
            Route::get('/data-table', [UserCustomerController::class, 'dataTable'])->name('data-table');
            Route::get('/{id?}', [UserCustomerController::class, 'show'])->name('show');
            Route::delete('/{id?}', [UserCustomerController::class, 'destroy'])->name('delete');
        });
        Route::prefix('customer-company-good')->name('customer-company-good.')->group(function () {
            Route::post('/', [CustomerCompanyGoodController::class, 'store'])->name('store');
            Route::post('/store-temp-product/{date?}', [CustomerTemporaryProductController::class, 'storeTempProduct'])->name('store-temp-product');
            Route::get('/data-table', [CustomerCompanyGoodController::class, 'dataTable'])->name('data-table');
            Route::get('/temp-product', [CustomerCompanyGoodController::class, 'tempProduct'])->name('temp-product');
            Route::post('/{id?}', [CustomerCompanyGoodController::class, 'update'])->name('update');
            Route::get('/{id?}', [CustomerCompanyGoodController::class, 'show'])->name('show');
            Route::delete('/{id?}', [CustomerCompanyGoodController::class, 'destroy'])->name('delete');
        });
        Route::prefix('customer-temporary-product')->name('customer-temporary-product.')->group(function () {
            Route::post('/', [CustomerTemporaryProductController::class, 'store'])->name('store');
            Route::post('/store-temp-product/{date?}', [CustomerTemporaryProductController::class, 'storeTempProduct'])->name('store-temp-product');
            Route::put('/{id?}', [CustomerTemporaryProductController::class, 'update'])->name('update');
            Route::get('/data-table', [CustomerTemporaryProductController::class, 'dataTable'])->name('data-table');
            Route::get('/{date?}', [CustomerTemporaryProductController::class, 'show'])->name('show');
            Route::get('/temp/{id?}', [CustomerTemporaryProductController::class, 'showTemp'])->name('show-temp');
            Route::delete('/{id?}', [CustomerTemporaryProductController::class, 'destroy'])->name('delete');
        });
        Route::prefix('customer-product-type')->name('customer-product-type.')->group(function () {
            Route::get('/', [CustomerProductTypeController::class, 'index'])->name('index');
            Route::post('/', [CustomerProductTypeController::class, 'store'])->name('store');
            Route::put('/{id?}', [CustomerProductTypeController::class, 'update'])->name('update');
            Route::get('/data-table', [CustomerProductTypeController::class, 'dataTable'])->name('data-table');
            Route::get('/{id?}', [CustomerProductTypeController::class, 'show'])->name('show');
            Route::delete('/{id?}', [CustomerProductTypeController::class, 'destroy'])->name('delete');
        });
    });

    Route::prefix('dev')->name('dev.')->group(function () {
        Route::prefix('app-role')->name('app-role.')->group(function () {
            Route::get('/', [AppRoleController::class, 'index'])->name('index');
            Route::post('/', [AppRoleController::class, 'store'])->name('store');
            Route::put('/{id?}', [AppRoleController::class, 'update'])->name('update');
            Route::get('/data-table', [AppRoleController::class, 'dataTable'])->name('data-table');
            Route::get('/{id?}', [AppRoleController::class, 'show'])->name('show');
            Route::delete('/{id?}', [AppRoleController::class, 'destroy'])->name('delete');
        });
        Route::prefix('app-menu')->name('app-menu.')->group(function () {
            Route::get('/', [AppMenuController::class, 'index'])->name('index');
            Route::post('/', [AppMenuController::class, 'store'])->name('store');
            Route::put('/{id?}', [AppMenuController::class, 'update'])->name('update');
            Route::get('/data-table', [AppMenuController::class, 'dataTable'])->name('data-table');
            Route::get('/{id?}', [AppMenuController::class, 'show'])->name('show');
            Route::delete('/{id?}', [AppMenuController::class, 'destroy'])->name('delete');
        });
        Route::prefix('app-good-unit')->name('app-good-unit.')->group(function () {
            Route::get('/', [AppGoodUnitController::class, 'index'])->name('index');
            Route::post('/', [AppGoodUnitController::class, 'store'])->name('store');
            Route::put('/{id?}', [AppGoodUnitController::class, 'update'])->name('update');
            Route::get('/data-table', [AppGoodUnitController::class, 'dataTable'])->name('data-table');
            Route::get('/{id?}', [AppGoodUnitController::class, 'show'])->name('show');
            Route::delete('/{id?}', [AppGoodUnitController::class, 'destroy'])->name('delete');
        });
        Route::prefix('app-subscription')->name('app-subscription.')->group(function () {
            Route::get('/', [AppSubscriptionController::class, 'index'])->name('index');
            Route::post('/', [AppSubscriptionController::class, 'store'])->name('store');
            Route::put('/{id?}', [AppSubscriptionController::class, 'update'])->name('update');
            Route::get('/data-table', [AppSubscriptionController::class, 'dataTable'])->name('data-table');
            Route::get('/{id?}', [AppSubscriptionController::class, 'show'])->name('show');
            Route::delete('/{id?}', [AppSubscriptionController::class, 'destroy'])->name('delete');
        });
    });
});
