<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JobsController extends Controller
{
    public function index(Request $request)
    {
        $q = Job::query()
            ->with('employer:id,name,email')
            ->when($request->filled('status'), fn($x)=>$x->where('status',$request->status))
            ->when($request->boolean('flagged'), fn($x)=>$x->where('is_flagged',true))
            ->orderByDesc('created_at');

        return view('admin.jobs.index', [
            'jobs' => $q->paginate(20),
            'filters' => [
                'status' => $request->status,
                'flagged' => $request->boolean('flagged'),
            ]
        ]);
    }

    public function toggleFlag(Job $job)
    {
        $job->is_flagged = ! $job->is_flagged;
        $job->save();

        return back()->with('status', "Job #{$job->id} flag set to " . ($job->is_flagged ? 'FLAGGED' : 'ok') . '.');
    }

    public function setStatus(Request $request, Job $job)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['open','closed'])],
        ]);

        $job->status = $data['status'];
        $job->save();

        return back()->with('status', "Job #{$job->id} status set to {$job->status}.");
    }
}
