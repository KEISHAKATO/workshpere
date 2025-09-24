<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      data-theme="{{ session('ui.theme', 'worksphere') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Worksphere') }}</title>

    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@300..700" rel="stylesheet">

    <script>window.WORKSPHERE_GOOGLE_KEY = @json(config('services.google.maps_key'));</script>

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Theme guard --}}
    <script>
        (function () {
            var theme = "{{ session('ui.theme', 'worksphere') }}";
            if (theme !== 'dark') {
                document.documentElement.classList.remove('dark');
                try {
                    if (localStorage.getItem('color-theme') === 'dark') {
                        localStorage.removeItem('color-theme');
                    }
                } catch (e) {}
            }
        })();
    </script>
</head>
<body class="font-sans antialiased bg-base-200 min-h-screen">
    <div class="drawer lg:drawer-open">
        {{-- Drawer toggle (hidden on lg+) --}}
        <input id="ws-drawer" type="checkbox" class="drawer-toggle" />

        <div class="drawer-content flex flex-col">
            {{-- Top Navbar --}}
            @include('layouts.navigation')

            {{-- Page Heading --}}
            @isset($header)
                <header class="bg-base-100 border-b border-base-300">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            {{-- Main Content --}}
            <main class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-6">
                @includeWhen(session('status'), 'components.flash')
                {{ $slot }}
            </main>

            {{-- Chatbot --}}
            
        </div>
        

        {{-- Sidebar --}}
        <div class="drawer-side z-40">
            <label for="ws-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
            <aside class="w-72 bg-base-100 border-r border-base-300 min-h-full">
                <div class="px-4 py-4 border-b border-base-300 flex items-center gap-2">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2">
                        <img src="{{ asset('workshpere-logo.png') }}" alt="Worksphere Logo" class="h-48 w-48">
                    </a>
                </div>

                @include('layouts.sidebar')
            </aside>
        </div>
    </div>
    @include('partials.chatbot')
    @stack('modals')
    @stack('scripts')
</body>
</html>



                