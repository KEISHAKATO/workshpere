<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Message;
use Illuminate\Http\Request;

class MyApplicationsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $apps = Application::with([
                'job:id,title,location_city,location_county,job_type,status,posted_at,currency,pay_min,pay_max,employer_id'
            ])
            ->where('seeker_id', $user->id)
            ->latest()
            ->paginate(12);

        // Unread messages addressed to the seeker, grouped by job
        $jobIds = $apps->pluck('job_id')->all();

        $unreadByJob = Message::query()
            ->whereNull('read_at')
            ->where('receiver_id', $user->id)
            ->whereIn('job_id', $jobIds)
            ->selectRaw('job_id, COUNT(*) as c')
            ->groupBy('job_id')
            ->pluck('c', 'job_id'); // [job_id => count]

        return view('seeker.applications.index', [
            'apps'        => $apps,
            'unreadByJob' => $unreadByJob,
        ]);
    }

    public function destroy(Request $request, Application $application)
    {
        abort_if($application->seeker_id !== $request->user()->id, 403);
        abort_if($application->status !== 'pending', 422, 'Only pending applications can be withdrawn.');

        $application->delete();

        return back()->with('status', 'Application withdrawn.');
    }
}
