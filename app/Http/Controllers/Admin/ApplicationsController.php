<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ApplicationsController extends Controller
{
    public function index(Request $request)
    {
        $q = Application::query()
            ->with(['job:id,title,employer_id','seeker:id,name,email'])
            ->when($request->filled('status'), fn($x)=>$x->where('status',$request->status))
            ->orderByDesc('created_at');

        return view('admin.applications.index', [
            'apps' => $q->paginate(20),
            'filters' => [
                'status' => $request->status,
            ]
        ]);
    }

    public function updateStatus(Request $request, Application $application)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['pending','accepted','rejected'])],
        ]);

        $application->status = $data['status'];
        $application->save();

        return back()->with('status', "Application #{$application->id} set to {$application->status}.");
    }
}
