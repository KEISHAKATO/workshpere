<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\Application;
use App\Models\Message;   
use Illuminate\Http\Request;

class ApplicationReviewController extends Controller
{
    public function index(Request $request, Job $job)
    {
        // Only the employer who owns this job (or admin via middleware) can view
        abort_if($request->user()->id !== (int) $job->employer_id, 403);

        // List applications with seeker relation
        $applications = Application::with(['seeker:id,name,email'])
            ->where('job_id', $job->id)
            ->latest()
            ->paginate(15)
            ->withQueryString();


        // Unread messages for each seeker thread (employer is the receiver)
        $unreadBySeeker = Message::query()
            ->selectRaw('sender_id, COUNT(*) as c')
            ->where('job_id', $job->id)
            ->whereNull('read_at')
            ->where('receiver_id', $request->user()->id)
            ->groupBy('sender_id')
            ->pluck('c', 'sender_id'); // [seeker_id => count]

        return view('employer.applications.index', [
            'job'            => $job,
            'applications'   => $applications,
            'unreadBySeeker' => $unreadBySeeker,
        ]);
    }

    public function show(Request $request, Application $application)
    {
        // Ensure the logged-in employer owns the job for this application
        abort_if($request->user()->id !== (int) $application->job->employer_id, 403);

        $seeker  = $application->seeker()->first();      // name, email
        $profile = optional($seeker)->profile;            // can be null

        return view('employer.applications.show', compact('application', 'seeker', 'profile'));
    }

    public function updateStatus(Request $request, Application $application)
    {
        // Ensure the logged-in employer owns the job for this application
        abort_if($request->user()->id !== (int) $application->job->employer_id, 403);

        $data = $request->validate([
            'status' => 'required|in:pending,accepted,rejected',
        ]);

        $application->update(['status' => $data['status']]);

        return back()->with('status', 'Application status updated.');
    }
}
