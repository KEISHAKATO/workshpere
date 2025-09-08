<?php

namespace App\Services;

use App\Models\Job;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Collection;
use Symfony\Component\Process\Process;

class Recommender
{
    public function __construct(
        private string $python = 'python3',
        private string $script = 'ml/score_jobs.py',
        private string $model  = 'ml/model.joblib',
    ) {}

    /**
     * Recommend jobs for a seeker (current user).
     * Returns collection of ['job' => Job, 'score' => int%]
     */
    public function recommendJobsForUser(User $user, int $limit = 8): Collection
    {
        $profile = $user->profile;
        if (!$profile) return collect();

        // collect a small set of open jobs
        $jobs = Job::where('status', 'open')
            ->orderByDesc('posted_at')
            ->limit(60)
            ->get(['id','title','location_city','location_county','job_type','required_skills']);

        if ($jobs->isEmpty()) return collect();

        $seekerPayload = [
            'id'               => $user->id,
            'experience_years' => (int)($profile->experience_years ?? 0),
            'skills'           => is_array($profile->skills) ? $profile->skills : [],
            'location_county'  => (string)($profile->location_county ?? ''),
        ];

        $jobsPayload = $jobs->map(function(Job $j){
            return [
                'id'               => $j->id,
                'title'            => $j->title,
                'location_county'  => (string)($j->location_county ?? ''),
                'job_type'         => (string)($j->job_type ?? ''),
                'required_skills'  => is_array($j->required_skills) ? $j->required_skills : [],
            ];
        })->values()->all();

        $scores = $this->runPython([
            '--mode', 'seeker',
            '--model', $this->model,
            '--seeker-json', json_encode($seekerPayload),
            '--jobs-json', json_encode($jobsPayload),
            '--topk', (string)$limit,
        ]);

        // map to Job models
        $byId = $jobs->keyBy('id');
        return collect($scores)
            ->map(function ($row) use ($byId) {
                $job = $byId->get($row['job_id']);
                if (!$job) return null;
                return [
                    'job'   => $job,
                    'score' => (int) round($row['score'] * 100),
                ];
            })
            ->filter()
            ->values();
    }

    /**
     * Suggest candidate profiles for one employer job.
     * Returns collection of ['profile' => Profile, 'best_score' => int%]
     */
    public function suggestProfilesForJob(Job $job, int $limit = 8): Collection
    {
        // simple pool: recent profiles
        $profiles = Profile::with('user')
            ->orderByDesc('updated_at')
            ->limit(80)
            ->get(['id','user_id','skills','experience_years','location_city','location_county']);

        if ($profiles->isEmpty()) return collect();

        $jobPayload = [
            'id'               => $job->id,
            'location_county'  => (string)($job->location_county ?? ''),
            'job_type'         => (string)($job->job_type ?? ''),
            'required_skills'  => is_array($job->required_skills) ? $job->required_skills : [],
        ];

        $seekersPayload = $profiles->map(function(Profile $p){
            return [
                'id'               => $p->user_id,
                'experience_years' => (int)($p->experience_years ?? 0),
                'skills'           => is_array($p->skills) ? $p->skills : [],
                'location_county'  => (string)($p->location_county ?? ''),
            ];
        })->values()->all();

        $scores = $this->runPython([
            '--mode', 'job',
            '--model', $this->model,
            '--job-json', json_encode($jobPayload),
            '--seekers-json', json_encode($seekersPayload),
            '--topk', (string)$limit,
        ]);

        // merge scores back onto profiles
        $byUserId = $profiles->keyBy('user_id');
        return collect($scores)
            ->map(function ($row) use ($byUserId) {
                $p = $byUserId->get($row['user_id']);
                if (!$p) return null;
                return [
                    'profile'    => $p,
                    'best_score' => (int) round($row['score'] * 100),
                ];
            })
            ->filter()
            ->values();
    }

    /**
     * Run Python scorer and decode JSON output.
     */
    private function runPython(array $args): array
    {
        $cmd = array_merge([$this->python, base_path($this->script)], $args);
        $process = new Process($cmd, base_path());
        $process->setTimeout(20);
        $process->run();

        if (!$process->isSuccessful()) {
            // fail soft: return empty
            // You can log: \Log::warning('ML scorer failed', ['err' => $process->getErrorOutput()]);
            return [];
        }

        $out = trim($process->getOutput());
        if ($out === '') return [];
        try {
            $decoded = json_decode($out, true, 512, JSON_THROW_ON_ERROR);
            return is_array($decoded) ? $decoded : [];
        } catch (\Throwable $e) {
            // \Log::warning('ML scorer bad JSON', ['out' => $out]);
            return [];
        }
    }
}
