<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AuthController;

// Public routes
Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
// Route::middleware('auth:sanctum')->post('/logout', [App\Http\Controllers\AuthController::class, 'logout']);

// Test route - returns a simple success message to test API connectivity
Route::get('/ping', function() {
    return response()->json(['message' => 'API is working']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // User info endpoint
    Route::get('/user', function (Request $request) {
        // Debug the authenticated user
        $user = $request->user();
        \Log::info('Auth user in /user endpoint: ' . ($user ? $user->id . ' - ' . $user->email : 'No user'));
        return $user;
    });

    // Task endpoints
    Route::apiResource('tasks', TaskController::class);
    Route::post('tasks/{task}/images', [TaskController::class, 'uploadImages']);
    Route::delete('tasks/{task}/images/{image}', [TaskController::class, 'deleteImage']);
});

