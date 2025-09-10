<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

class SupportConversation extends Conversation
{
    public function run(): void
    {
        $this->showMenu();
    }

    protected function showMenu(): void
    {
        $q = Question::create("How can I help?\nChoose a topic:")
            ->addButtons([
                Button::create('🔑 Registration / Login')->value('registration'),
                Button::create('🧑‍💼 Apply for a Job')->value('apply'),
                Button::create('🏢 Post a Job (Employer)')->value('post'),
                Button::create('👤 My Profile')->value('profile'),
                Button::create('🔒 Password & Security')->value('security'),
                Button::create('❓ Other FAQs')->value('faq'),
            ]);

        $this->ask($q, function (Answer $answer) {
            switch ($answer->getValue()) {
                case 'registration':
                    $this->say("**Registration**\n1) Go to the Register page\n2) Fill in name, email, password\n3) Confirm email if required\n4) Log in from the Login page");
                    $this->say("Links: /register  •  /login");
                    break;

                case 'apply':
                    $this->say("**How to apply for a job**\n1) Browse jobs (Jobs → Browse)\n2) Open a job you like\n3) Click **Apply**\n4) Fill the short form & submit\n5) Track status in **My Applications**");
                    break;

                case 'post':
                    $this->say("**Employers – Post a Job**\n1) Log in as employer\n2) Go to **Post a Job**\n3) Fill title, description, skills, location, pay range\n4) Publish\n5) Review applicants in **Manage Job Posts**");
                    break;

                case 'profile':
                    $this->say("**Edit Profile**\n- Job seeker: Dashboard → My Profile\n- Employer: Dashboard → Company Profile\n- Tip: Use the location search to set city/county and we’ll auto-fill lat/lng.");
                    break;

                case 'security':
                    $this->say("**Password & Security**\n- Forgot password? Use **Forgot your password?** on /login\n- Change password: Profile → Update password\n- Suspended account? Contact support or an admin.");
                    break;

                case 'faq':
                default:
                    $this->say("**FAQs**\n• Where do I see my applications? → Seeker → My Applications\n• How do I filter jobs? → Browse Jobs page filters\n• Why can’t I post? → You need employer role – ask an admin\n• Account suspended? → Contact support.");
                    break;
            }

            // Loop back to menu
            $this->showMenu();
        });
    }
}
