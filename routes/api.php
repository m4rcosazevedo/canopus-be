<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ClassRegistrationController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\UserAddressController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserDocumentController;
use App\Modules\City\Http\Controllers\CityController;
use App\Modules\DocumentType\Http\Controllers\DocumentTypeController;
use App\Modules\Permission\Http\Controllers\PermissionController;
use App\Modules\Report\Http\Controllers\ReportController;
use App\Modules\State\Http\Controllers\StateController;
use App\Modules\UserType\Http\Controllers\UserTypeController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/signIn', [AuthController::class, 'signIn']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/refreshToken', [AuthController::class, 'refreshToken']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::get('/me', [AuthController::class, 'me']);

    // --- User Management ---
    Route::middleware('permission:user')->group(function () {
        Route::apiResource('/user', UserController::class);
        Route::apiResource('/user/{user}/document', UserDocumentController::class);
        Route::apiResource('/user/{user}/address', UserAddressController::class);
    });

    // --- User Type Management ---
    Route::get('/user-type/options', [UserTypeController::class, 'options'])->middleware('permission:user-type.options');
    Route::middleware('permission:user-type')->group(function () {
        Route::apiResource('/user-type', UserTypeController::class);
        Route::post('/user-type/{userType}/permissions', [UserTypeController::class, 'syncPermissions']);
    });

    // --- AuditLog Management ---
    Route::middleware('permission:audit-log')->group(function () {
        Route::get('/audit-log', [AuditLogController::class, 'index']);
        Route::get('/audit-log/{auditLog}', [AuditLogController::class, 'show']);
        Route::get('/audit-log/transaction/{transactionId}', [AuditLogController::class, 'showTransaction']);
    });

    // --- State Management ---
    Route::get('/state/options', [StateController::class, 'options'])->middleware('permission:state.options');
    Route::middleware('permission:state')->group(function () {
        Route::apiResource('/state', StateController::class);
    });

    // --- City Management ---
    Route::get('/city/options', [CityController::class, 'options'])->middleware('permission:city.options');
    Route::middleware('permission:city')->group(function () {
        Route::apiResource('/city', CityController::class);
    });

    // --- Address Management ---
    Route::middleware('permission:address')->group(function () {
        Route::apiResource('/address', AddressController::class);
        Route::get('/address/byZipCode', [AddressController::class, 'searchByZipCode']);
        Route::post('/address/byZipCode', [AddressController::class, 'storeByZipCode']);
        Route::get('/address/streetTypes', [AddressController::class, 'streetTypes']);
    });

    // --- Document Type Management ---
    Route::get('/document-type/options', [DocumentTypeController::class, 'options'])->middleware('permission:document-type.options');
    Route::middleware('permission:document-type')->group(function () {
        Route::apiResource('/document-type', DocumentTypeController::class);
    });

    // --- Report Type Management ---
    Route::middleware('permission:report')->group(function () {
        Route::post('/report', [ReportController::class, 'store']);
        Route::get('/report', [ReportController::class, 'index']);
        Route::get('/report/{report}', [ReportController::class, 'show']);
        Route::get('/report/{report}/download', [ReportController::class, 'download']);
    });

    // --- Permissions Management ---
    Route::middleware('permission:permission')->group(function () {
        Route::apiResource('/permission', PermissionController::class);
    });


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
});
