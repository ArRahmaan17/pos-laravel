<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Company\WarehouseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Man\CustomerCompanyDiscountController;
use App\Http\Controllers\Man\CustomerCompanyGoodController;
use App\Http\Controllers\Man\CustomerCompanyMasterTaskController;
use App\Http\Controllers\Man\CustomerCompanyStocktakingController;
use App\Http\Controllers\Man\CustomerProductTransactionController;
use App\Http\Controllers\Man\CustomerProductTypeController;
use App\Http\Controllers\Man\CustomerTaskController;
use App\Http\Controllers\Man\CustomerTemporaryProductController;
use App\Http\Controllers\Man\CustomerWareHouseRackGoodController;
use App\Http\Controllers\Man\UserCustomerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Settings\CompanyController;
use App\Http\Controllers\Settings\PermissionController;
use App\Http\Controllers\Settings\ProductUnitController;
use App\Http\Controllers\Settings\RoleController;
use App\Http\Controllers\Settings\SubscriptionController;
use App\Http\Middleware\Authorization;
use App\Http\Middleware\AuthorizationOnly;
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

Route::meta(['group' => 'web'], function () {
    Route::middleware([UnAuthorization::class])->name('auth.')->prefix('auth')->group(function () {
        Route::get('/login', [AuthController::class, 'index'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.process');
        Route::get('/registration', [AuthController::class, 'register'])->name('registration');
        Route::post('/registration', [AuthController::class, 'registration'])->middleware('throttle:3,1')->name('registration.process');
    });
    Route::middleware([unSelectCustomerCompany::class])->group(function () {
        Route::get('/', function () {
            return view('select-company');
        })->name('select-company');
        Route::post('/', [AuthController::class, 'selectCompany'])
            ->name('choosing-company');
        Route::get('/list-company', [AuthController::class, 'customerCompany'])->name('list-company');
    });
    Route::name('privacy.')->prefix('privacy')->as('privacy.')->middleware([Authorization::class])->group(function () {
        Route::get('/request-access-pin', [AuthController::class, 'requestActivateAccessPin'])->name('request-access-pin');
        Route::post('/validate-access-pin', [AuthController::class, 'validateAccessPin'])->name('validate-access-pin');
        Route::post('/access-pin', [AuthController::class, 'activateAccessPin'])->name('access-pin');
    });
    Route::middleware([Authorization::class, setupAccessPin::class])->group(function () {
        Route::meta([
            'icon' => 'bx bx-home-alt-3',
            'prefix' => 'dashboard',
            'as' => 'dashboard.',
            'parent' => null,
            'name' => 'dashboard',
            'module' => 'dashboard',
            'middleware' => [checkPageAuthorization::class],
        ], function () {
            Route::get('/', [HomeController::class, 'index'])->name('index')->middleware([checkPageAuthorization::class]);
        });
        Route::meta([
            'icon' => 'bx bx-user-check',
            'prefix' => 'auth',
            'as' => 'auth.',
            'parent' => null,
            'name' => 'auth',
            'module' => 'auth',
        ], function () {
            Route::post('/login-as/{id?}', [AuthController::class, 'loginAs'])->name('login-as')->middleware([checkPageAuthorization::class]);
            Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
            Route::post('/lockscreen', [AuthController::class, 'lockscreen'])->name('lockscreen');
            Route::post('/unlock-screen', [AuthController::class, 'unlockScreen'])->name('unlock-screen');
            Route::get('/request-change-password', [AuthController::class, 'requestChangePassword'])->name('request-change-password');
            Route::get('/change-company', [AuthController::class, 'changeCompany'])->name('change-company');
        });
        Route::meta([
            'parent-icon' => 'bx bx-apartment',
            'prefix' => 'company',
            'as' => 'company.',
            'parent' => null,
            'name' => 'company',
            'module' => 'company',
            'middleware' => [checkPageAuthorization::class],
        ], function () {
            Route::meta([
                'icon' => 'bx bxs-group',
                'prefix' => 'user',
                'as' => 'user.',
                'parent' => 'company',
                'name' => 'user',
                'module' => 'user',
                'middleware' => [checkPageAuthorization::class],
            ], function () {
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
            Route::meta([
                'icon' => 'bx bxs-list-square',
                'prefix' => 'master-task',
                'as' => 'master-task.',
                'parent' => 'company',
                'name' => 'master-task',
                'module' => 'master-task',
                'middleware' => [checkPageAuthorization::class],
            ], function () {
                Route::get('/', [CustomerCompanyMasterTaskController::class, 'index'])->name('index');
                Route::post('/', [CustomerCompanyMasterTaskController::class, 'store'])->name('store');
                Route::put('/{id?}', [CustomerCompanyMasterTaskController::class, 'update'])->name('update');
                Route::get('/data-table', [CustomerCompanyMasterTaskController::class, 'dataTable'])->name('data-table');
                Route::get('/{id?}', [CustomerCompanyMasterTaskController::class, 'show'])->name('show');
                Route::delete('/{id?}', [CustomerCompanyMasterTaskController::class, 'destroy'])->name('delete');
            });
            Route::meta([
                'icon' => 'bx bx-checklist',
                'prefix' => 'task-management',
                'as' => 'task-management.',
                'parent' => 'company',
                'name' => 'task-management',
                'module' => 'task-management',
                'middleware' => [checkPageAuthorization::class],
            ], function () {
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
            Route::meta([
                'icon' => 'bx bxs-warehouse',
                'prefix' => 'warehouse',
                'as' => 'warehouse.',
                'parent' => 'company',
                'name' => 'warehouse',
                'module' => 'warehouse',
                'middleware' => [checkPageAuthorization::class],
            ], function () {
                Route::get('/', [WarehouseController::class, 'index'])->name('index');
                Route::post('/', [WarehouseController::class, 'store'])->name('store');
                Route::put('/{id?}', [WarehouseController::class, 'update'])->name('update');
                Route::get('/data-table', [WarehouseController::class, 'dataTable'])->name('data-table');
                Route::get('/{id?}', [WarehouseController::class, 'show'])->name('show');
                Route::delete('/{id?}', [WarehouseController::class, 'destroy'])->name('delete');
            });
        });
        Route::meta([
            'parent-icon' => 'bx bx-package',
            'prefix' => 'product',
            'as' => 'product.',
            'parent' => null,
            'name' => 'product',
            'module' => 'product',
            'middleware' => [checkPageAuthorization::class],
        ], function () {
            Route::meta([
                'icon' => 'bx bxs-cupboard-alt',
                'prefix' => 'warehouse-shelf',
                'as' => 'warehouse-shelf.',
                'parent' => 'product',
                'name' => 'warehouse-shelf',
                'module' => 'warehouse-shelf',
                'middleware' => [checkPageAuthorization::class],
            ], function () {
                Route::get('/', [CustomerWareHouseRackGoodController::class, 'index'])->name('index');
                Route::post('/', [CustomerWareHouseRackGoodController::class, 'store'])->name('store');
                Route::put('/{rackId?}/{id?}', [CustomerWareHouseRackGoodController::class, 'update'])->name('update');
                Route::get('/{id?}', [CustomerWareHouseRackGoodController::class, 'racks'])->name('show');
            });
            Route::meta([
                'icon' => 'bx bxs-cog',
                'prefix' => 'weight',
                'as' => 'weight.',
                'parent' => 'product',
                'name' => 'weight',
                'module' => 'weight',
                'middleware' => [checkPageAuthorization::class],
            ], function () {
                Route::get('/', [ProductUnitController::class, 'index'])->name('index')->defaults('module', 'settings');
                Route::post('/', [ProductUnitController::class, 'store'])->name('store');
                Route::put('/{id?}', [ProductUnitController::class, 'update'])->name('update');
                Route::get('/data-table', [ProductUnitController::class, 'dataTable'])->name('data-table');
                Route::get('/{id?}', [ProductUnitController::class, 'show'])->name('show');
                Route::delete('/{id?}', [ProductUnitController::class, 'destroy'])->name('delete');
            });
            Route::meta([
                'icon' => 'bx bxs-box',
                'prefix' => 'category',
                'as' => 'category.',
                'parent' => 'product',
                'name' => 'category',
                'module' => 'category',
                'middleware' => [checkPageAuthorization::class],
            ], function () {
                Route::get('/', [CustomerProductTypeController::class, 'index'])->name('index');
                Route::post('/', [CustomerProductTypeController::class, 'store'])->name('store');
                Route::put('/{id?}', [CustomerProductTypeController::class, 'update'])->name('update');
                Route::get('/data-table', [CustomerProductTypeController::class, 'dataTable'])->name('data-table');
                Route::get('/{id?}', [CustomerProductTypeController::class, 'show'])->name('show');
                Route::delete('/{id?}', [CustomerProductTypeController::class, 'destroy'])->name('delete');
            });
            Route::meta([
                'icon' => 'bx bxs-box-alt',
                'prefix' => 'temp product',
                'as' => 'temp product.',
                'parent' => 'product',
                'name' => 'temp product',
                'module' => 'temp product',
                'middleware' => [checkPageAuthorization::class],
            ], function () {
                Route::get('/', [CustomerTemporaryProductController::class, 'index'])->name('index');
                Route::post('/', [CustomerTemporaryProductController::class, 'store'])->name('store');
                Route::post('/store-temp-product/{date?}', [CustomerTemporaryProductController::class, 'storeTempProduct'])->name('store-temp-product');
                Route::post('/{id?}', [CustomerTemporaryProductController::class, 'update'])->name('update');
                Route::get('/data-table', [CustomerTemporaryProductController::class, 'dataTable'])->name('data-table');
                Route::get('/temp-product', [CustomerTemporaryProductController::class, 'tempProduct'])->name('temp-product');
                Route::get('/{id?}', [CustomerTemporaryProductController::class, 'show'])->name('show');
                Route::delete('/{id?}', [CustomerTemporaryProductController::class, 'destroy'])->name('delete');
            });
            Route::meta([
                'icon' => 'bx bxs-package',
                'prefix' => 'products',
                'as' => 'products.',
                'parent' => 'product',
                'name' => 'products',
                'module' => 'products',
                'middleware' => [checkPageAuthorization::class],
            ], function () {
                Route::get('/', [CustomerCompanyGoodController::class, 'index'])->name('index');
                Route::post('/', [CustomerCompanyGoodController::class, 'store'])->name('store');
                Route::post('/store-temp-product/{date?}', [CustomerTemporaryProductController::class, 'storeTempProduct'])->name('store-temp-product');
                Route::post('/{id?}', [CustomerCompanyGoodController::class, 'update'])->name('update');
                Route::get('/data-table', [CustomerCompanyGoodController::class, 'dataTable'])->name('data-table');
                Route::get('/temp-product', [CustomerCompanyGoodController::class, 'tempProduct'])->name('temp-product');
                Route::get('/{id?}', [CustomerCompanyGoodController::class, 'show'])->name('show');
                Route::delete('/{id?}', [CustomerCompanyGoodController::class, 'destroy'])->name('delete');
            });
            Route::meta([
                'icon' => 'bx bxs-discount',
                'prefix' => 'discount',
                'as' => 'discount.',
                'parent' => 'product',
                'name' => 'discount',
                'module' => 'discount',
                'middleware' => [checkPageAuthorization::class],
            ], function () {
                Route::get('/', [CustomerCompanyDiscountController::class, 'index'])->name('index');
                Route::post('/', [CustomerCompanyDiscountController::class, 'store'])->name('store');
                Route::put('/{id?}', [CustomerCompanyDiscountController::class, 'update'])->name('update');
                Route::get('/data-table', [CustomerCompanyDiscountController::class, 'dataTable'])->name('data-table');
                Route::get('/{id?}', [CustomerCompanyDiscountController::class, 'show'])->name('show');
                Route::delete('/{id?}', [CustomerCompanyDiscountController::class, 'destroy'])->name('delete');
            });
        });
        Route::meta([
            'icon' => 'bx bx-folder-zip',
            'prefix' => 'inventory',
            'as' => 'inventory.',
            'parent' => null,
            'module' => 'inventory',
            'name' => 'inventory',
        ], function () {
            Route::meta([
                'icon' => 'bx bxs-monitor-wide',
                'prefix' => 'transaction',
                'as' => 'transaction.',
                'parent' => 'inventory',
                'module' => 'transaction',
                'name' => 'transaction',
            ], function () {
                Route::get('/', [CustomerProductTransactionController::class, 'index'])->name('index');
                Route::post('/', [CustomerProductTransactionController::class, 'store'])->name('store');
                Route::post('/validate-transaction-items', [CustomerProductTransactionController::class, 'validateTransactionItems'])->name('validate-transaction-items');
                Route::get('/transaction-receipt/{orderCode?}/print', [CustomerProductTransactionController::class, 'viewPdf'])->name('print-transaction-receipt');
                Route::get('/product-data-table', [CustomerProductTransactionController::class, 'productDataTable'])->name('product-data-table');
                Route::get('/data-table', [CustomerProductTransactionController::class, 'dataTable'])->name('data-table');
                Route::get('/discount-data-table', [CustomerProductTransactionController::class, 'discountDataTable'])->name('discount-data-table');
                Route::get('/validate-discount-code/{id?}', [CustomerProductTransactionController::class, 'validateDiscountCode'])->name('validate-discount-code');
            });
            Route::meta([
                'icon' => 'bx bxs-chart-bar-big-columns',
                'prefix' => 'stocktaking',
                'as' => 'stocktaking.',
                'parent' => 'inventory',
                'module' => 'stocktaking',
                'name' => 'stocktaking',
            ], function () {
                Route::get('/', [CustomerCompanyStocktakingController::class, 'index'])->name('index');
                Route::post('/', [CustomerCompanyStocktakingController::class, 'store'])->name('store');
                Route::put('/{id?}', [CustomerCompanyStocktakingController::class, 'update'])->name('update');
                Route::get('/data-table', [CustomerCompanyStocktakingController::class, 'dataTable'])->name('data-table');
                Route::get('/{id?}', [CustomerCompanyStocktakingController::class, 'show'])->name('show');
                Route::post('/{id?}', [CustomerCompanyStocktakingController::class, 'approveStocktaking'])->name('approve');
                Route::delete('/{id?}', [CustomerCompanyStocktakingController::class, 'destroy'])->name('delete');
            });
        });
        Route::meta([
            'icon' => 'bx bxs-printer',
            'prefix' => 'report',
            'as' => 'report.',
            'parent' => null,
            'name' => 'report',
        ], function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::post('/', [ReportController::class, 'generateReport'])->name('generate-report');
        });
        Route::meta([
            'parent-icon' => 'bx bx-cog',
            'prefix' => 'settings',
            'as' => 'settings.',
            'name' => 'settings',
            'parent' => null,
            'module' => 'settings',
            'middleware' => [checkPageAuthorization::class],
        ], function () {
            Route::meta([
                'icon' => 'bx bxs-building',
                'prefix' => 'your-company',
                'as' => 'your-company.',
                'parent' => 'settings',
                'name' => 'your-company',
                'module' => 'your-company',
                'middleware' => [checkPageAuthorization::class],
            ], function () {
                Route::get('/', [CompanyController::class, 'index'])->name('index');
                Route::post('/', [CompanyController::class, 'store'])->name('store');
                Route::get('/company', [CompanyController::class, 'company'])->name('companies');
                Route::get('/profile', [CompanyController::class, 'profile'])->name('profile');
                Route::post('/login-company', [CompanyController::class, 'loginCompany'])->name('login-company');
                Route::post('/{id?}', [CompanyController::class, 'update'])->name('update');
                Route::get('/data-table', [CompanyController::class, 'dataTable'])->name('data-table');
                Route::get('/{id?}', [CompanyController::class, 'show'])->name('show');
                Route::delete('/{id?}', [CompanyController::class, 'destroy'])->name('delete');
            });
            Route::meta([
                'icon' => 'bx bxs-user-id-card',
                'prefix' => 'role',
                'as' => 'role.',
                'name' => 'role',
                'parent' => 'settings',
                'module' => 'role',
                'middleware' => [checkPageAuthorization::class],
            ], function () {
                Route::get('/', [RoleController::class, 'index'])->name('index');
                Route::post('/', [RoleController::class, 'store'])->name('store');
                Route::put('/{id?}', [RoleController::class, 'update'])->name('update');
                Route::get('/data-table', [RoleController::class, 'dataTable'])->name('data-table');
                Route::get('/{id?}', [RoleController::class, 'show'])->name('show');
                Route::delete('/{id?}', [RoleController::class, 'destroy'])->name('delete');
            });
            Route::meta([
                'icon' => 'bx bxs-user-check',
                'prefix' => 'permission',
                'as' => 'permission.',
                'name' => 'permission',
                'parent' => 'settings',
                'module' => 'permission',
                'middleware' => [checkPageAuthorization::class],
            ], function () {
                Route::get('/', [PermissionController::class, 'index'])->name('index');
                Route::post('/', [PermissionController::class, 'store'])->name('store');
                Route::put('/{id?}', [PermissionController::class, 'update'])->name('update');
                Route::get('/data-table', [PermissionController::class, 'dataTable'])->name('data-table');
                Route::get('/{id?}', [PermissionController::class, 'show'])->name('show');
                Route::delete('/{id?}', [PermissionController::class, 'destroy'])->name('delete');
            });
            Route::prefix('subscription')->middleware([AuthorizationOnly::class])->name('subscription.')->group(function () {
                Route::get('/', [SubscriptionController::class, 'index'])->name('index');
                Route::post('/', [SubscriptionController::class, 'store'])->name('store');
                Route::put('/{id?}', [SubscriptionController::class, 'update'])->name('update');
                Route::get('/data-table', [SubscriptionController::class, 'dataTable'])->name('data-table');
                Route::get('/{id?}', [SubscriptionController::class, 'show'])->name('show');
                Route::delete('/{id?}', [SubscriptionController::class, 'destroy'])->name('delete');
            });
        });
    });
});
