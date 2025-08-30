<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;

class ApplicationReviewController extends Controller
{
    // GET /employer/job-posts/{job}/applications
    public function index(Request $request, Job $job)
    {
        $user = $request->user();

        // Only the owning employer (or admin) can view
        if (!($user->isAdmin() || $job->employer_id === $user->id)) {
            abort(403);
        }

        $applications = Application::with(['seeker.profile'])
            ->where('job_id', $job->id)
            ->latest()
            ->paginate(15);

        return view('employer.applications.index', compact('job', 'applications'));
    }

    // PUT /employer/applications/{application}/status
    public function updateStatus(Request $request, Application $application)
    {
        $user = $request->user();

        // Ensure the application belongs to a job owned by this employer (or admin)
        if (!($user->isAdmin() || $application->job->employer_id === $user->id)) {
            abort(403);
        }

        $data = $request->validate([
            'status' => ['required', 'in:pending,accepted,rejected'],
        ]);

        $application->update(['status' => $data['status']]);

        return back()->with('ok', 'Application status updated.');
    }
    public function show(\App\Models\Application $application)
    {
        // Security: only the employer who owns the job can view
        abort_unless($application->job && $application->job->employer_id === auth()->id(), 403);

        // Eager load seeker + profile + job for the view
        $application->load([
            'job',
            'seeker.profile',
        ]);

        $seeker = $application->seeker;
        $profile = $seeker?->profile;

        return view('employer.applications.show', compact('application', 'seeker', 'profile'));
    }

}
