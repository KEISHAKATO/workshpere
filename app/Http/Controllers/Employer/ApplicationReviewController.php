<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;

class ApplicationReviewController extends Controller
{
    public function index(Job $job)
    {
        abort_unless($job->employer_id === auth()->id(), 403);
        $applications = $job->applications()->with('seeker.profile')->latest()->paginate(15);
        return view('employer.job_posts.applications', compact('job','applications'));
    }

    public function updateStatus(Request $request, Application $application)
    {
        $request->validate(['status' => 'required|in:pending,accepted,rejected']);
        $job = $application->job;
        abort_unless($job && $job->employer_id === auth()->id(), 403);

        $application->update(['status' => $request->status]);

        return back()->with('ok', 'Status updated to '.$request->status);
    }
}
