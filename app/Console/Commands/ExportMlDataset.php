<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Application;

class ExportMlDataset extends Command
{
    protected $signature = 'ml:export';
    protected $description = 'Export applications dataset for ML to storage/app/*/ml/dataset.csv';

    public function handle(): int
    {
        $disk = Storage::disk('local'); // In your app, this points to storage/app/private
        $disk->makeDirectory('ml');

        $apps = Application::with(['job', 'seeker.profile'])
            ->whereIn('status', ['accepted', 'rejected'])
            ->get();

        $stream = fopen('php://temp', 'w+');

        fputcsv($stream, [
            'seeker_id',
            'experience_years',
            'skills',
            'location_county',
            'job_id',
            'job_category',
            'job_type',
            'job_required_skills',
            'label',
        ]);

        foreach ($apps as $app) {
            $profile  = optional($app->seeker)->profile;
            $job      = $app->job;

            $skillsSeeker = is_array($profile?->skills) ? implode(',', $profile->skills) : (string)($profile->skills ?? '');
            $skillsJob    = is_array($job?->required_skills) ? implode(',', $job->required_skills) : (string)($job->required_skills ?? '');
            $label        = $app->status === 'accepted' ? 1 : 0;

            fputcsv($stream, [
                $app->seeker_id,
                $profile->experience_years ?? 0,
                $skillsSeeker,
                $profile->location_county ?? '',
                $app->job_id,
                $job->category ?? '',
                $job->job_type ?? '',
                $skillsJob,
                $label,
            ]);
        }

        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);

        $relative = 'ml/dataset.csv';
        $disk->put($relative, $csv);

        // This prints the absolute path for the disk you’re using
        $absolute = method_exists($disk, 'path') ? $disk->path($relative) : storage_path("app/{$relative}");
        $this->info("Exported {$apps->count()} decided applications to {$absolute}");

        return self::SUCCESS;
    }
}
