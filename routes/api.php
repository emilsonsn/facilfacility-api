<?php

use App\Http\Controllers\ActionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ComponentController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdminMiddleware;

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

Route::post('login', [AuthController::class, 'login']);

Route::get('validateToken', [AuthController::class, 'validateToken']);
Route::post('recoverPassword', [UserController::class, 'passwordRecovery']);
Route::post('updatePassword', [UserController::class, 'updatePassword']);


Route::get('validateToken', [AuthController::class, 'validateToken']);

Route::middleware('jwt')->group(function(){

    Route::middleware(AdminMiddleware::class)->group(function() {
        // Middleware do admin
    });

    Route::post('logout', [AuthController::class, 'logout']);

    Route::prefix('user')->group(function(){
        Route::get('all', [UserController::class, 'all']);
        Route::get('search', [UserController::class, 'search']);
        Route::get('cards', [UserController::class, 'cards']);
        Route::get('me', [UserController::class, 'getUser']);
        Route::post('create', [UserController::class, 'create']);
        Route::patch('{id}', [UserController::class, 'update']);
        Route::post('block/{id}', [UserController::class, 'userBlock']);
    });

    Route::prefix('facility')->group(function(){
        Route::get('search', [FacilityController::class, 'search']);
        Route::get('{id}', [FacilityController::class, 'getById']);
        Route::post('create', [FacilityController::class, 'create']);
        Route::post('upload-image/{id}', [FacilityController::class, 'uploadImage']);
        Route::patch('{id}', [FacilityController::class, 'update']);
        Route::delete('image/{id}', [FacilityController::class, 'deleteImage']);
        Route::delete('{id}', [FacilityController::class, 'delete']);
    });
    
    Route::prefix('component')->group(function(){
        Route::get('search', [ComponentController::class, 'search']);
        Route::get('{id}', [ComponentController::class, 'getById']);
        Route::post('create', [ComponentController::class, 'create']);
        Route::patch('{id}', [ComponentController::class, 'update']);
        Route::delete('image/{id}', [ComponentController::class, 'deleteImage']);
        Route::delete('{id}', [ComponentController::class, 'delete']);
    });

    Route::prefix('action')->group(function(){
        Route::get('search', [ActionController::class, 'search']);
        Route::get('{id}', [ActionController::class, 'getById']);
        Route::post('create', [ActionController::class, 'create']);
        Route::patch('{id}', [ActionController::class, 'update']);
        Route::delete('image/{id}', [ActionController::class, 'deleteImage']);
        Route::delete('{id}', [ActionController::class, 'delete']);
    });
    
    // Route::prefix('client')->group(function(){
    //     Route::get('search', [ClientController::class, 'search']);
    //     Route::post('create', [ClientController::class, 'create']);
    //     Route::patch('{id}', [ClientController::class, 'update']);
    //     Route::delete('{id}', [ClientController::class, 'delete']);
    // });
});
