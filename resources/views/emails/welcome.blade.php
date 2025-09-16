@component('mail::message')
# Hi {{ $userName }},

Welcome to **Workshpere**! Your email is verified and you’re all set.

@component('mail::button', ['url' => config('app.url').'/dashboard'])
Go to Dashboard
@endcomponent

If you need help, just reply to this email.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
