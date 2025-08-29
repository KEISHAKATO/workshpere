<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;

class BrowseJobsController extends Controller
{
    // logged-in seeker browse
    public function index(Request $request)
    {
        $query = Job::query()->where('status','open');

        if ($search = $request->string('q')->toString()) {
            $query->where(function($q) use ($search) {
                $q->where('title','like',"%$search%")
                  ->orWhere('description','like',"%$search%")
                  ->orWhere('category','like',"%$search%");
            });
        }

        if ($county = $request->string('county')->toString()) {
            $query->where('location_county', $county);
        }

        if ($skills = $request->string('skills')->toString()) {
            // comma-separated -> look for any of them in required_skills JSON
            $tags = collect(explode(',', $skills))->map(fn($s)=>trim($s))->filter()->all();
            foreach ($tags as $tag) {
                $query->whereJsonContains('required_skills', $tag);
            }
        }

        $jobs = $query->latest('created_at')->paginate(10)->withQueryString();

        return view('seeker.jobs.index', compact('jobs'));
    }

    // logged-in seeker detail
    public function show(Job $job)
    {
        abort_if($job->status !== 'open', 404);
        return view('seeker.jobs.show', compact('job'));
    }

    // public job detail (no auth)
    public function publicShow(Job $job)
    {
        abort_if($job->status !== 'open', 404);
        return view('public.jobs.show', compact('job'));
    }
}
