<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Company\MasterTaskController;
use App\Http\Controllers\Company\TaskController;
use App\Http\Controllers\Company\UserController;
use App\Http\Controllers\Company\WarehouseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Inventory\ProductController;
use App\Http\Controllers\Inventory\StocktakingController;
use App\Http\Controllers\Inventory\TemporaryProductController;
use App\Http\Controllers\Man\TransactionController;
use App\Http\Controllers\Product\CategoryController;
use App\Http\Controllers\Product\WeightController;
use App\Http\Controllers\Promo\DiscountController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Settings\CompanyController;
use App\Http\Controllers\Settings\PermissionController;
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
| be assigned to the 'web' middleware group. Make something great!
|
*/

Route::meta(['group' => 'web'], function () {
    // Public Landing Page
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::middleware([UnAuthorization::class])->name('auth.')->prefix('auth')->group(function () {
        Route::get('/login', [AuthController::class, 'index'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.process');
        Route::get('/registration', [AuthController::class, 'register'])->name('registration');
        Route::post('/registration', [AuthController::class, 'registration'])->middleware('throttle:3,1')->name('registration.process');
    });
    Route::middleware([unSelectCustomerCompany::class])->group(function () {
        Route::get('/select-company', function () {
            return view('select-company');
        })->name('select-company');
        Route::post('/select-company', [AuthController::class, 'selectCompany'])
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
            Route::get('/', [DashboardController::class, 'index'])->name('index');
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
                Route::get('/', [UserController::class, 'index'])->name('index');
                Route::post('/', [UserController::class, 'store'])->name('store');
                Route::get('/profile', [UserController::class, 'profile'])->name('profile');
                Route::post('/update-profile', [UserController::class, 'updateProfile'])->name('update-profile');
                Route::patch('/generate-affiliate-code', [UserController::class, 'generateAffiliateCode'])->name('generate-affiliate-code');
                Route::post('/generate-link', [UserController::class, 'generateRegistrationLink'])->name('registration-link');
                Route::put('/{id?}', [UserController::class, 'update'])->name('update');
                Route::get('/data-table', [UserController::class, 'dataTable'])->name('data-table');
                Route::get('/{id?}', [UserController::class, 'show'])->name('show');
                Route::delete('/{id?}', [UserController::class, 'destroy'])->name('delete');
            });
            Route::meta([
                'icon' => 'bx bxs-list-square',
                'prefix' => 'task-template',
                'as' => 'task-template.',
                'parent' => 'company',
                'name' => 'task-template',
                'module' => 'task-template',
                'middleware' => [checkPageAuthorization::class],
            ], function () {
                Route::get('/', [MasterTaskController::class, 'index'])->name('index');
                Route::post('/', [MasterTaskController::class, 'store'])->name('store');
                Route::put('/{id?}', [MasterTaskController::class, 'update'])->name('update');
                Route::get('/data-table', [MasterTaskController::class, 'dataTable'])->name('data-table');
                Route::get('/{id?}', [MasterTaskController::class, 'show'])->name('show');
                Route::delete('/{id?}', [MasterTaskController::class, 'destroy'])->name('delete');
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
                Route::get('/', [TaskController::class, 'index'])->name('index');
                Route::get('/new-task', [TaskController::class, 'newTask'])->name('new-task');
                Route::get('/unfinish-task', [TaskController::class, 'unfinishTask'])->name('unfinish-task');
                Route::post('/', [TaskController::class, 'store'])->name('store');
                Route::get('/get-evidence/{id?}', [TaskController::class, 'getEvidence'])->name('get-evidence');
                Route::post('/finish-task/{id?}/{type?}', [TaskController::class, 'finishTask'])->name('finish-task');
                Route::put('/{id?}', [TaskController::class, 'update'])->name('update');
                Route::put('/start-task/{id?}/{type?}', [TaskController::class, 'startTask'])->name('start-task');
                Route::get('/data-table', [TaskController::class, 'dataTable'])->name('data-table');
                Route::get('/{id?}', [TaskController::class, 'show'])->name('show');
                Route::delete('/detail/{id?}', [TaskController::class, 'destroyDetail'])->name('delete-detail');
                Route::delete('/{id?}', [TaskController::class, 'destroy'])->name('delete');
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
                'icon' => 'bx bxs-cog',
                'prefix' => 'weight',
                'as' => 'weight.',
                'parent' => 'product',
                'name' => 'weight',
                'module' => 'weight',
                'middleware' => [checkPageAuthorization::class],
            ], function () {
                Route::get('/', [WeightController::class, 'index'])->name('index')->defaults('module', 'settings');
                Route::post('/', [WeightController::class, 'store'])->name('store');
                Route::put('/{id?}', [WeightController::class, 'update'])->name('update');
                Route::get('/data-table', [WeightController::class, 'dataTable'])->name('data-table');
                Route::get('/{id?}', [WeightController::class, 'show'])->name('show');
                Route::delete('/{id?}', [WeightController::class, 'destroy'])->name('delete');
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
                Route::get('/', [CategoryController::class, 'index'])->name('index');
                Route::post('/', [CategoryController::class, 'store'])->name('store');
                Route::put('/{id?}', [CategoryController::class, 'update'])->name('update');
                Route::get('/data-table', [CategoryController::class, 'dataTable'])->name('data-table');
                Route::get('/{id?}', [CategoryController::class, 'show'])->name('show');
                Route::delete('/{id?}', [CategoryController::class, 'destroy'])->name('delete');
            });
        });

        Route::meta([
            'parent-icon' => 'bx bx-folder-zip',
            'prefix' => 'inventory',
            'as' => 'inventory.',
            'parent' => null,
            'name' => 'inventory',
            'module' => 'inventory',
            'middleware' => [checkPageAuthorization::class],
        ], function () {
            Route::meta([
                'icon' => 'bx bxs-box-alt',
                'prefix' => 'temporary-product',
                'as' => 'temporary-product.',
                'parent' => 'inventory',
                'name' => 'temporary-product',
                'module' => 'temporary-product',
                'middleware' => [checkPageAuthorization::class],
            ], function () {
                Route::get('/', [TemporaryProductController::class, 'index'])->name('index');
                Route::post('/', [TemporaryProductController::class, 'store'])->name('store');
                Route::post('/store-temporary-product/{date?}', [TemporaryProductController::class, 'storeTempProduct'])->name('store-temporary-product');
                Route::post('/{id?}', [TemporaryProductController::class, 'update'])->name('update');
                Route::get('/data-table', [TemporaryProductController::class, 'dataTable'])->name('data-table');
                Route::get('/temporary-product', [TemporaryProductController::class, 'tempProduct'])->name('temporary-product');
                Route::get('/{id?}', [TemporaryProductController::class, 'show'])->name('show');
                Route::delete('/{id?}', [TemporaryProductController::class, 'destroy'])->name('delete');
            });
            Route::meta([
                'icon' => 'bx bxs-package',
                'prefix' => 'your-products',
                'as' => 'your-products.',
                'parent' => 'inventory',
                'name' => 'your-products',
                'module' => 'your-products',
                'middleware' => [checkPageAuthorization::class],
            ], function () {
                Route::get('/', [ProductController::class, 'index'])->name('index');
                Route::post('/', [ProductController::class, 'store'])->name('store');
                Route::post('/store-temporary-product/{date?}', [TemporaryProductController::class, 'storeTempProduct'])->name('store-temporary-product');
                Route::post('/{id?}', [ProductController::class, 'update'])->name('update');
                Route::get('/data-table', [ProductController::class, 'dataTable'])->name('data-table');
                Route::get('/temporary-product', [ProductController::class, 'tempProduct'])->name('temporary-product');
                Route::get('/{id?}', [ProductController::class, 'show'])->name('show');
                Route::delete('/{id?}', [ProductController::class, 'destroy'])->name('delete');
            });

            Route::meta([
                'icon' => 'bx bxs-monitor-wide',
                'prefix' => 'transaction',
                'as' => 'transaction.',
                'parent' => 'inventory',
                'module' => 'transaction',
                'name' => 'transaction',
                'middleware' => [checkPageAuthorization::class],
            ], function () {
                Route::get('/', [TransactionController::class, 'index'])->name('index');
                Route::post('/', [TransactionController::class, 'store'])->name('store');
                Route::post('/validate-transaction-items', [TransactionController::class, 'validateTransactionItems'])->name('validate-transaction-items');
                Route::get('/transaction-receipt/{orderCode?}/print', [TransactionController::class, 'viewPdf'])->name('print-transaction-receipt');
                Route::get('/product-data-table', [TransactionController::class, 'productDataTable'])->name('product-data-table');
                Route::get('/data-table', [TransactionController::class, 'dataTable'])->name('data-table');
                Route::get('/discount-data-table', [TransactionController::class, 'discountDataTable'])->name('discount-data-table');
                Route::get('/validate-discount-code/{id?}', [TransactionController::class, 'validateDiscountCode'])->name('validate-discount-code');
            });
            Route::meta([
                'icon' => 'bx bxs-chart-bar-big-columns',
                'prefix' => 'stocktaking',
                'as' => 'stocktaking.',
                'parent' => 'inventory',
                'module' => 'stocktaking',
                'name' => 'stocktaking',
                'middleware' => [checkPageAuthorization::class],
            ], function () {
                Route::get('/', [StocktakingController::class, 'index'])->name('index');
                Route::post('/', [StocktakingController::class, 'store'])->name('store');
                Route::put('/{id?}', [StocktakingController::class, 'update'])->name('update');
                Route::get('/data-table', [StocktakingController::class, 'dataTable'])->name('data-table');
                Route::get('/{id?}', [StocktakingController::class, 'show'])->name('show');
                Route::post('/{id?}', [StocktakingController::class, 'approveStocktaking'])->name('approve');
                Route::delete('/{id?}', [StocktakingController::class, 'destroy'])->name('delete');
            });
        });
        Route::meta([
            'parent-icon' => 'bx bx-ticket',
            'prefix' => 'promo',
            'as' => 'promo.',
            'parent' => null,
            'name' => 'promo',
            'module' => 'promo',
            'middleware' => [checkPageAuthorization::class],
        ], function () {
            Route::meta([
                'icon' => 'bx bxs-discount',
                'prefix' => 'discount',
                'as' => 'discount.',
                'parent' => 'promo',
                'name' => 'discount',
                'module' => 'discount',
                'middleware' => [checkPageAuthorization::class],
            ], function () {
                Route::get('/', [DiscountController::class, 'index'])->name('index');
                Route::post('/', [DiscountController::class, 'store'])->name('store');
                Route::put('/{id?}', [DiscountController::class, 'update'])->name('update');
                Route::get('/data-table', [DiscountController::class, 'dataTable'])->name('data-table');
                Route::get('/{id?}', [DiscountController::class, 'show'])->name('show');
                Route::delete('/{id?}', [DiscountController::class, 'destroy'])->name('delete');
            });
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
                Route::get('/role-option/{id?}', [RoleController::class, 'role'])->name('role-options');
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
        Route::meta([
            'icon' => 'bx bxs-printer',
            'prefix' => 'report',
            'as' => 'report.',
            'parent' => null,
            'name' => 'report',
            'middleware' => [checkPageAuthorization::class],
        ], function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/debug-print', [ReportController::class, 'debugPrint'])->name('debug-print');
            Route::post('/', [ReportController::class, 'generateReport'])->name('generate-report');
        });
    });
});
