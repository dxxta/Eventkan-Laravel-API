<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\GlobalErrorHandler;
use App\Http\Controllers\EventController;

Route::middleware([GlobalErrorHandler::class])->group(function () {
    // User Routes
    Route::post('/user/signin', [AuthController::class, 'signin']);
    Route::post('/user/signup', [AuthController::class, 'signup']);
    // Event Routes
    Route::get('/event/list', [EventController::class, 'index']);
    Route::get('/event/{id}/categories', [EventController::class,
    'eventCategories']);
    // Category Routes
    Route::get('/category/list', [CategoryController::class, 'index']);
    Route::middleware(['auth:sanctum'])->group(function () {
        // User Routes
        Route::get('/user/me', [AuthController::class, 'me']);
        Route::delete('/user/signout', [AuthController::class, 'signout']);
        // Category Routes
        Route::middleware('validateRole:admin')->group(function () {
            Route::post('/category/create', [CategoryController::class, 'create']);
            Route::delete('/category/remove/{id}', [CategoryController::class, 'remove']);
        });
        // Event Routes
        Route::middleware('validateRole:admin')->group(function () {
            Route::post('/event/create', [EventController::class, 'create']);
            Route::delete('/event/remove/{id}', [EventController::class, 'remove']);
        });
    });
});
