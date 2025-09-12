{{-- resources/views/layouts/guest.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ session('ui.theme', 'worksphere') }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-base-200">
        <div class="min-h-screen flex flex-col sm:justify-center items-center p-6">
            <div class="mb-6">
                <a href="/">
                    <x-application-logo class="w-20 h-20 text-primary" />
                </a>
            </div>

            {{-- Single card container for auth forms --}}
            <div class="w-full sm:max-w-md bg-base-100 shadow-xl rounded-xl p-8">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
