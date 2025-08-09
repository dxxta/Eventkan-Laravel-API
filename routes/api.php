<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\GlobalErrorHandler;

Route::middleware([GlobalErrorHandler::class])->group(function () {
    Route::post('/user/signin', [AuthController::class, 'signin']);
    Route::post('/user/signup', [AuthController::class, 'signup']);
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/user/me', [AuthController::class, 'me']);
        Route::delete('/user/signout', [AuthController::class, 'signout']);
        // Category Routes
        Route::get('/category/list', [CategoryController::class, 'index']);
        Route::middleware('validateRole:admin')->group(function () {
            Route::post('/category/create', [CategoryController::class, 'create']);
            Route::delete('/category/remove/{id}', [CategoryController::class, 'remove']);
        });
        // Event Routes
        // Route::get('/event/list', [EventController::class, 'index']);
        // Route::post('/event/create', [EventController::class, 'create']);
        // Route::delete('/event/remove/{id}', [EventController::class, 'remove']);
    });
});
