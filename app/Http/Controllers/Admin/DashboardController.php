<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Job;
use App\Models\Application;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'userCount'  => User::count(),
            'jobCount'   => Job::count(),
            'appCount'   => Application::count(),
            'flaggedUsers' => User::where('is_flagged', true)->count(),
            'flaggedJobs'  => Job::where('is_flagged', true)->count(),
            'openJobs'     => Job::where('status', 'open')->count(),
            'pendingApps'  => Application::where('status', 'pending')->count(),
        ]);
    }
}
