<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('auth')->group(function() {
    Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/register-by-admin', [\App\Http\Controllers\Api\AuthController::class, 'registerByAdmin']);
        Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
        Route::post('/reset-password', [\App\Http\Controllers\Api\AuthController::class, 'resetPassword']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [\App\Http\Controllers\Api\AuthController::class, 'me']);
    Route::prefix('users')->group(function() {
        Route::get('/', [\App\Http\Controllers\Api\UserController::class, 'index']);

        Route::post('/customers', [\App\Http\Controllers\Api\CustomerController::class, 'store']);
        Route::post('/medical-persons', [\App\Http\Controllers\Api\CustomerController::class, 'store']);
        Route::post('/nurses', [\App\Http\Controllers\Api\NurseController::class, 'store']);
        Route::post('/patients', [\App\Http\Controllers\Api\PatientController::class, 'store']);
    });

    Route::prefix('roles')->group(function() {
        Route::patch('/users/{uuid}', [\App\Http\Controllers\Api\RoleUserController::class, 'update']);

        Route::get('/', [\App\Http\Controllers\Api\RoleController::class, 'index']);
        Route::get('/{uuid}', [\App\Http\Controllers\Api\RoleController::class, 'show']);
        Route::post('/', [\App\Http\Controllers\Api\RoleController::class, 'store']);
        Route::patch('/{uuid}', [\App\Http\Controllers\Api\RoleController::class, 'update']);
        Route::delete('/{uuid}', [\App\Http\Controllers\Api\RoleController::class, 'destroy']);
    });

    Route::prefix('service_types')->group(function() {
        Route::get('/', [\App\Http\Controllers\Api\ServiceTypeController::class, 'index']);
        Route::get('/{uuid}', [\App\Http\Controllers\Api\ServiceTypeController::class, 'show']);
        Route::post('/', [\App\Http\Controllers\Api\ServiceTypeController::class, 'store']);
        Route::patch('/{uuid}', [\App\Http\Controllers\Api\ServiceTypeController::class, 'update']);
        Route::delete('/{uuid}', [\App\Http\Controllers\Api\ServiceTypeController::class, 'destroy']);
    });

    Route::prefix('drugs')->group(function() {
        Route::get('/', [\App\Http\Controllers\Api\DrugController::class, 'index']);
        Route::get('/{uuid}', [\App\Http\Controllers\Api\DrugController::class, 'show']);
        Route::post('/', [\App\Http\Controllers\Api\DrugController::class, 'store']);
        Route::patch('/{uuid}', [\App\Http\Controllers\Api\DrugController::class, 'update']);
        Route::delete('/{uuid}', [\App\Http\Controllers\Api\DrugController::class, 'destroy']);
    });

    Route::prefix('payment_methods')->group(function() {
        Route::get('/', [\App\Http\Controllers\Api\PaymentMethodController::class, 'index']);
        Route::get('/{uuid}', [\App\Http\Controllers\Api\PaymentMethodController::class, 'show']);
        Route::post('/', [\App\Http\Controllers\Api\PaymentMethodController::class, 'store']);
        Route::patch('/{uuid}', [\App\Http\Controllers\Api\PaymentMethodController::class, 'update']);
        Route::delete('/{uuid}', [\App\Http\Controllers\Api\PaymentMethodController::class, 'destroy']);
    });

    Route::prefix('transactions')->group(function() {
        Route::get('/', [\App\Http\Controllers\Api\TransactionController::class, 'index']);
        Route::get('/{uuid}', [\App\Http\Controllers\Api\TransactionController::class, 'show']);
        Route::post('/', [\App\Http\Controllers\Api\TransactionController::class, 'store']);
    });
});
