<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;

// Employer
use App\Http\Controllers\Employer\JobPostController;
use App\Http\Controllers\Employer\ApplicationReviewController;
use App\Http\Controllers\Employer\ProfileController as EmployerProfileController;

// Seeker
use App\Http\Controllers\Seeker\BrowseJobsController;
use App\Http\Controllers\Seeker\ApplyController;
use App\Http\Controllers\Seeker\ProfileController as SeekerProfileController;
use App\Http\Controllers\Seeker\MyApplicationsController;

// Public jobs
use App\Http\Controllers\PublicJobsController;

// Messaging
use App\Http\Controllers\MessageController;

// Admin
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\JobsController;
use App\Http\Controllers\Admin\ApplicationsController;

// App dashboard (invokable)
use App\Http\Controllers\DashboardController;

/*
| Public
*/
Route::get('/', fn () => view('welcome'));
Route::get('/jobs', [PublicJobsController::class, 'index'])->name('public.jobs.index');
Route::get('/jobs/{job}', [PublicJobsController::class, 'show'])->name('public.jobs.show');

/*
| Dashboard (EnsureUserIsActive is applied globally via Kernel's web group)
*/
Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
| Redirect generic /profile -> role-specific editor
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', function () {
        $u = auth()->user();
        if (method_exists($u, 'isEmployer') && ($u->isEmployer() || $u->isAdmin())) {
            return redirect()->route('employer.profile.edit');
        }
        return redirect()->route('seeker.profile.edit');
    })->name('profile');
});

/*
| Authenticated areas
*/
Route::middleware(['auth'])->group(function () {

    /*
    | Employer
    */
    Route::middleware([RoleMiddleware::class . ':employer,admin'])
        ->prefix('employer')->name('employer.')
        ->group(function () {
            // Profile
            Route::get('profile', [EmployerProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('profile', [EmployerProfileController::class, 'update'])->name('profile.update');

            // Job posts
            Route::get('job-posts', [JobPostController::class, 'index'])->name('job_posts.index');
            Route::get('job-posts/create', [JobPostController::class, 'create'])->name('job_posts.create');
            Route::post('job-posts', [JobPostController::class, 'store'])->name('job_posts.store');
            Route::get('job-posts/{job}', [JobPostController::class, 'show'])->name('job_posts.show');
            Route::get('job-posts/{job}/edit', [JobPostController::class, 'edit'])->name('job_posts.edit');
            Route::put('job-posts/{job}', [JobPostController::class, 'update'])->name('job_posts.update');
            Route::delete('job-posts/{job}', [JobPostController::class, 'destroy'])->name('job_posts.destroy');

            // Applications
            Route::get('job-posts/{job}/applications', [ApplicationReviewController::class, 'index'])->name('applications.index');
            Route::get('applications/{application}', [ApplicationReviewController::class, 'show'])->name('applications.show');
            Route::put('applications/{application}/status', [ApplicationReviewController::class, 'updateStatus'])->name('applications.updateStatus');
        });

    /*
    | Seeker
    */
    Route::middleware([RoleMiddleware::class . ':seeker,admin'])
        ->prefix('seeker')->name('seeker.')
        ->group(function () {
            // Profile
            Route::get('profile', [SeekerProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('profile', [SeekerProfileController::class, 'update'])->name('profile.update');

            // Jobs
            Route::get('jobs', [BrowseJobsController::class, 'index'])->name('jobs.index');
            Route::get('jobs/{job}', [BrowseJobsController::class, 'show'])->name('jobs.show');
            Route::post('jobs/{job}/apply', [ApplyController::class, 'store'])->name('apply.store');

            // My Applications
            Route::get('applications', [MyApplicationsController::class, 'index'])->name('applications.index');
            Route::delete('applications/{application}', [MyApplicationsController::class, 'destroy'])->name('applications.destroy');
        });

    /*
    | Messaging (both roles)
    */
    Route::get('/jobs/{job}/chat', [MessageController::class, 'index'])->name('chat.show');
    Route::get('/jobs/{job}/messages', [MessageController::class, 'fetch'])->name('chat.fetch');
    Route::post('/jobs/{job}/messages', [MessageController::class, 'store'])->name('chat.store');

    /*
    | Admin
    */
    Route::middleware([RoleMiddleware::class . ':admin'])
        ->prefix('admin')->name('admin.')
        ->group(function () {
            // Dashboard
            Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

            // Users
            Route::get('users', [UsersController::class, 'index'])->name('users.index');
            Route::put('users/{user}/toggle-active', [UsersController::class, 'toggleActive'])->name('users.toggleActive');
            Route::put('users/{user}/toggle-flag',   [UsersController::class, 'toggleFlag'])->name('users.toggleFlag');

            // Jobs
            Route::get('jobs', [JobsController::class, 'index'])->name('jobs.index');
            Route::put('jobs/{job}/toggle-flag', [JobsController::class, 'toggleFlag'])->name('jobs.toggleFlag');
            Route::put('jobs/{job}/status',      [JobsController::class, 'setStatus'])->name('jobs.setStatus');

            // Applications
            Route::get('applications', [ApplicationsController::class, 'index'])->name('applications.index');
            Route::put('applications/{application}/status', [ApplicationsController::class, 'updateStatus'])
                ->name('applications.updateStatus');
        });
});

require __DIR__ . '/auth.php';
