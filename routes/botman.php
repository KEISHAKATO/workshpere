<?php

use BotMan\BotMan\BotMan;
use App\Conversations\SupportConversation;

/** @var BotMan $botman */
$botman = app('botman');

// Greetings / help
$botman->hears('^(hi|hello|hey)$', function (BotMan $bot) {
    $bot->reply("Hi! I'm your Worksphere assistant 🤖");
    $bot->startConversation(new SupportConversation());
});

$botman->hears('^help|menu|support$', function (BotMan $bot) {
    $bot->startConversation(new SupportConversation());
});

// Navigation shortcuts
$botman->hears('register|sign ?up', fn($bot) => $bot->reply('Go to /register and create your account. Verify email if prompted, then log in at /login.'));
$botman->hears('login|log ?in', fn($bot) => $bot->reply('Use /login (Forgot your password? link is there too).'));
$botman->hears('browse jobs|find jobs|jobs', fn($bot) => $bot->reply('Browse jobs here: /jobs or Seeker → Browse Jobs.'));
$botman->hears('apply', fn($bot) => $bot->reply("Open a job page and hit **Apply**. Then fill the short form and submit. Track it in **My Applications**."));
$botman->hears('post job|create job', fn($bot) => $bot->reply('Employers: use **Post a Job** in the navbar. Make sure your role is employer.'));
$botman->hears('profile', fn($bot) => $bot->reply('Seeker → My Profile or Employer → Company Profile to edit details and location.'));
$botman->hears('password|reset', fn($bot) => $bot->reply('On /login click **Forgot your password?**. Then check your email.'));
$botman->hears('reports?|analytics', fn($bot) => $bot->reply('Admin Reports: /admin/reports (KPIs, jobs, applications, skills).'));

// Fallback
$botman->fallback(function(BotMan $bot) {
    $bot->reply("Sorry, I didn't understand that. Type **help** to see options.");
});
