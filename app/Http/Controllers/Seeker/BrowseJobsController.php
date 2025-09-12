<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;

class BrowseJobsController extends Controller
{
    public function index(Request $request)
    {
        $jobs = Job::query()
            ->where('status', 'open')
            ->latest('posted_at')
            ->paginate(12);

        return view('seeker.jobs.index', [
            'jobs' => $jobs,
        ]);
    }

    public function show(Request $request, Job $job)
    {
        $user = $request->user();

        $isOwner    = $user && (int)$job->employer_id === (int)$user->id;
        $hasApplied = $user
            ? Application::where('job_id', $job->id)
                ->where('seeker_id', $user->id)
                ->exists()
            : false;

        return view('seeker.jobs.show', [
            'job'        => $job,
            'isOwner'    => $isOwner,
            'hasApplied' => $hasApplied,
        ]);
    }
}
