<?php

use App\Http\Controllers\AuthController;
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

     // User Management
     Route::get('/user', [UserController::class, 'index']);
     Route::post('/user', [UserController::class, 'store']);
     Route::get('/user/{id}', [UserController::class, 'show']);
     Route::put('/user/{id}', [UserController::class, 'update']);
     Route::delete('/user/{id}', [UserController::class, 'destroy']);
 });
