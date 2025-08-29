<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

class PublicJobsController extends Controller
{
    // GET /jobs
    public function index(Request $request)
    {
        $q = Job::query()->where('status', 'open');

        $request->validate([
            'search'   => ['nullable','string','max:120'],
            'county'   => ['nullable','string','max:120'],
            'job_type' => ['nullable','in:full_time,part_time,gig,contract'],
            'min_pay'  => ['nullable','integer','min:0'],
            'max_pay'  => ['nullable','integer','min:0'],
            'sort'     => ['nullable','in:newest,oldest,pay_high,pay_low'],
        ]);

        if ($s = $request->search) {
            $q->where(function ($qq) use ($s) {
                $qq->where('title', 'like', "%{$s}%")
                   ->orWhere('description', 'like', "%{$s}%")
                   ->orWhere('category', 'like', "%{$s}%")
                   ->orWhereJsonContains('required_skills', $s);
            });
        }
        if ($c = $request->county)   $q->where('location_county', 'like', "%{$c}%");
        if ($t = $request->job_type) $q->where('job_type', $t);

        if (($min = $request->min_pay) !== null) $q->where('pay_min', '>=', $min);
        if (($max = $request->max_pay) !== null) {
            $q->where(function($qq) use ($max){
                $qq->where('pay_max', '<=', $max)->orWhereNull('pay_max');
            });
        }

        switch ($request->sort) {
            case 'oldest':   $q->oldest('posted_at'); break;
            case 'pay_high': $q->orderByDesc('pay_max'); break;
            case 'pay_low':  $q->orderBy('pay_min'); break;
            default:         $q->latest('posted_at');
        }

        $jobs = $q->paginate(10)->withQueryString();

        return view('public.jobs.index', compact('jobs'));
    }

    // GET /jobs/{job}
    public function show(Job $job)
    {
        abort_if($job->status !== 'open', 404);
        return view('public.jobs.show', compact('job'));
    }
}
