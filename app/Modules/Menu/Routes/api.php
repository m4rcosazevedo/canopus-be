<?php

use App\Modules\Menu\Http\Controllers\MenuController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('menu/available', [MenuController::class, 'available']);
Route::get('menu/options', [MenuController::class, 'options']);
Route::apiResource('menu', MenuController::class);
