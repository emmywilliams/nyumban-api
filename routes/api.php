<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\PropertyController;
use App\Http\Controllers\Api\V1\UnitController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\LocationController;
use App\Http\Controllers\Api\V1\FavoriteController;


Route::prefix('v1')->group(function () {

    // --- PUBLIC ROUTES ---
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/properties', [PropertyController::class, 'index']);
    Route::get('/properties/{property}', [PropertyController::class, 'show']);

    Route::get('/properties/{property}/units', [UnitController::class, 'index']);
    Route::get('/properties/{property}/units/{unit}', [UnitController::class, 'show']);

    Route::get('/categories', [CategoryController::class, 'index']);

    Route::prefix('locations')->group(function () {
        Route::get('districts', [LocationController::class, 'getDistricts']);
        Route::get('districts/{district}/counties', [LocationController::class, 'getCounties']);
        Route::get('counties/{county}/sub-counties', [LocationController::class, 'getSubCounties']);
        Route::get('sub-counties/{subCounty}/parishes', [LocationController::class, 'getParishes']);
        Route::get('parishes/{parish}/villages', [LocationController::class, 'getVillages']);
    });



    // --- PROTECTED ROUTES ---
    Route::middleware('auth:sanctum')->group(function () {
        // User Logout
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/my-properties', [PropertyController::class, 'myProperties']);

        // Properties (only for landlords to manage their own properties)
        Route::apiResource('properties', PropertyController::class)
            ->except(['index', 'show']);

        // Units to a Property
        Route::apiResource('properties.units', UnitController::class)->only(['store', 'update', 'destroy']);

        // Favorites
        Route::get('/favorites', [FavoriteController::class, 'index']);
        Route::post('/favorites/toggle', [FavoriteController::class, 'toggle']);
    });
});
