<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;

class JobPostController extends Controller
{
    public function index(Request $request)
    {
        $jobs = Job::where('employer_id', $request->user()->id)
            ->latest('created_at')
            ->paginate(10);

        return view('employer.job_posts.index', compact('jobs'));
    }

    public function create()
    {
        return view('employer.job_posts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'            => ['required','string','max:160'],
            'description'      => ['required','string'],
            'category'         => ['nullable','string','max:80'],
            'job_type'         => ['required','in:full_time,part_time,gig,contract'],
            'pay_min'          => ['nullable','integer','min:0'],
            'pay_max'          => ['nullable','integer','min:0'],
            'currency'         => ['required','string','size:3'],
            'location_city'    => ['nullable','string','max:120'],
            'location_county'  => ['nullable','string','max:120'],
            'lat'              => ['nullable','numeric'],
            'lng'              => ['nullable','numeric'],
            // skills: comma list from form (optional); accept array too
            'required_skills'  => ['nullable'],
            'status'           => ['nullable','in:open,closed,paused'],
        ]);

        // normalize skills -> array
        if (isset($data['required_skills']) && is_string($data['required_skills'])) {
            $data['required_skills'] = collect(explode(',', $data['required_skills']))
                ->map(fn($s)=>trim($s))
                ->filter()
                ->values()
                ->all();
        }

        $data['employer_id'] = $request->user()->id;
        $data['status']      = $data['status'] ?? 'open';

        $job = Job::create($data);

        return redirect()
            ->route('employer.job_posts.show', $job)
            ->with('status', 'Job created.');
    }

    public function show(Job $job, Request $request)
    {
        $this->ensureOwner($job, $request);
        return view('employer.job_posts.show', compact('job'));
    }

    public function edit(Job $job, Request $request)
    {
        $this->ensureOwner($job, $request);
        return view('employer.job_posts.edit', compact('job'));
    }

    public function update(Job $job, Request $request)
    {
        $this->ensureOwner($job, $request);

        $data = $request->validate([
            'title'            => ['required','string','max:160'],
            'description'      => ['required','string'],
            'category'         => ['nullable','string','max:80'],
            'job_type'         => ['required','in:full_time,part_time,gig,contract'],
            'pay_min'          => ['nullable','integer','min:0'],
            'pay_max'          => ['nullable','integer','min:0'],
            'currency'         => ['required','string','size:3'],
            'location_city'    => ['nullable','string','max:120'],
            'location_county'  => ['nullable','string','max:120'],
            'lat'              => ['nullable','numeric'],
            'lng'              => ['nullable','numeric'],
            'required_skills'  => ['nullable'],
            'status'           => ['required','in:open,closed,paused'],
        ]);

        if (isset($data['required_skills']) && is_string($data['required_skills'])) {
            $data['required_skills'] = collect(explode(',', $data['required_skills']))
                ->map(fn($s)=>trim($s))
                ->filter()
                ->values()
                ->all();
        }

        $job->update($data);

        return redirect()
            ->route('employer.job_posts.show', $job)
            ->with('status', 'Job updated.');
    }

    public function destroy(Job $job, Request $request)
    {
        $this->ensureOwner($job, $request);
        $job->delete();

        return redirect()
            ->route('employer.job_posts.index')
            ->with('status', 'Job deleted.');
    }

    private function ensureOwner(Job $job, Request $request): void
    {
        if (!($request->user()->isAdmin() || $request->user()->id === $job->employer_id)) {
            abort(403);
        }
    }
}
