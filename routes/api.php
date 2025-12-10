<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/signIn', [AuthController::class, 'signIn']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/signOut', [AuthController::class, 'signOut']);
    Route::post('/refreshToken', [AuthController::class, 'refreshToken']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    Route::group(['prefix' => '/user'], function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });

    Route::group(['prefix' => '/plan'], function () {
        Route::get('/', [PlanController::class, 'index']);
        Route::post('/', [PlanController::class, 'store']);
        Route::get('/available', [PlanController::class, 'available']);
        Route::get('/{plan}', [PlanController::class, 'show']);
        Route::put('/{plan}', [PlanController::class, 'update']);
        Route::delete('/{plan}', [PlanController::class, 'destroy']);
    });

    Route::group(['prefix' => '/class'], function () {
        Route::get('/', [ClassController::class, 'index']);
        Route::post('/', [ClassController::class, 'store']);
        Route::get('/{class}', [ClassController::class, 'show']);
        Route::put('/{class}', [ClassController::class, 'update']);
        Route::delete('/{class}', [ClassController::class, 'destroy']);
    });
});
