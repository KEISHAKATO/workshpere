<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Profile;
use App\Services\Matcher;
use App\Services\Recommender;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function __construct(
        private Matcher $matcher,
        private Recommender $recommender, // ML scorer (python)
    ) {}

    public function __invoke(Request $request)
    {
        $user = $request->user();

        $seekerRecommendations = collect();
        $employerSuggestions   = collect();

        /*
        
        | Seeker: Recommended Jobs (ML first, fallback to heuristic)
        
        */
        if (method_exists($user, 'isSeeker') && ($user->isSeeker() || $user->isAdmin())) {
            // Try ML recommender
            try {
                $ml = $this->recommender->recommendJobsForUser($user, 8);
                if ($ml instanceof Collection && $ml->isNotEmpty()) {
                    $seekerRecommendations = $ml;
                }
            } catch (\Throwable $e) {
                // swallow & fallback to heuristic
            }

            // Fallback to your existing heuristic if ML returned nothing
            if ($seekerRecommendations->isEmpty()) {
                $profile = $user->profile;
                if ($profile && is_array($profile->skills)) {
                    $jobs = Job::where('status', 'open')
                        ->select(
                            'id','title','location_city','location_county','job_type',
                            'currency','pay_min','pay_max','required_skills','posted_at','employer_id'
                        )
                        ->latest('posted_at')
                        ->take(150)
                        ->get();

                    $seekerRecommendations = $jobs->map(function (Job $job) use ($profile) {
                        return [
                            'job'   => $job,
                            'score' => $this->matcher->scoreJobForSeeker($profile, $job),
                        ];
                    })->sortByDesc('score')->take(6)->values();
                }
            }
        }

        /*
        
        Employer: Suggested Candidates (ML first, fallback to heuristic)
        
        */
        if (method_exists($user, 'isEmployer') && ($user->isEmployer() || $user->isAdmin())) {
            // Prefer ML suggestions for the most recent open job
            try {
                $latestOpenJob = $user->jobs()
                    ->where('status', 'open')
                    ->latest('posted_at')
                    ->first();

                if ($latestOpenJob) {
                    $ml = $this->recommender->suggestProfilesForJob($latestOpenJob, 8);
                    if ($ml instanceof Collection && $ml->isNotEmpty()) {
                        $employerSuggestions = $ml;
                    }
                }
            } catch (\Throwable $e) {
                // swallow & fallback to heuristic
            }

            // Fallback heuristic across all open jobs if ML returned nothing
            if ($employerSuggestions->isEmpty()) {
                $openJobs = Job::where('employer_id', $user->id)
                    ->where('status', 'open')
                    ->select('id','title','location_county','job_type','required_skills')
                    ->get();

                if ($openJobs->isNotEmpty()) {
                    $seekerProfiles = Profile::with('user:id,name,email')
                        ->whereNotNull('skills')
                        ->select(
                            'id','user_id','skills','location_city',
                            'location_county','experience_years','preferred_job_type'
                        )
                        ->take(300)
                        ->get();

                    $ranked = $seekerProfiles->map(function (Profile $p) use ($openJobs) {
                        $best = $openJobs->map(
                            fn($j) => $this->matcher->scoreSeekerForJob($p, $j)
                        )->max() ?? 0;

                        return [
                            'profile'    => $p,
                            'best_score' => $best,
                        ];
                    })->sortByDesc('best_score')->take(8)->values();

                    $employerSuggestions = $ranked;
                }
            }
        }

        return view('dashboard', compact('seekerRecommendations', 'employerSuggestions'));
    }
}
