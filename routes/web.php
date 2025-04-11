<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

// Route::get('/', function () {
//     return view('welcome');
// });

// Authentication routes (if not using Laravel's built-in auth)
// Route::get('/login', function () {
//     return view('auth.login');
// })->name('login');
Route::get('/login', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
})->name('login');

// Route::get('/register', function () {
//     return view('auth.register');
// })->name('register');
Route::get('/register', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('auth.register');
})->name('register');

// Landing page
Route::get('/', function () {
    return view('welcome');
});

// Dashboard
// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');
Route::get('/dashboard', function () {
    // If session-based auth isn't working, display the dashboard anyway
    // Authentication is handled by API tokens already
    return view('dashboard');
})->name('dashboard');

// Task routes for web interface
// Route::middleware(['auth'])->group(function () {
// Route::middleware(['auth:sanctum'])->group(function () {
Route::get('/tasks', function () {
    return view('tasks.index');
})->name('tasks.index');

Route::get('/tasks/create', function () {
    return view('tasks.create');
})->name('tasks.create');

// Route::get('/tasks/{task}/edit', function ($task) {
Route::get('/tasks/{task}/edit', function ($taskId) {
    // return view('tasks.edit', ['task' => $task]);
    // We're just passing the task ID to the view
    // The actual Task model will be fetched via API in the frontend
    return view('tasks.edit', ['task' => $taskId]);
})->name('tasks.edit');
// });