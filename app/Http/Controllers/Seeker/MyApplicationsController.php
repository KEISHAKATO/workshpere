<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MyApplicationsController extends Controller
{
    public function index(Request $request)
    {
        $apps = $request->user()
            ->applications()
            ->with(['job:id,title,location_city,location_county,job_type,status,posted_at,currency,pay_min,pay_max'])
            ->latest()
            ->paginate(12);

        return view('seeker.applications.index', compact('apps'));
    }

    public function destroy(Request $request, \App\Models\Application $application)
    {
        abort_if($application->seeker_id !== $request->user()->id, 403);
        abort_if($application->status !== 'pending', 422, 'Only pending applications can be withdrawn.');

        $application->delete();

        return back()->with('status', 'Application withdrawn.');
    }
}
