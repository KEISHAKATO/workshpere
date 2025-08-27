<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;

// Employer controllers
use App\Http\Controllers\Employer\JobPostController;
use App\Http\Controllers\Employer\ApplicationReviewController;

// Seeker controllers
use App\Http\Controllers\Seeker\BrowseJobsController;
use App\Http\Controllers\Seeker\ApplyController;

// Messaging
use App\Http\Controllers\MessageController;

// Admin
use App\Http\Controllers\Admin\UsersController;

// Welcome page
Route::get('/', function () {
    return view('welcome');
});

// Dashboard (available for all roles after login/verification)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile (all authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Must be logged in first
Route::middleware(['auth'])->group(function () {


    // Employer-only area

    Route::middleware([RoleMiddleware::class . ':employer,admin'])
        ->prefix('employer')->name('employer.')
        ->group(function () {
            Route::get('job-posts', [JobPostController::class, 'index'])->name('job_posts.index');
            Route::get('job-posts/create', [JobPostController::class, 'create'])->name('job_posts.create');
            Route::post('job-posts', [JobPostController::class, 'store'])->name('job_posts.store');
            Route::get('job-posts/{job}', [JobPostController::class, 'show'])->name('job_posts.show');
            Route::get('job-posts/{job}/edit', [JobPostController::class, 'edit'])->name('job_posts.edit');
            Route::put('job-posts/{job}', [JobPostController::class, 'update'])->name('job_posts.update');
            Route::delete('job-posts/{job}', [JobPostController::class, 'destroy'])->name('job_posts.destroy');

            // Applications for a job
            Route::get('job-posts/{job}/applications', [ApplicationReviewController::class, 'index'])->name('applications.index');
            Route::put('applications/{application}/status', [ApplicationReviewController::class, 'updateStatus'])->name('applications.updateStatus');
        });


    // Seeker-only area

    Route::middleware([RoleMiddleware::class . ':seeker,admin'])
        ->prefix('seeker')->name('seeker.')
        ->group(function () {
            Route::get('jobs', [BrowseJobsController::class, 'index'])->name('jobs.index');
            Route::get('jobs/{job}', [BrowseJobsController::class, 'show'])->name('jobs.show');
            Route::post('jobs/{job}/apply', [ApplyController::class, 'store'])->name('apply.store');
        });


    // Messaging (both employer & seeker involved)

    Route::post('jobs/{job}/messages', [MessageController::class, 'store'])->name('messages.store');


    // Admin-only area

    Route::middleware([RoleMiddleware::class . ':admin'])
        ->prefix('admin')->name('admin.')
        ->group(function () {
            Route::get('users', [UsersController::class, 'index'])->name('users.index');
        });
});

// Authentication routes (login, register, etc.)
require __DIR__ . '/auth.php';
