<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ session('ui.theme', 'worksphere') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Worksphere') }}</title>

    {{-- Fonts (optional keep) --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@300..700" rel="stylesheet">


    {{-- Vite --}}
    <script>window.WORKSPHERE_GOOGLE_KEY = @json(config('services.google.maps_key'));</script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-base-200 min-h-screen">
    <div class="drawer lg:drawer-open">
        {{-- Drawer toggle (hidden on lg+) --}}
        <input id="ws-drawer" type="checkbox" class="drawer-toggle" />

        <div class="drawer-content flex flex-col">
            {{-- Top Navbar --}}
            @include('layouts.navigation')

            {{-- Page Heading (from x-slot name="header") --}}
            @isset($header)
                <header class="bg-base-100 border-b border-base-300">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            {{-- Page Content --}}
            <main class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-6">
                {{-- Flash (keeps your existing component) --}}
                @includeWhen(session('status'), 'components.flash')
                {{ $slot }}
            </main>

            {{-- Chatbot bubble (existing partial) --}}
            @include('partials.chatbot')
        </div>

        {{-- Sidebar / Drawer --}}
        <div class="drawer-side z-40">
            <label for="ws-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
            <aside class="w-72 bg-base-100 border-r border-base-300 min-h-full">
                <div class="px-4 py-4 border-b border-base-300 flex items-center gap-2">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2">
                        <x-application-logo class="h-8 w-8 text-primary" />
                        <span class="font-semibold text-base-content">Worksphere</span>
                    </a>
                </div>

                @include('layouts.sidebar')
            </aside>
        </div>
    </div>

    @stack('modals')
    @stack('scripts')
</body>
</html>
