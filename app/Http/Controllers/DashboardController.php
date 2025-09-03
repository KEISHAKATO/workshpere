<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Profile;
use App\Services\Matcher;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private Matcher $matcher) {}

    public function __invoke(Request $request)
    {
        $user = $request->user();

        // Shared quick stats 
        $seekerRecommendations = collect();
        $employerSuggestions   = collect();

        // Seeker: Recommended Jobs 
        if (method_exists($user, 'isSeeker') && ($user->isSeeker() || $user->isAdmin())) {
            $profile = $user->profile;
            if ($profile && is_array($profile->skills)) {
                $jobs = Job::where('status', 'open')
                    ->select('id','title','location_city','location_county','job_type','currency','pay_min','pay_max','required_skills','posted_at','employer_id')
                    ->latest('posted_at')
                    ->take(150) // safety limit
                    ->get();

                $seekerRecommendations = $jobs->map(function (Job $job) use ($profile) {
                    return [
                        'job'   => $job,
                        'score' => $this->matcher->scoreJobForSeeker($profile, $job),
                    ];
                })->sortByDesc('score')->take(6)->values();
            }
        }

        //  Employer: Suggested Candidates 
        if (method_exists($user, 'isEmployer') && ($user->isEmployer() || $user->isAdmin())) {
            $openJobs = Job::where('employer_id', $user->id)
                ->where('status', 'open')
                ->select('id','title','location_county','job_type','required_skills')
                ->get();

            if ($openJobs->isNotEmpty()) {
                // Load seeker profiles with skills
                $seekerProfiles = Profile::with('user:id,name,email')
                    ->whereNotNull('skills')
                    ->select('id','user_id','skills','location_city','location_county','experience_years','preferred_job_type')
                    ->take(300)
                    ->get();

                // For each profile compute the BEST score across the employer's open jobs
                $ranked = $seekerProfiles->map(function (Profile $p) use ($openJobs) {
                    $best = $openJobs->map(fn($j) => $this->matcher->scoreSeekerForJob($p, $j))
                                     ->max() ?? 0;
                    return [
                        'profile'    => $p,
                        'best_score' => $best,
                    ];
                })->sortByDesc('best_score')->take(8)->values();

                $employerSuggestions = $ranked;
            }
        }

        return view('dashboard', compact('seekerRecommendations','employerSuggestions'));
    }
}
