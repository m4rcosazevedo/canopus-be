<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ClassRegistrationController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\UserAddressController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserDocumentController;
use App\Http\Controllers\UserTypeController;
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
        Route::apiResource('/{user}/document', UserDocumentController::class);
        Route::apiResource('/{user}/address', UserAddressController::class);
    });
    Route::apiResource('/user', UserController::class);

    /** User Type */
    Route::group(['prefix' => '/user-type'], function () {
        Route::get('/options', [UserTypeController::class, 'options']);
    });
    Route::apiResource('user-type', UserTypeController::class);

    /** Plans */
    Route::group(['prefix' => '/plan'], function () {
        Route::get('/available', [PlanController::class, 'available']);
        Route::get('/{planId}/available-classes', [ClassController::class, 'availableClasses']);
    });
    Route::apiResource('/plan', PlanController::class);

    /** Class */
    Route::apiResource('/class', ClassController::class);

    /** Class Registration */
    Route::group(['prefix' => '/class-registration'], function () {
        Route::get('/', [ClassRegistrationController::class, 'index']);
    });

    /** Enroll */
    Route::post('/enroll', [EnrollmentController::class, 'enroll']);

    /** AuditLog */
    Route::group(['prefix' => '/audit-log'], function () {
        Route::get('/', [AuditLogController::class, 'index']);
        Route::get('/{auditLog}', [AuditLogController::class, 'show']);
        Route::get('/transaction/{transactionId}', [AuditLogController::class, 'showTransaction']);
    });

    /** State */
    Route::group(['prefix' => '/state'], function () {
        Route::get('/options', [StateController::class, 'options']);
    });
    Route::apiResource('/state', StateController::class);

    /** City */
    Route::group(['prefix' => '/city'], function () {
        Route::get('/options', [CityController::class, 'options']);
    });
    Route::apiResource('/city', CityController::class);

    /** Address */
    Route::group(['prefix' => '/address'], function () {
        Route::get('/byZipCode', [AddressController::class, 'searchByZipCode']);
        Route::post('/byZipCode', [AddressController::class, 'storeByZipCode']);
        Route::get('/streetTypes', [AddressController::class, 'streetTypes']);
    });
    Route::apiResource('address', AddressController::class);

    /** Document Type */
    Route::group(['prefix' => '/document-type'], function () {
        Route::get('/options', [DocumentTypeController::class, 'options']);
    });
    Route::apiResource('document-type', DocumentTypeController::class);
});
