<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;

class MyApplicationsController extends Controller
{
    // GET /seeker/applications
    public function index(Request $request)
    {
        $apps = Application::with(['job:id,title,location_city,location_county,job_type,status,posted_at,currency,pay_min,pay_max'])
            ->where('seeker_id', $request->user()->id)
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('seeker.applications.index', compact('apps'));
    }

    // DELETE /seeker/applications/{application}  (Withdraw)
    public function destroy(Request $request, Application $application)
    {
        abort_unless($application->seeker_id === $request->user()->id, 403);

        // Allow withdraw only if still pending
        if ($application->status !== 'pending') {
            return back()->with('status', 'You can only withdraw pending applications.');
        }

        $application->delete();

        return back()->with('status', 'Application withdrawn.');
    }
}
