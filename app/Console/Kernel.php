<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Register Artisan commands.
     * @var array<class-string>
     */
    protected $commands = [
        \App\Console\Commands\ExportMlDataset::class,
    ];
}
