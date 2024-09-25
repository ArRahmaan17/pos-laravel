<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Dev\AppMenuController;
use App\Http\Controllers\Dev\AppRoleController;
use App\Http\Controllers\Man\CustomerCompanyController;
use App\Http\Controllers\Man\CustomerCompanyDiscountController;
use App\Http\Controllers\Man\CustomerCompanyGoodController;
use App\Http\Controllers\Man\CustomerCompanyWarehouseController;
use App\Http\Controllers\Man\CustomerProductTransactionController;
use App\Http\Controllers\Man\CustomerRoleAccessibilityController;
use App\Http\Controllers\Man\CustomerRoleController;
use App\Http\Controllers\Man\CustomerWareHouseRackGoodController;
use App\Http\Controllers\Man\UserCustomerController;
use App\Http\Middleware\Authorization;
use App\Http\Middleware\selectCustomerCompany;
use App\Http\Middleware\UnAuthorization;
use App\Http\Middleware\unSelectCustomerCompany;
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

Route::middleware([unAuthorization::class])->name('auth.')->prefix('auth')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
    Route::get('/registration', [AuthController::class, 'register'])->name('registration');
    Route::post('/registration', [AuthController::class, 'registration'])->name('registration.process');
});
Route::middleware([unSelectCustomerCompany::class])->group(function () {
    Route::get('/select-company', function () {
        return view('select-customer-company');
    })->name('select-customer-company');
    Route::post('/select-company', [AuthController::class, 'selectCompany'])
        ->name('select-customer-company');
    Route::get('/list-company', [AuthController::class, 'customerCompany'])->name('list-company');
});
Route::middleware([Authorization::class])->group(function () {
    Route::get('/', function () {
        return view('home');
    })->name('home');
    Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/change-company', [AuthController::class, 'changeCompany'])->name('auth.change-company');
    Route::name('dev')->as('dev.')->prefix('dev')->group(function () {
        Route::name('app-role')->as('app-role.')->prefix('app-role')->group(function () {
            Route::get('/', [AppRoleController::class, 'index'])->name('index');
            Route::post('/', [AppRoleController::class, 'store'])->name('store');
            Route::put('/{id?}', [AppRoleController::class, 'update'])->name('update');
            Route::get('/data-table', [AppRoleController::class, 'dataTable'])->name('data-table');
            Route::get('/{id?}', [AppRoleController::class, 'show'])->name('show');
            Route::delete('/{id?}', [AppRoleController::class, 'destroy'])->name('delete');
        });
        Route::name('app-menu')->as('app-menu.')->prefix('app-menu')->group(function () {
            Route::get('/', [AppMenuController::class, 'index'])->name('index');
            Route::post('/', [AppMenuController::class, 'store'])->name('store');
            Route::put('/{id?}', [AppMenuController::class, 'update'])->name('update');
            Route::get('/data-table', [AppMenuController::class, 'dataTable'])->name('data-table');
            Route::get('/{id?}', [AppMenuController::class, 'show'])->name('show');
            Route::delete('/{id?}', [AppMenuController::class, 'destroy'])->name('delete');
        });
    });
    Route::name('man')->as('man.')->prefix('man')->group(function () {
        Route::name('customer-company')->as('customer-company.')->prefix('customer-company')->group(function () {
            Route::get('/', [CustomerCompanyController::class, 'index'])->name('index');
            Route::post('/', [CustomerCompanyController::class, 'store'])->name('store');
            Route::get('/company', [CustomerCompanyController::class, 'company'])->name('company');
            Route::post('/{id?}', [CustomerCompanyController::class, 'update'])->name('update');
            Route::get('/data-table', [CustomerCompanyController::class, 'dataTable'])->name('data-table');
            Route::get('/{id?}', [CustomerCompanyController::class, 'show'])->name('show');
            Route::delete('/{id?}', [CustomerCompanyController::class, 'destroy'])->name('delete');
        });
        Route::name('customer-role-accessibility')->as('customer-role-accessibility.')->prefix('customer-role-accessibility')->group(function () {
            Route::get('/', [CustomerRoleAccessibilityController::class, 'index'])->name('index');
            Route::post('/', [CustomerRoleAccessibilityController::class, 'store'])->name('store');
            Route::put('/{id?}', [CustomerRoleAccessibilityController::class, 'update'])->name('update');
            Route::get('/data-table', [CustomerRoleAccessibilityController::class, 'dataTable'])->name('data-table');
            Route::get('/{id?}', [CustomerRoleAccessibilityController::class, 'show'])->name('show');
            Route::delete('/{id?}', [CustomerRoleAccessibilityController::class, 'destroy'])->name('delete');
        });
        Route::name('customer-company-discount')->as('customer-company-discount.')->prefix('customer-company-discount')->group(function () {
            Route::get('/', [CustomerCompanyDiscountController::class, 'index'])->name('index');
            Route::post('/', [CustomerCompanyDiscountController::class, 'store'])->name('store');
            Route::put('/{id?}', [CustomerCompanyDiscountController::class, 'update'])->name('update');
            Route::get('/data-table', [CustomerCompanyDiscountController::class, 'dataTable'])->name('data-table');
            Route::get('/{id?}', [CustomerCompanyDiscountController::class, 'show'])->name('show');
            Route::delete('/{id?}', [CustomerCompanyDiscountController::class, 'destroy'])->name('delete');
        });
        Route::name('customer-role')->as('customer-role.')->prefix('customer-role')->group(function () {
            Route::get('/', [CustomerRoleController::class, 'index'])->name('index');
            Route::post('/', [CustomerRoleController::class, 'store'])->name('store');
            Route::put('/{id?}', [CustomerRoleController::class, 'update'])->name('update');
            Route::get('/data-table', [CustomerRoleController::class, 'dataTable'])->name('data-table');
            Route::get('/{id?}', [CustomerRoleController::class, 'show'])->name('show');
            Route::get('/role/{userId?}', [CustomerRoleController::class, 'role'])->name('role');
            Route::delete('/{id?}', [CustomerRoleController::class, 'destroy'])->name('delete');
        });
        Route::name('customer-user')->as('customer-user.')->prefix('customer-user')->group(function () {
            Route::get('/', [UserCustomerController::class, 'index'])->name('index');
            Route::post('/', [UserCustomerController::class, 'store'])->name('store');
            Route::post('/generate-link', [UserCustomerController::class, 'generateRegistrationLink'])->name('registration-link');
            Route::put('/{id?}', [UserCustomerController::class, 'update'])->name('update');
            Route::get('/data-table', [UserCustomerController::class, 'dataTable'])->name('data-table');
            Route::get('/{id?}', [UserCustomerController::class, 'show'])->name('show');
            Route::delete('/{id?}', [UserCustomerController::class, 'destroy'])->name('delete');
        });
        Route::name('customer-company-warehouse')->as('customer-company-warehouse.')->prefix('customer-company-warehouse')->group(function () {
            Route::get('/', [CustomerCompanyWarehouseController::class, 'index'])->name('index');
            Route::post('/', [CustomerCompanyWarehouseController::class, 'store'])->name('store');
            Route::put('/{id?}', [CustomerCompanyWarehouseController::class, 'update'])->name('update');
            Route::get('/data-table', [CustomerCompanyWarehouseController::class, 'dataTable'])->name('data-table');
            Route::get('/{id?}', [CustomerCompanyWarehouseController::class, 'show'])->name('show');
            Route::delete('/{id?}', [CustomerCompanyWarehouseController::class, 'destroy'])->name('delete');
        });
        Route::name('customer-product-transaction')->as('customer-product-transaction.')->prefix('customer-product-transaction')->group(function () {
            Route::get('/', [CustomerProductTransactionController::class, 'index'])->name('index');
            Route::post('/', [CustomerProductTransactionController::class, 'store'])->name('store');
            Route::get('/transaction-receipt/{orderCode?}/print', [CustomerProductTransactionController::class, 'viewPdf'])->name('print-transaction-receipt');
            Route::get('/product-data-table', [CustomerProductTransactionController::class, 'productDataTable'])->name('product-data-table');
            Route::get('/discount-data-table', [CustomerProductTransactionController::class, 'discountDataTable'])->name('discount-data-table');
            Route::get('/validate-discount-code/{id?}', [CustomerProductTransactionController::class, 'validateDiscountCode'])->name('validate-discount-code');
        });
        Route::name('customer-warehouse-rack-good')->as('customer-warehouse-rack-good.')->prefix('customer-warehouse-rack-good')->group(function () {
            Route::get('/', [CustomerWareHouseRackGoodController::class, 'index'])->name('index');
            Route::post('/', [CustomerWareHouseRackGoodController::class, 'store'])->name('store');
            Route::put('/{rackId?}/{id?}', [CustomerWareHouseRackGoodController::class, 'update'])->name('update');
            Route::get('/data-table', [CustomerWareHouseRackGoodController::class, 'dataTable'])->name('data-table');
            Route::get('/{id?}', [CustomerWareHouseRackGoodController::class, 'racks'])->name('show');
            Route::delete('/{id?}', [CustomerWareHouseRackGoodController::class, 'destroy'])->name('delete');
        });
        Route::name('customer-company-good')->as('customer-company-good.')->prefix('customer-company-good')->group(function () {
            Route::get('/', [CustomerCompanyGoodController::class, 'index'])->name('index');
            Route::post('/', [CustomerCompanyGoodController::class, 'store'])->name('store');
            Route::post('/{id?}', [CustomerCompanyGoodController::class, 'update'])->name('update');
            Route::get('/data-table', [CustomerCompanyGoodController::class, 'dataTable'])->name('data-table');
            Route::get('/{id?}', [CustomerCompanyGoodController::class, 'show'])->name('show');
            Route::delete('/{id?}', [CustomerCompanyGoodController::class, 'destroy'])->name('delete');
        });
    });
});
