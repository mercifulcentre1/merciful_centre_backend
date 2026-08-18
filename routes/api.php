<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SermonsController;
use App\Http\Controllers\Api\EventsController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\LivestreamController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\AdminUserController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

Route::get('/sermons', [SermonsController::class, 'index']);
Route::get('/sermons/{id}', [SermonsController::class, 'show']);

Route::get('/events', [EventsController::class, 'index']);
Route::get('/events/{id}', [EventsController::class, 'show']);

Route::get('/gallery', [GalleryController::class, 'index']);

Route::get('/livestream/settings', [LivestreamController::class, 'getSettings']);
Route::get('/livestream/archives', [LivestreamController::class, 'archives']);

Route::get('/settings', [SettingController::class, 'index']);


// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Sermons
    Route::post('/sermons', [SermonsController::class, 'store']);
    Route::put('/sermons/{id}', [SermonsController::class, 'update']);
    Route::delete('/sermons/{id}', [SermonsController::class, 'destroy']);
    
    // Events
    Route::post('/events', [EventsController::class, 'store']);
    Route::put('/events/{id}', [EventsController::class, 'update']);
    Route::delete('/events/{id}', [EventsController::class, 'destroy']);
    
    // Gallery
    Route::post('/gallery', [GalleryController::class, 'store']);
    Route::post('/gallery/{id}', [GalleryController::class, 'update']);
    Route::delete('/gallery/{id}', [GalleryController::class, 'destroy']);
    
    // Users
    Route::get('/users', [AdminUserController::class, 'index']);
    Route::post('/users', [AdminUserController::class, 'store']);
    Route::put('/users/{id}', [AdminUserController::class, 'update']);
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy']);
    
    // Livestream
    Route::post('/livestream/settings', [LivestreamController::class, 'updateSettings']);
    Route::post('/livestream/archives', [LivestreamController::class, 'storeArchive']);
    Route::delete('/livestream/archives/{id}', [LivestreamController::class, 'destroyArchive']);
    
    // Settings
    Route::post('/settings', [SettingController::class, 'update']);
});
