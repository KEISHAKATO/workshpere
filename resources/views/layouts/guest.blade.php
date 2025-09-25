<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ session('ui.theme', 'worksphere') }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Workshphere') }}</title>
        
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased min-h-screen bg-cover bg-center"
          style="background-image:url('https://ik.imagekit.io/xqjcglzri/workshpere-hero.png?updatedAt=1757700544438');">
        <div class="min-h-screen flex flex-col sm:justify-center items-center p-6">
            <div class="mb-6">
                <a href="/">
                    <img src="{{ asset('workshpere-logo.png') }}" alt="Worksphere Logo" class="h-48 w-48">
                </a>
            </div>

            <div class="w-full sm:max-w-md bg-base-100 shadow-xl rounded-xl p-8">
                {{ $slot }}
            </div>
        </div>
        @include('partials.support-button')


    </body>
</html>
