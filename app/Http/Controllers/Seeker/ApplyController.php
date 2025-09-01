<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Application;
use Illuminate\Http\Request;

class ApplyController extends Controller
{
    public function store(Request $request, Job $job)
    {
        abort_if($job->status !== 'open', 404);

        $data = $request->validate([
            'cover_letter' => 'nullable|string|max:5000',
        ]);

        $user = $request->user();

        if (!($user->isSeeker() || $user->isAdmin())) {
            abort(403);
        }

        Application::firstOrCreate(
            ['job_id' => $job->id, 'seeker_id' => $user->id],
            ['cover_letter' => $data['cover_letter'] ?? null, 'status' => 'pending']
        );

        return back()->with('status', 'Application submitted.');
    }
}
