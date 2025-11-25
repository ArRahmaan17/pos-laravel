<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Dev\ProductUnitController;
use App\Http\Controllers\Dev\PermissionController;
use App\Http\Controllers\Dev\RoleController;
use App\Http\Controllers\Dev\SubscriptionController;
use App\Http\Controllers\Man\CustomerCompanyController;
use App\Http\Controllers\Man\CustomerCompanyDiscountController;
use App\Http\Controllers\Man\CustomerCompanyGoodController;
use App\Http\Controllers\Man\CustomerCompanyMasterTaskController;
use App\Http\Controllers\Man\CustomerCompanyStocktakingController;
use App\Http\Controllers\Man\CustomerCompanyWarehouseController;
use App\Http\Controllers\Man\CustomerProductTransactionController;
use App\Http\Controllers\Man\CustomerProductTypeController;
use App\Http\Controllers\Man\CustomerRoleAccessibilityController;
use App\Http\Controllers\Man\CustomerRoleController;
use App\Http\Controllers\Man\CustomerTaskController;
use App\Http\Controllers\Man\CustomerTemporaryProductController;
use App\Http\Controllers\Man\CustomerWareHouseRackGoodController;
use App\Http\Controllers\Man\UserCustomerController;
use App\Http\Controllers\ReportController;
use App\Http\Middleware\Authorization;
use App\Http\Middleware\checkPageAuthorization;
use App\Http\Middleware\setupAccessPin;
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
        return view('select-company');
    })->name('select-company');
    Route::post('/select-company', [AuthController::class, 'selectCompany'])
        ->name('select-company');
    Route::get('/list-company', [AuthController::class, 'customerCompany'])->name('list-company');
});

Route::name('privacy.')->prefix('privacy')->group(function () {
    Route::get('/request-access-pin', [AuthController::class, 'requestActivateAccessPin'])->name('request-access-pin');
    Route::post('/validate-access-pin', [AuthController::class, 'validateAccessPin'])->name('validate-access-pin');
    Route::post('/access-pin', [AuthController::class, 'activateAccessPin'])->name('access-pin');
});
Route::middleware([Authorization::class, setupAccessPin::class])->group(function () {
    Route::get('/', function () {
        return view('home');
    })->name('dashboard.index')->middleware([checkPageAuthorization::class]);
    Route::name('auth')->as('auth.')->prefix('auth')->group(function () {
        Route::post('/login-as/{id?}', [AuthController::class, 'loginAs'])->name('login-as')->middleware([checkPageAuthorization::class]);
        Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('/lockscreen', [AuthController::class, 'lockscreen'])->name('lockscreen');
        Route::post('/unlock-screen', [AuthController::class, 'unlockScreen'])->name('unlock-screen');
        Route::get('/request-change-password', [AuthController::class, 'requestChangePassword'])->name('request-change-password');
        Route::get('/change-company', [AuthController::class, 'changeCompany'])->name('change-company');
    });

    Route::name('management')->as('management.')->prefix('management')->middleware([checkPageAuthorization::class])->group(function () {
        Route::name('customer-company')->as('customer-company.')->prefix('customer-company')->group(function () {
            Route::get('/', [CustomerCompanyController::class, 'index'])->name('index');
            Route::post('/', [CustomerCompanyController::class, 'store'])->name('store');
            Route::get('/company', [CustomerCompanyController::class, 'company'])->name('company');
            Route::get('/profile', [CustomerCompanyController::class, 'profile'])->name('profile');
            Route::post('/login-company', [CustomerCompanyController::class, 'loginCompany'])->name('login-company');
            Route::post('/{id?}', [CustomerCompanyController::class, 'update'])->name('update');
            Route::get('/data-table', [CustomerCompanyController::class, 'dataTable'])->name('data-table');
            Route::get('/{id?}', [CustomerCompanyController::class, 'show'])->name('show');
            Route::delete('/{id?}', [CustomerCompanyController::class, 'destroy'])->name('delete');
        });
        Route::name('report')->as('report.')->prefix('report')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::post('/', [ReportController::class, 'generateReport'])->name('generate-report');
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
            Route::get('/role/{user_id?}', [CustomerRoleController::class, 'role'])->name('role');
            Route::delete('/{id?}', [CustomerRoleController::class, 'destroy'])->name('delete');
        });
        Route::name('customer-user')->as('customer-user.')->prefix('customer-user')->group(function () {
            Route::get('/', [UserCustomerController::class, 'index'])->name('index');
            Route::post('/', [UserCustomerController::class, 'store'])->name('store');
            Route::get('/profile', [UserCustomerController::class, 'profile'])->name('profile');
            Route::post('/update-profile', [UserCustomerController::class, 'updateProfile'])->name('update-profile');
            Route::patch('/generate-affiliate-code', [UserCustomerController::class, 'generateAffiliateCode'])->name('generate-affiliate-code');
            Route::post('/generate-link', [UserCustomerController::class, 'generateRegistrationLink'])->name('registration-link');
            Route::put('/{id?}', [UserCustomerController::class, 'update'])->name('update');
            Route::get('/data-table', [UserCustomerController::class, 'dataTable'])->name('data-table');
            Route::get('/{id?}', [UserCustomerController::class, 'show'])->name('show');
            Route::delete('/{id?}', [UserCustomerController::class, 'destroy'])->name('delete');
        });
        Route::name('customer-company-good')->as('customer-company-good.')->prefix('customer-company-good')->group(function () {
            Route::get('/', [CustomerCompanyGoodController::class, 'index'])->name('index');
            Route::post('/', [CustomerCompanyGoodController::class, 'store'])->name('store');
            Route::post('/store-temp-product/{date?}', [CustomerTemporaryProductController::class, 'storeTempProduct'])->name('store-temp-product');
            Route::post('/{id?}', [CustomerCompanyGoodController::class, 'update'])->name('update');
            Route::get('/data-table', [CustomerCompanyGoodController::class, 'dataTable'])->name('data-table');
            Route::get('/temp-product', [CustomerCompanyGoodController::class, 'tempProduct'])->name('temp-product');
            Route::get('/{id?}', [CustomerCompanyGoodController::class, 'show'])->name('show');
            Route::delete('/{id?}', [CustomerCompanyGoodController::class, 'destroy'])->name('delete');
        });
        Route::name('customer-company-warehouse')->as('customer-company-warehouse.')->prefix('customer-company-warehouse')->group(function () {
            Route::get('/', [CustomerCompanyWarehouseController::class, 'index'])->name('index');
            Route::post('/', [CustomerCompanyWarehouseController::class, 'store'])->name('store');
            Route::put('/{id?}', [CustomerCompanyWarehouseController::class, 'update'])->name('update');
            Route::get('/data-table', [CustomerCompanyWarehouseController::class, 'dataTable'])->name('data-table');
            Route::get('/{id?}', [CustomerCompanyWarehouseController::class, 'show'])->name('show');
            Route::delete('/{id?}', [CustomerCompanyWarehouseController::class, 'destroy'])->name('delete');
        });
        Route::name('customer-warehouse-rack-good')->as('customer-warehouse-rack-good.')->prefix('customer-warehouse-rack-good')->group(function () {
            Route::get('/', [CustomerWareHouseRackGoodController::class, 'index'])->name('index');
            Route::post('/', [CustomerWareHouseRackGoodController::class, 'store'])->name('store');
            Route::put('/{rackId?}/{id?}', [CustomerWareHouseRackGoodController::class, 'update'])->name('update');
            Route::get('/{id?}', [CustomerWareHouseRackGoodController::class, 'racks'])->name('show');
        });
        Route::name('customer-product-transaction')->as('customer-product-transaction.')->prefix('customer-product-transaction')->group(function () {
            Route::get('/', [CustomerProductTransactionController::class, 'index'])->name('index');
            Route::post('/', [CustomerProductTransactionController::class, 'store'])->name('store');
            Route::post('/validate-transaction-items', [CustomerProductTransactionController::class, 'validateTransactionItems'])->name('validate-transaction-items');
            Route::get('/transaction-receipt/{orderCode?}/print', [CustomerProductTransactionController::class, 'viewPdf'])->name('print-transaction-receipt');
            Route::get('/product-data-table', [CustomerProductTransactionController::class, 'productDataTable'])->name('product-data-table');
            Route::get('/data-table', [CustomerProductTransactionController::class, 'dataTable'])->name('data-table');
            Route::get('/discount-data-table', [CustomerProductTransactionController::class, 'discountDataTable'])->name('discount-data-table');
            Route::get('/validate-discount-code/{id?}', [CustomerProductTransactionController::class, 'validateDiscountCode'])->name('validate-discount-code');
        });
        Route::name('customer-temp-product')->as('customer-temp-product.')->prefix('customer-temp-product')->group(function () {
            Route::get('/', [CustomerTemporaryProductController::class, 'index'])->name('index');
            Route::post('/', [CustomerTemporaryProductController::class, 'store'])->name('store');
            Route::post('/store-temp-product/{date?}', [CustomerTemporaryProductController::class, 'storeTempProduct'])->name('store-temp-product');
            Route::post('/{id?}', [CustomerTemporaryProductController::class, 'update'])->name('update');
            Route::get('/data-table', [CustomerTemporaryProductController::class, 'dataTable'])->name('data-table');
            Route::get('/temp-product', [CustomerTemporaryProductController::class, 'tempProduct'])->name('temp-product');
            Route::get('/{id?}', [CustomerTemporaryProductController::class, 'show'])->name('show');
            Route::delete('/{id?}', [CustomerTemporaryProductController::class, 'destroy'])->name('delete');
        });
        Route::name('customer-task-management')->as('customer-task-management.')->prefix('customer-task-management')->group(function () {
            Route::get('/', [CustomerTaskController::class, 'index'])->name('index');
            Route::get('/new-task', [CustomerTaskController::class, 'newTask'])->name('new-task');
            Route::get('/unfinish-task', [CustomerTaskController::class, 'unfinishTask'])->name('unfinish-task');
            Route::post('/', [CustomerTaskController::class, 'store'])->name('store');
            Route::get('/get-evidence/{id?}', [CustomerTaskController::class, 'getEvidence'])->name('get-evidence');
            Route::post('/finish-task/{id?}/{type?}', [CustomerTaskController::class, 'finishTask'])->name('finish-task');
            Route::put('/{id?}', [CustomerTaskController::class, 'update'])->name('update');
            Route::put('/start-task/{id?}/{type?}', [CustomerTaskController::class, 'startTask'])->name('start-task');
            Route::get('/data-table', [CustomerTaskController::class, 'dataTable'])->name('data-table');
            Route::get('/{id?}', [CustomerTaskController::class, 'show'])->name('show');
            Route::delete('/detail/{id?}', [CustomerTaskController::class, 'destroyDetail'])->name('delete-detail');
            Route::delete('/{id?}', [CustomerTaskController::class, 'destroy'])->name('delete');
        });
        Route::name('customer-master-tasks')->as('customer-master-tasks.')->prefix('customer-master-tasks')->group(function () {
            Route::get('/', [CustomerCompanyMasterTaskController::class, 'index'])->name('index');
            Route::post('/', [CustomerCompanyMasterTaskController::class, 'store'])->name('store');
            Route::put('/{id?}', [CustomerCompanyMasterTaskController::class, 'update'])->name('update');
            Route::get('/data-table', [CustomerCompanyMasterTaskController::class, 'dataTable'])->name('data-table');
            Route::get('/{id?}', [CustomerCompanyMasterTaskController::class, 'show'])->name('show');
            Route::delete('/{id?}', [CustomerCompanyMasterTaskController::class, 'destroy'])->name('delete');
        });
        Route::name('customer-product-stocktaking')->as('customer-product-stocktaking.')->prefix('customer-product-stocktaking')->group(function () {
            Route::get('/', [CustomerCompanyStocktakingController::class, 'index'])->name('index');
            Route::post('/', [CustomerCompanyStocktakingController::class, 'store'])->name('store');
            Route::put('/{id?}', [CustomerCompanyStocktakingController::class, 'update'])->name('update');
            Route::get('/data-table', [CustomerCompanyStocktakingController::class, 'dataTable'])->name('data-table');
            Route::get('/{id?}', [CustomerCompanyStocktakingController::class, 'show'])->name('show');
            Route::post('/{id?}', [CustomerCompanyStocktakingController::class, 'approveStocktaking'])->name('approve');
            Route::delete('/{id?}', [CustomerCompanyStocktakingController::class, 'destroy'])->name('delete');
        });
        Route::name('customer-product-type')->as('customer-product-type.')->prefix('customer-product-type')->group(function () {
            Route::get('/', [CustomerProductTypeController::class, 'index'])->name('index');
            Route::post('/', [CustomerProductTypeController::class, 'store'])->name('store');
            Route::put('/{id?}', [CustomerProductTypeController::class, 'update'])->name('update');
            Route::get('/data-table', [CustomerProductTypeController::class, 'dataTable'])->name('data-table');
            Route::get('/{id?}', [CustomerProductTypeController::class, 'show'])->name('show');
            Route::delete('/{id?}', [CustomerProductTypeController::class, 'destroy'])->name('delete');
        });
    });
    Route::prefix('dev')->as('dev.')->name('dev.')->middleware([checkPageAuthorization::class])->group(function () {
        Route::prefix('role')->name('role.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::post('/', [RoleController::class, 'store'])->name('store');
            Route::put('/{id?}', [RoleController::class, 'update'])->name('update');
            Route::get('/data-table', [RoleController::class, 'dataTable'])->name('data-table');
            Route::get('/{id?}', [RoleController::class, 'show'])->name('show');
            Route::delete('/{id?}', [RoleController::class, 'destroy'])->name('delete');
        });
        Route::prefix('permission')->name('permission.')->group(function () {
            Route::get('/', [PermissionController::class, 'index'])->name('index');
            Route::post('/', [PermissionController::class, 'store'])->name('store');
            Route::put('/{id?}', [PermissionController::class, 'update'])->name('update');
            Route::get('/data-table', [PermissionController::class, 'dataTable'])->name('data-table');
            Route::get('/{id?}', [PermissionController::class, 'show'])->name('show');
            Route::delete('/{id?}', [PermissionController::class, 'destroy'])->name('delete');
        });
        Route::prefix('product-unit')->name('product-unit.')->group(function () {
            Route::get('/', [ProductUnitController::class, 'index'])->name('index');
            Route::post('/', [ProductUnitController::class, 'store'])->name('store');
            Route::put('/{id?}', [ProductUnitController::class, 'update'])->name('update');
            Route::get('/data-table', [ProductUnitController::class, 'dataTable'])->name('data-table');
            Route::get('/{id?}', [ProductUnitController::class, 'show'])->name('show');
            Route::delete('/{id?}', [ProductUnitController::class, 'destroy'])->name('delete');
        });
        Route::prefix('subscription')->name('subscription.')->group(function () {
            Route::get('/', [SubscriptionController::class, 'index'])->name('index');
            Route::post('/', [SubscriptionController::class, 'store'])->name('store');
            Route::put('/{id?}', [SubscriptionController::class, 'update'])->name('update');
            Route::get('/data-table', [SubscriptionController::class, 'dataTable'])->name('data-table');
            Route::get('/{id?}', [SubscriptionController::class, 'show'])->name('show');
            Route::delete('/{id?}', [SubscriptionController::class, 'destroy'])->name('delete');
        });
    });
});
