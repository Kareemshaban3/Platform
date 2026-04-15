<?php

use App\Http\Controllers\AttachmentsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminControll;
use App\Http\Controllers\ReviewsController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {

    Route::apiResource('/category', CategoryController::class)->except(['index', 'show']);

    Route::apiResource('/attachment', AttachmentsController::class)->except(['index', 'show']);

    Route::get('/admin', [AdminControll::class, 'index']);
    Route::post('/admin', [AdminControll::class, 'store']);
    Route::get('/admin/{id}', [AdminControll::class, 'show']);
    Route::put('/admin/{id}', [AdminControll::class, 'update']);
    Route::delete('/admin/{id}', [AdminControll::class, 'destroy']);


    Route::post('/admin/{id}/toggle-ban', [AdminControll::class, 'toggleBan']);
    Route::post('/admin/{id}/change-role', [AdminControll::class, 'changeUserRole']);
    Route::post('/categories/toggle-access', [AdminControll::class, 'toggleCategoryAccess']);
    Route::post('/users/reset-device/{id}', [AdminControll::class, 'resetDevice']);
});





Route::middleware(['auth:sanctum', 'role:client,admin'])->group(function () {


    Route::get('/category', [CategoryController::class, 'index']);
    Route::get('/category/{category}', [CategoryController::class, 'show']);
    Route::get('/attachment', [AttachmentsController::class, 'index']);
    Route::get('/attachment/{attachment}', [AttachmentsController::class, 'show']);
    Route::post('/logout', [AuthController::class, 'logout']);



    Route::get('reviews', [ReviewsController::class, 'index']);
    Route::post('reviews', [ReviewsController::class, 'store']);
    Route::put('reviews', [ReviewsController::class, 'update']);
    Route::delete('reviews', [ReviewsController::class, 'destroy']);
});
