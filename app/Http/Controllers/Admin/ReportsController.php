<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function data(Request $request)
    {
        // KPIs
        $totalApps    = Application::count();
        $acceptedApps = Application::where('status', 'accepted')->count();
        $pendingApps  = Application::where('status', 'pending')->count();
        $acceptance   = $totalApps > 0 ? round(($acceptedApps / $totalApps) * 100, 1) : 0;

        // Jobs by category
        $jobsByCategory = Job::query()
            ->select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        // Jobs by county (top 12)
        $jobsByCounty = Job::query()
            ->select('location_county as county', DB::raw('count(*) as total'))
            ->whereNotNull('location_county')
            ->groupBy('location_county')
            ->orderByDesc('total')
            ->limit(12)
            ->get();

        // Applications by status
        $appsByStatus = Application::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        // Skills demand (from Job.required_skills array)
        $demandCounts = [];
        Job::whereNotNull('required_skills')->chunk(500, function ($chunk) use (&$demandCounts) {
            foreach ($chunk as $job) {
                $skills = is_array($job->required_skills) ? $job->required_skills : [];
                foreach ($skills as $s) {
                    $s = trim((string)$s);
                    if ($s === '') continue;
                    $demandCounts[$s] = ($demandCounts[$s] ?? 0) + 1;
                }
            }
        });

        // Skills supply (from Profile.skills array)
        $supplyCounts = [];
        Profile::whereNotNull('skills')->chunk(500, function ($chunk) use (&$supplyCounts) {
            foreach ($chunk as $p) {
                $skills = is_array($p->skills) ? $p->skills : [];
                foreach ($skills as $s) {
                    $s = trim((string)$s);
                    if ($s === '') continue;
                    $supplyCounts[$s] = ($supplyCounts[$s] ?? 0) + 1;
                }
            }
        });

        // Pick top 12 skills by demand; align supply to those skills
        arsort($demandCounts);
        $topSkills = array_slice(array_keys($demandCounts), 0, 12);
        $demandData = array_map(fn($k) => $demandCounts[$k] ?? 0, $topSkills);
        $supplyData = array_map(fn($k) => $supplyCounts[$k] ?? 0, $topSkills);

        return response()->json([
            'kpis' => [
                'acceptanceRate' => $acceptance,
                'accepted'       => $acceptedApps,
                'pending'        => $pendingApps,
            ],
            'charts' => [
                'jobsByCategory' => [
                    'labels' => $jobsByCategory->pluck('category')->map(fn($v) => $v ?: 'Uncategorized'),
                    'data'   => $jobsByCategory->pluck('total'),
                ],
                'jobsByCounty' => [
                    'labels' => $jobsByCounty->pluck('county')->map(fn($v) => $v ?: '—'),
                    'data'   => $jobsByCounty->pluck('total'),
                ],
                'appsByStatus' => [
                    'labels' => $appsByStatus->pluck('status')->map(fn($v) => ucfirst($v ?: 'unknown')),
                    'data'   => $appsByStatus->pluck('total'),
                ],
                'skillsDemandVsSupply' => [
                    'labels' => $topSkills,
                    'demand' => $demandData,
                    'supply' => $supplyData,
                ],
            ],
        ]);
    }
}
