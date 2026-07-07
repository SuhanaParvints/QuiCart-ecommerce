<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\OrderController;
use App\Http\Controllers\Api\Admin\ReviewController as AdminReviewController;

/*
|--------------------------------------------------------------------------
| Public Auth Routes
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Public Product Routes
|--------------------------------------------------------------------------
*/

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Public Approved Reviews
|--------------------------------------------------------------------------
*/

Route::get('/products/{id}/reviews', [ReviewController::class, 'productReviews']);

/*
|--------------------------------------------------------------------------
| TEMP ADMIN ROUTES - No Login Required
|--------------------------------------------------------------------------
*/

/* Dashboard */
Route::get('/admin/dashboard', [OrderController::class, 'dashboard']);

/* Users */
Route::get('/admin/users', [UserController::class, 'index']);
Route::get('/admin/users/{id}', [UserController::class, 'show']);
Route::put('/admin/users/{id}', [UserController::class, 'update']);
Route::delete('/admin/users/{id}', [UserController::class, 'destroy']);

/* Orders */
Route::get('/admin/orders', [OrderController::class, 'index']);
Route::get('/admin/orders/{id}', [OrderController::class, 'show']);
Route::put('/admin/orders/{id}/status', [OrderController::class, 'updateStatus']);
Route::delete('/admin/orders/{id}', [OrderController::class, 'destroy']);

/* Products */
Route::get('/admin/products', [ProductController::class, 'index']);
Route::post('/admin/products/upload-image', [ProductController::class, 'uploadImage']);
Route::post('/admin/products', [ProductController::class, 'store']);
Route::get('/admin/products/{id}', [ProductController::class, 'show']);
Route::put('/admin/products/{id}', [ProductController::class, 'update']);
Route::delete('/admin/products/{id}', [ProductController::class, 'destroy']);

/* Reviews */
Route::get('/admin/reviews', [AdminReviewController::class, 'index']);
Route::get('/admin/reviews/dashboard', [AdminReviewController::class, 'dashboard']);
Route::put('/admin/reviews/{id}/status', [AdminReviewController::class, 'updateStatus']);
Route::delete('/admin/reviews/{id}', [AdminReviewController::class, 'destroy']);

/*
|--------------------------------------------------------------------------
| Protected User Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    /* User Profile */
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    Route::post('/user/avatar', [AuthController::class, 'uploadAvatar']);
    Route::delete('/user/avatar', [AuthController::class, 'deleteAvatar']);
    Route::post('/logout', [AuthController::class, 'logout']);

    /* Customer Orders */
    Route::get('/orders', [OrderController::class, 'myOrders']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::put('/orders/{id}/cancel', [OrderController::class, 'cancel']);

    /* Submit Product Review */
    Route::post('/products/{id}/reviews', [ReviewController::class, 'store']);
});