<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\GlobalErrorHandler;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\RegistrationController;

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

    // Authentication:Routes
    Route::middleware(['auth:sanctum'])->group(function () {
        // User Routes
        Route::get('/user/me', [AuthController::class, 'me']);
        Route::delete('/user/signout', [AuthController::class, 'signout']);
        // Audit Routes
        Route::get('/audit/list', [AuditController::class, 'index']);
        // Registration Routes
        Route::post('/registration/create', [RegistrationController::class, 'create']);
        Route::get('/registration/list', [RegistrationController::class, 'index']);

        // Admin: Routes
        Route::middleware('validateRole:admin')->group(function () {
            // Category Routes
            Route::post('/category/create', [CategoryController::class, 'create']);
            Route::delete('/category/remove/{id}', [CategoryController::class, 'remove']);
            // Event Routes
            Route::post('/event/create', [EventController::class, 'create']);
            Route::patch('/event/update/{id}', [EventController::class, 'update']);
            Route::delete('/event/remove/{id}', [EventController::class, 'remove']);
            // Audit Routes
            Route::get('/audit/{id}', [AuditController::class, 'show']);
        });
    });
});
