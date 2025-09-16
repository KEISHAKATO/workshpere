<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;

class SendWelcomeEmail
{
    public function handle(Verified $event): void
    {
        $user = $event->user;
        // Avoid resending if already verified before (Optional safety)
        if ($user && $user->email_verified_at) {
            Mail::to($user->email)->send(new WelcomeMail($user->name));
        }
    }
}
