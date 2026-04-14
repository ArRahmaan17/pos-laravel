<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Dev\PermissionController;
use App\Http\Controllers\Api\Dev\RoleController;
use App\Http\Controllers\Api\Dev\SubscriptionController;
use App\Http\Controllers\Api\Dev\WeightController;
use App\Http\Controllers\Api\Man\CategoryController;
use App\Http\Controllers\Api\Man\ProductController;
use App\Http\Controllers\Api\Man\TemporaryProductController;
use App\Http\Controllers\Api\Man\UserController;
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
// Route::middleware('throttle:60,1')->group(function () {
//     Route::get('/', function () {
//         return response()->json([
//             'message' => 'Welcome to the API',
//         ], 200);
//     });
//     Route::prefix('auth')->name('api.auth.')->group(function () {
//         Route::post('/login', [AuthController::class, 'login'])->name('login');
//         Route::post('/register', [AuthController::class, 'register'])->name('register');
//         Route::get('/check-available-user', [AuthController::class, 'checkAvailableUser'])->name('check-available-user');
//         Route::get('/company-types', [AuthController::class, 'companyTypes'])->name('company-types');
//         Route::get('/check-company-availability', [AuthController::class, 'checkAvailableCompany'])->name('check-company-availability');
//     });
// });

// Route::name('api.')->middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
//     Route::prefix('auth')->name('auth.')->group(function () {
//         Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
//         Route::post('/logout-all', [AuthController::class, 'logoutAll'])->name('logout-all');
//         Route::get('/me', [AuthController::class, 'me'])->name('me');
//         Route::post('/select-company', [AuthController::class, 'selectCompany'])->name('select-company');
//         Route::post('/login-as/{id}', [AuthController::class, 'loginAs'])->name('login-as');
//         Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change-password');
//         Route::post('/activate-access-pin', [AuthController::class, 'activateAccessPin'])->name('activate-access-pin');
//         Route::post('/unlock-screen', [AuthController::class, 'unlockScreen'])->name('unlock-screen');
//         Route::get('/customer-companies', [AuthController::class, 'customerCompany'])->name('customer-companies');
//     });
//     Route::prefix('man')->name('man.')->group(function () {
//         Route::prefix('customer-user')->name('customer-user.')->group(function () {
//             Route::get('/', [UserController::class, 'index'])->name('index');
//             Route::post('/', [UserController::class, 'store'])->name('store');
//             Route::post('/update-profile', [UserController::class, 'updateProfile'])->name('update-profile');
//             Route::patch('/generate-affiliate-code', [UserController::class, 'generateAffiliateCode'])->name('generate-affiliate-code');
//             Route::post('/generate-link', [UserController::class, 'generateRegistrationLink'])->name('registration-link');
//             Route::put('/{id?}', [UserController::class, 'update'])->name('update');
//             Route::get('/data-table', [UserController::class, 'dataTable'])->name('data-table');
//             Route::get('/{id?}', [UserController::class, 'show'])->name('show');
//             Route::delete('/{id?}', [UserController::class, 'destroy'])->name('delete');
//         });
//         Route::prefix('customer-company-good')->name('customer-company-good.')->group(function () {
//             Route::post('/', [ProductController::class, 'store'])->name('store');
//             Route::post('/store-temporary-product/{date?}', [TemporaryProductController::class, 'storeTempProduct'])->name('store-temporary-product');
//             Route::get('/data-table', [ProductController::class, 'dataTable'])->name('data-table');
//             Route::get('/temp-product', [ProductController::class, 'tempProduct'])->name('temp-product');
//             Route::post('/{id?}', [ProductController::class, 'update'])->name('update');
//             Route::get('/{id?}', [ProductController::class, 'show'])->name('show');
//             Route::delete('/{id?}', [ProductController::class, 'destroy'])->name('delete');
//         });
//         Route::prefix('customer-temporary-product')->name('customer-temporary-product.')->group(function () {
//             Route::post('/', [TemporaryProductController::class, 'store'])->name('store');
//             Route::post('/store-temporary-product/{date?}', [TemporaryProductController::class, 'storeTempProduct'])->name('store-temporary-product');
//             Route::put('/{id?}', [TemporaryProductController::class, 'update'])->name('update');
//             Route::get('/data-table', [TemporaryProductController::class, 'dataTable'])->name('data-table');
//             Route::get('/{date?}', [TemporaryProductController::class, 'show'])->name('show');
//             Route::get('/temp/{id?}', [TemporaryProductController::class, 'showTemp'])->name('show-temp');
//             Route::delete('/{id?}', [TemporaryProductController::class, 'destroy'])->name('delete');
//         });
//         Route::prefix('customer-product-type')->name('customer-product-type.')->group(function () {
//             Route::get('/', [CategoryController::class, 'index'])->name('index');
//             Route::post('/', [CategoryController::class, 'store'])->name('store');
//             Route::put('/{id?}', [CategoryController::class, 'update'])->name('update');
//             Route::get('/data-table', [CategoryController::class, 'dataTable'])->name('data-table');
//             Route::get('/{id?}', [CategoryController::class, 'show'])->name('show');
//             Route::delete('/{id?}', [CategoryController::class, 'destroy'])->name('delete');
//         });
//     });

//     Route::prefix('dev')->name('settings.')->group(function () {
//         Route::prefix('role')->name('role.')->group(function () {
//             Route::get('/', [RoleController::class, 'index'])->name('index');
//             Route::post('/', [RoleController::class, 'store'])->name('store');
//             Route::put('/{id?}', [RoleController::class, 'update'])->name('update');
//             Route::get('/data-table', [RoleController::class, 'dataTable'])->name('data-table');
//             Route::get('/{id?}', [RoleController::class, 'show'])->name('show');
//             Route::delete('/{id?}', [RoleController::class, 'destroy'])->name('delete');
//         });
//         Route::prefix('permission')->name('permission.')->group(function () {
//             Route::get('/', [PermissionController::class, 'index'])->name('index');
//             Route::post('/', [PermissionController::class, 'store'])->name('store');
//             Route::put('/{id?}', [PermissionController::class, 'update'])->name('update');
//             Route::get('/data-table', [PermissionController::class, 'dataTable'])->name('data-table');
//             Route::get('/{id?}', [PermissionController::class, 'show'])->name('show');
//             Route::delete('/{id?}', [PermissionController::class, 'destroy'])->name('delete');
//         });
//         Route::prefix('product-unit')->name('product-unit.')->group(function () {
//             Route::get('/', [WeightController::class, 'index'])->name('index');
//             Route::post('/', [WeightController::class, 'store'])->name('store');
//             Route::put('/{id?}', [WeightController::class, 'update'])->name('update');
//             Route::get('/data-table', [WeightController::class, 'dataTable'])->name('data-table');
//             Route::get('/{id?}', [WeightController::class, 'show'])->name('show');
//             Route::delete('/{id?}', [WeightController::class, 'destroy'])->name('delete');
//         });
//         Route::prefix('subscription')->name('subscription.')->group(function () {
//             Route::get('/', [SubscriptionController::class, 'index'])->name('index');
//             Route::post('/', [SubscriptionController::class, 'store'])->name('store');
//             Route::put('/{id?}', [SubscriptionController::class, 'update'])->name('update');
//             Route::get('/data-table', [SubscriptionController::class, 'dataTable'])->name('data-table');
//             Route::get('/{id?}', [SubscriptionController::class, 'show'])->name('show');
//             Route::delete('/{id?}', [SubscriptionController::class, 'destroy'])->name('delete');
//         });
//     });
// });
