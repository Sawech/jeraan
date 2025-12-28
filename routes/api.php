<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DesignController;
use App\Http\Controllers\Admin\FabricController;
use App\Http\Controllers\Admin\SizeTypeCategoryController;
use App\Http\Controllers\Admin\SizeTypeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminSizeTypeCategoryUser;
use App\Http\Controllers\Admin\SizeGownController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ClientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json($request->user());
});

/*Route::group(['middleware'=>'api','prefix'=>'auth'], function($router){
Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);
Route::get('/profile',[AuthController::class,'profile']);
});*/

Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/send-verification-code', [AuthController::class, 'sendVerificationCode']);
    Route::post('/check-verification-code', [AuthController::class, 'checkVerificationCode']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    Route::group(['middleware' => 'jwt-auth:api'], function () {
        Route::get('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::post('/edit-profile', [AuthController::class, 'editProfile']);
    });
});

Route::group(['prefix' => 'list', 'middleware' => 'jwt-auth:api'], function () {
    Route::get('/category', [ListController::class, 'listCateogry']);
    Route::get('/design', [ListController::class, 'listDesign']);
    Route::get('/fabric', [ListController::class, 'listFabric']);
    Route::post('/size-gown', [ListController::class, 'sizeGown']);
    Route::get('/user-size-type-category', [ListController::class, 'sizeTypeCategoryUser']);
});

Route::group(['prefix' => 'order', 'middleware' => 'jwt-auth:api'], function () {
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/check-create', [OrderController::class, 'addOrder']);
    Route::post('/create', [OrderController::class, 'saveOrder']);
    Route::post('/show', [OrderController::class, 'showOrder']);
    Route::post('/upload-payment-image', [OrderController::class, 'uploadPaymentImage']);
    Route::post('/delete-order', [OrderController::class, 'deleteOrders']);
});

Route::group(['prefix' => 'setting'], function () {
    Route::get('/site-info', [SettingController::class, 'siteInfo']);
    Route::get('/settings', [SettingController::class, 'settings']);
    Route::post('/send-message', [SettingController::class, 'sendMessage']);
});

Route::group(['prefix' => 'admin', 'middleware' => ['jwt-auth:api', 'Admin-auth:api']], function () {
    Route::get('/category', [CategoryController::class, 'listCateogry']);
    Route::get('/all-category', [CategoryController::class, 'listAllCateogry']);
    Route::post('/category/add', [CategoryController::class, 'addCategory']);
    Route::post('/category/edit', [CategoryController::class, 'editCategory']);
    Route::post('/category/fetch', [CategoryController::class, 'fetchCategory']);
    Route::post('/category/delete', [CategoryController::class, 'deleteCategory']);

    Route::get('/fabric', [FabricController::class, 'listFabric']);
    Route::post('/fabric/add', [FabricController::class, 'addFabric']);
    Route::post('/fabric/edit', [FabricController::class, 'editFabric']);
    Route::post('/fabric/fetch', [FabricController::class, 'fetchFabric']);
    Route::post('/fabric/delete', [FabricController::class, 'deleteFabric']);

    Route::get('/size-type', [SizeTypeController::class, 'listSizeType']);
    Route::get('/all-size-type', [SizeTypeController::class, 'listAllSizeType']);
    Route::post('/size-type/add', [SizeTypeController::class, 'addSizeType']);
    Route::post('/size-type/edit', [SizeTypeController::class, 'editSizeType']);
    Route::post('/size-type/fetch', [SizeTypeController::class, 'fetchSizeType']);
    Route::post('/size-type/delete', [SizeTypeController::class, 'deleteSizeType']);

    Route::get('/design', [DesignController::class, 'listDesign']);
    Route::post('/design/add', [DesignController::class, 'addDesign']);
    Route::post('/design/edit', [DesignController::class, 'editDesign']);
    Route::post('/design/fetch', [DesignController::class, 'fetchDesign']);
    Route::post('/design/delete', [DesignController::class, 'deleteDesign']);

    Route::get('/size-type-category', [SizeTypeCategoryController::class, 'listSizeTypeCategory']);
    Route::post('/size-type-category/category', [SizeTypeCategoryController::class, 'listAllSizeTypeCategory']);
    Route::post('/size-type-category/add', [SizeTypeCategoryController::class, 'addSizeTypeCategory']);
    //Route::post('/size-type/edit',[SizeTypeController::class,'editSizeType']);
    Route::post('/size-type-category/fetch', [SizeTypeCategoryController::class, 'fetchSizeTypeCategory']);
    Route::post('/size-type-category/delete', [SizeTypeCategoryController::class, 'deleteSizeTypeCategory']);

    Route::post('/user', [UserController::class, 'listUser']);
    Route::post('/user/add', [UserController::class, 'addUser']);
    Route::post('/user/edit', [UserController::class, 'editUser']);
    Route::post('/user/fetch', [UserController::class, 'fetchUser']);
    Route::post('/user/change-status', [UserController::class, 'changeStatusUser']);
    Route::post('/user/delete', [UserController::class, 'deleteUser']);
    Route::get('/user/roles', [UserController::class, 'listAdminRole']);
    Route::get('/user/all-user', [UserController::class, 'listAllUser']);

    Route::post('/order', [AdminOrderController::class, 'listOrder']);
    Route::post('/order/edit', [AdminOrderController::class, 'editOrder']);
    Route::post('/order/fetch', [AdminOrderController::class, 'showOrder']);
    Route::post('/order/delete', [AdminOrderController::class, 'deleteOrder']);
    Route::post('/order/change-status', [AdminOrderController::class, 'changeStatusOrder']);
    Route::post('/order/save-order', [AdminOrderController::class, 'saveOrder']);

    Route::post('/size-type-category-user/add', [AdminSizeTypeCategoryUser::class, 'addSizeTypeCategoryUser']);
    Route::post('/size-type-category-user/list', [AdminSizeTypeCategoryUser::class, 'listUserSizeTypeCategory']);

    Route::get('/size-gown', [SizeGownController::class, 'listSizeGown']);
    Route::post('/size-gown/fetch', [SizeGownController::class, 'fetchSizeGown']);
    Route::post('/size-gown/add', [SizeGownController::class, 'addSizeGown']);
    Route::post('/size-gown/edit', [SizeGownController::class, 'editSizeGown']);
    Route::post('/size-gown/delete', [SizeGownController::class, 'deleteSizeGown']);

    Route::get('/dashboard', [DashboardController::class, 'dashboardList']);
    Route::get('/dashboard/site-setting', [DashboardController::class, 'fetchSiteSettings']);
    Route::get('/dashboard/messages', [DashboardController::class, 'messages']);
    Route::post('/dashboard/message/fetch', [DashboardController::class, 'fetchMessage']);
    Route::post('/dashboard/edit-profile', [DashboardController::class, 'editProfile']);
    Route::post('/dashboard/edit-site-info', [DashboardController::class, 'editSiteInfo']);
    Route::post('/dashboard/edit-setting', [DashboardController::class, 'editSetting']);
    Route::get('/dashboard/monthly-orders', [DashboardController::class, 'monthlyOrders']);
    Route::get('/dashboard/daily-orders', [DashboardController::class, 'dailyOrders']);
    Route::get('/dashboard/weekly-orders', [DashboardController::class, 'weeklyOrders']);



});
