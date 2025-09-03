<?php

namespace App\Services;

use App\Models\Job;
use App\Models\Profile;

class Matcher
{
    /**
     * Score how well a job matches a seeker profile.
     * Returns float 0..100.
     */
    public function scoreJobForSeeker(Profile $seekerProfile, Job $job): float
    {
        $seekerSkills = $this->normalizeSkills($seekerProfile->skills ?? []);
        $jobSkills    = $this->normalizeSkills($job->required_skills ?? []);

        $skillScore = $this->skillOverlapScore($seekerSkills, $jobSkills); // 0..80
        $locScore   = $this->locationBonus($seekerProfile->location_county, $job->location_county); // 0 or 15
        $typeScore  = $this->jobTypeBonus($seekerProfile->preferred_job_type, $job->job_type); // 0 or 5

        return round(min(100, $skillScore + $locScore + $typeScore), 1);
    }

    /**
     * Score how well a seeker profile matches a job 
     */
    public function scoreSeekerForJob(Profile $seekerProfile, Job $job): float
    {
        return $this->scoreJobForSeeker($seekerProfile, $job);
    }

    // --- helpers 

    private function normalizeSkills(array $skills): array
    {
        return collect($skills)
            ->filter(fn($s) => is_string($s) && trim($s) !== '')
            ->map(fn($s) => mb_strtolower(trim($s)))
            ->unique()
            ->values()
            ->all();
    }

    /**
     * 0..80 based on overlap ratio.
     * If job requires 5 and seeker has 3 of them => overlap 3/5 => 60% => 48 points.
     * If job has no required skills -> neutral 40 points (so location/type can still lift it).
     */
    private function skillOverlapScore(array $seekerSkills, array $jobSkills): float
    {
        if (count($jobSkills) === 0) {
            return 40.0;
        }
        $overlap = count(array_values(array_intersect($jobSkills, $seekerSkills)));
        $ratio   = $overlap / max(1, count($jobSkills));
        return round($ratio * 80, 1);
    }

    private function locationBonus(?string $seekerCounty, ?string $jobCounty): float
    {
        if (!$seekerCounty || !$jobCounty) return 0.0;
        return mb_strtolower(trim($seekerCounty)) === mb_strtolower(trim($jobCounty)) ? 15.0 : 0.0;
    }

    private function jobTypeBonus(?string $wanted, ?string $actual): float
    {
        if (!$wanted || !$actual) return 0.0;
        return mb_strtolower(trim($wanted)) === mb_strtolower(trim($actual)) ? 5.0 : 0.0;
    }
}
