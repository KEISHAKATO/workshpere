<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;

class JobPostController extends Controller
{
    public function index()
    {
        $jobs = Job::where('employer_id', auth()->id())->latest()->paginate(10);
        return view('employer.job_posts.index', compact('jobs'));
    }

    public function create()
    {
        return view('employer.job_posts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'           => 'required|string|max:160',
            'description'     => 'required|string',
            'category'        => 'nullable|string|max:80',
            'job_type'        => 'required|in:full_time,part_time,gig,contract',
            'pay_min'         => 'nullable|integer|min:0',
            'pay_max'         => 'nullable|integer|min:0',
            'currency'        => 'required|string|size:3',
            'location_city'   => 'nullable|string|max:120',
            'location_county' => 'nullable|string|max:120',
            'required_skills' => 'nullable|array',
            'required_skills.*' => 'string|max:60',
        ]);

        $data['employer_id'] = auth()->id();
        $data['status'] = 'open';

        // ensure table name is job_posts via model
        $job = Job::create($data);

        return redirect()->route('employer.job_posts.show', $job)->with('ok', 'Job created.');
    }

    public function show(Job $job)
    {
        abort_unless($job->employer_id === auth()->id(), 403);
        $job->load(['applications.seeker', 'messages', 'reviews']);
        return view('employer.job_posts.show', compact('job'));
    }

    public function edit(Job $job)
    {
        abort_unless($job->employer_id === auth()->id(), 403);
        return view('employer.job_posts.edit', compact('job'));
    }

    public function update(Request $request, Job $job)
    {
        abort_unless($job->employer_id === auth()->id(), 403);

        $data = $request->validate([
            'title'           => 'required|string|max:160',
            'description'     => 'required|string',
            'category'        => 'nullable|string|max:80',
            'job_type'        => 'required|in:full_time,part_time,gig,contract',
            'pay_min'         => 'nullable|integer|min:0',
            'pay_max'         => 'nullable|integer|min:0',
            'currency'        => 'required|string|size:3',
            'location_city'   => 'nullable|string|max:120',
            'location_county' => 'nullable|string|max:120',
            'status'          => 'required|in:open,closed,paused',
            'required_skills' => 'nullable|array',
            'required_skills.*' => 'string|max:60',
        ]);

        $job->update($data);

        return redirect()->route('employer.job_posts.show', $job)->with('ok', 'Job updated.');
    }

    public function destroy(Job $job)
    {
        abort_unless($job->employer_id === auth()->id(), 403);
        $job->delete();
        return redirect()->route('employer.job_posts.index')->with('ok', 'Job deleted.');
    }
}
