<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;

// Employer controllers
use App\Http\Controllers\Employer\JobPostController;
use App\Http\Controllers\Employer\ApplicationReviewController;
use App\Http\Controllers\Employer\ProfileController as EmployerProfileController;

// Seeker controllers
use App\Http\Controllers\Seeker\BrowseJobsController;
use App\Http\Controllers\Seeker\ApplyController;
use App\Http\Controllers\Seeker\ProfileController as SeekerProfileController;

// Messaging
use App\Http\Controllers\MessageController;

// Admin
use App\Http\Controllers\Admin\UsersController;


// Public / Auth basics
use App\Http\Controllers\PublicJobsController;


// PUBLIC job detail (no login required)
Route::get('/jobs', [PublicJobsController::class, 'index'])->name('public.jobs.index');
Route::get('/jobs/{job}', [PublicJobsController::class, 'show'])->name('public.jobs.show');
// Welcome page
Route::get('/', function () {
    return view('welcome');
});

// Dashboard (available for all roles after login/verification)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Redirect generic /profile to role-specific editor
Route::middleware('auth')->group(function () {
    Route::get('/profile', function () {
        $u = auth()->user();
        if (method_exists($u, 'isEmployer') && ($u->isEmployer() || $u->isAdmin())) {
            return redirect()->route('employer.profile.edit');
        }
        return redirect()->route('seeker.profile.edit');
    })->name('profile'); // keeps a familiar name if you link to "Account Settings"
});


// Authenticated areas

Route::middleware(['auth'])->group(function () {


    // Employer-only area

    Route::middleware([RoleMiddleware::class . ':employer,admin'])
        ->prefix('employer')->name('employer.')
        ->group(function () {
            // Profile
            Route::get('profile', [EmployerProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('profile', [EmployerProfileController::class, 'update'])->name('profile.update');

            // Job posts CRUD
            Route::get('job-posts', [JobPostController::class, 'index'])->name('job_posts.index');
            Route::get('job-posts/create', [JobPostController::class, 'create'])->name('job_posts.create');
            Route::post('job-posts', [JobPostController::class, 'store'])->name('job_posts.store');
            Route::get('job-posts/{job}', [JobPostController::class, 'show'])->name('job_posts.show');
            Route::get('job-posts/{job}/edit', [JobPostController::class, 'edit'])->name('job_posts.edit');
            Route::put('job-posts/{job}', [JobPostController::class, 'update'])->name('job_posts.update');
            Route::delete('job-posts/{job}', [JobPostController::class, 'destroy'])->name('job_posts.destroy');

            // Applications management
            Route::get('job-posts/{job}/applications', [ApplicationReviewController::class, 'index'])->name('applications.index');
            Route::put('applications/{application}/status', [ApplicationReviewController::class, 'updateStatus'])->name('applications.updateStatus');
        });


    // Seeker-only area

    Route::middleware([RoleMiddleware::class . ':seeker,admin'])
        ->prefix('seeker')->name('seeker.')
        ->group(function () {
            // Profile
            Route::get('profile', [SeekerProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('profile', [SeekerProfileController::class, 'update'])->name('profile.update');

            // Browse/apply jobs
            Route::get('jobs', [BrowseJobsController::class, 'index'])->name('jobs.index');
            Route::get('jobs/{job}', [BrowseJobsController::class, 'show'])->name('jobs.show');
            Route::post('jobs/{job}/apply', [ApplyController::class, 'store'])->name('apply.store');

            // My Applications
            Route::get('applications', [\App\Http\Controllers\Seeker\MyApplicationsController::class, 'index'])
                ->name('applications.index');

            Route::delete('applications/{application}', [\App\Http\Controllers\Seeker\MyApplicationsController::class, 'destroy'])
                ->name('applications.destroy');

                    });


    // Messaging (both roles)

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
