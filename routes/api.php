<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ClassRegistrationController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserDocumentController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/signIn', [AuthController::class, 'signIn']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
//    Route::post('/signOut', [AuthController::class, 'signOut']);
    Route::post('/refreshToken', [AuthController::class, 'refreshToken']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);

    Route::get('/me', [AuthController::class, 'me']);

    Route::group(['prefix' => '/user'], function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);

        Route::group(['prefix' => '/{user}/document'], function () {
            Route::get('/', [UserDocumentController::class, 'index']);
            Route::post('/', [UserDocumentController::class, 'store']);
            Route::get('/{document}', [UserDocumentController::class, 'show']);
            Route::put('/{document}', [UserDocumentController::class, 'update']);
            Route::delete('/{document}', [UserDocumentController::class, 'destroy']);
        });
    });

    Route::group(['prefix' => '/plan'], function () {
        Route::get('/', [PlanController::class, 'index']);
        Route::post('/', [PlanController::class, 'store']);
        Route::get('/available', [PlanController::class, 'available']);
        Route::get('/{plan}', [PlanController::class, 'show']);
        Route::put('/{plan}', [PlanController::class, 'update']);
        Route::delete('/{plan}', [PlanController::class, 'destroy']);
        Route::get('/{planId}/available-classes', [ClassController::class, 'availableClasses']);
    });

    Route::group(['prefix' => '/class'], function () {
        Route::get('/', [ClassController::class, 'index']);
        Route::post('/', [ClassController::class, 'store']);
        Route::get('/{class}', [ClassController::class, 'show']);
        Route::put('/{class}', [ClassController::class, 'update']);
        Route::delete('/{class}', [ClassController::class, 'destroy']);
    });

    Route::group(['prefix' => '/class-registration'], function () {
        Route::get('/', [ClassRegistrationController::class, 'index']);
    });

    Route::post('/enroll', [EnrollmentController::class, 'enroll']);

    Route::group(['prefix' => '/audit-log'], function () {
        Route::get('/', [AuditLogController::class, 'index']);
        Route::get('/{auditLog}', [AuditLogController::class, 'show']);
        Route::get('/transaction/{transactionId}', [AuditLogController::class, 'showTransaction']);
    });

    Route::group(['prefix' => '/state'], function () {
        Route::get('/', [StateController::class, 'index']);
        Route::post('/', [StateController::class, 'store']);
        Route::get('/{state}', [StateController::class, 'show']);
        Route::put('/{state}', [StateController::class, 'update']);
        Route::delete('/{state}', [StateController::class, 'destroy']);
    });

    Route::group(['prefix' => '/city'], function () {
        Route::get('/', [CityController::class, 'index']);
        Route::post('/', [CityController::class, 'store']);
        Route::get('/{city}', [CityController::class, 'show']);
        Route::put('/{city}', [CityController::class, 'update']);
        Route::delete('/{city}', [CityController::class, 'destroy']);
    });

    Route::group(['prefix' => '/address'], function () {
        Route::get('/', [AddressController::class, 'index']);
        Route::post('/', [AddressController::class, 'store']);
        Route::post('/byZipCode', [AddressController::class, 'storeByZipCode']);
        Route::get('/{address}', [AddressController::class, 'show']);
        Route::put('/{address}', [AddressController::class, 'update']);
        Route::delete('/{address}', [AddressController::class, 'destroy']);
    });
});
