<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $userName;

    public function __construct(string $userName)
    {
        $this->userName = $userName;
    }

    public function build()
    {
        return $this->subject('Welcome to Workshpere 🎉')
            ->markdown('emails.welcome', [
                'userName' => $this->userName,
            ]);
    }
}
