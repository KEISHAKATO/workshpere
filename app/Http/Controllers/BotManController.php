<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BotManController extends Controller
{
    public function handle(Request $request)
    {
        $botman = app('botman');

        // Load BotMan "hears" routes (clean separation)
        if (file_exists(base_path('routes/botman.php'))) {
            require base_path('routes/botman.php');
        }

        $botman->listen();
    }
}
