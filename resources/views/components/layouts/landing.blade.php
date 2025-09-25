<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ session('ui.theme','worksphere') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Worksphere') }}</title>
    <script>window.WORKSPHERE_GOOGLE_KEY = @json(config('services.google.maps_key'));</script>

    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-dvh bg-base-200 text-base-content">
    {{-- public top nav --}}
    <header class="navbar bg-base-100 shadow-sm">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between gap-4 py-2">
                <a href="{{ url('/') }}" class="flex items-center gap-2 font-semibold">
                    <img src="{{ asset('workshpere-logo.png') }}" alt="Worksphere Logo" class="h-48 w-48">
                    <span>{{ config('app.name', 'Worksphere') }}</span>
                </a>

                <nav class="flex items-center gap-2">
                    <a href="{{ route('public.jobs.index') }}" class="btn btn-ghost btn-sm">Browse Jobs</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Sign in</a>
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Get Started</a>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    @if (session('status'))
        <div class="container mx-auto px-4 mt-4">
            <div class="alert alert-success">{{ session('status') }}</div>
        </div>
    @endif

    <main>
        {{ $slot }}
    </main>

    @include('partials.support-button')

</body>
</html>
