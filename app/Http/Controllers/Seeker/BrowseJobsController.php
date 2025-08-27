<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;

class BrowseJobsController extends Controller
{
    public function index(Request $request)
    {
        $q = Job::query()->where('status','open');

        if ($s = $request->get('q')) {
            $q->where(function($qq) use ($s) {
                $qq->where('title','like',"%$s%")
                   ->orWhere('description','like',"%$s%")
                   ->orWhere('category','like',"%$s%");
            });
        }

        if ($county = $request->get('county')) $q->where('location_county',$county);
        if ($type = $request->get('type'))     $q->where('job_type',$type);

        $jobs = $q->latest('posted_at')->paginate(12)->withQueryString();

        return view('seeker.jobs.index', compact('jobs'));
    }

    public function show(Job $job)
    {
        abort_if($job->status !== 'open', 404);
        return view('seeker.jobs.show', compact('job'));
    }
}
