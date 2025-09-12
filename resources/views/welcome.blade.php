@php $isAuthed = auth()->check(); @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ session('ui.theme','worksphere') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name','Worksphere') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="antialiased bg-base-100 text-base-content">
    {{-- Top nav --}}
    <header class="sticky top-0 z-30 bg-base-100/90 backdrop-blur border-b border-base-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <img src="{{ asset('workshpere-logo.png') }}" alt="Worksphere Logo" class="h-48 w-48">
                <!-- <span class="font-semibold">Worksphere</span> -->
            </a>

            <nav class="flex items-center gap-2">
                <a href="#features" class="btn btn-ghost btn-sm">Features</a>
                <a href="#why" class="btn btn-ghost btn-sm">Why Worksphere</a>
                <a href="#footer" class="btn btn-ghost btn-sm">Contact</a>

                @if($isAuthed)
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm">Go to dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Sign in</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Get started</a>
                @endif
            </nav>
        </div>
    </header>

    {{-- Hero --}}
    {{-- Hero --}}
<section class="relative overflow-hidden bg-cover bg-center" style="background-image: url('https://ik.imagekit.io/xqjcglzri/workshpere-hero.png?updatedAt=1757700544438');">
    <div class="absolute inset-0 bg-gradient-to-b from-primary/5 to-transparent pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
        <div class="grid lg:grid-cols-2 gap-10 items-center">
                <div>
                    <h1 class="text-4xl sm:text-5xl font-bold leading-tight">
                        Worksphere - hire locally, <span class="text-primary">faster</span>.
                    </h1>
                    <p class="mt-5 text-lg opacity-80">
                        Post roles, discover skilled workers, and manage applications in one simple workspace.
                        Built for counties, SMEs, and pros.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        @if($isAuthed)
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/1000/svg" class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h2v-2H3v2zm4 0h14v-2H7v2z"/></svg>
                                Open dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/1000/svg" class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12a5 5 0 100-10 5 5 0 000 10zm-7 9a7 7 0 0114 0H5z"/></svg>
                                Get started free
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-ghost">
                                <svg xmlns="http://www.w3.org/1000/svg" class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor"><path d="M10 17l5-5-5-5v10zM4 4h8v2H4v12h8v2H4a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                                Sign in
                            </a>
                        @endif
                    </div>

                    {{-- Stats --}}
                    <div class="mt-10 grid grid-cols-3 gap-4 text-center sm:max-w-md">
                        <div class="stat place-items-center bg-base-100 shadow-sm rounded-box">
                            <div class="stat-title">Jobs Posted</div>
                            <div class="stat-value text-primary">1,240</div>
                        </div>
                        <div class="stat place-items-center bg-base-100 shadow-sm rounded-box">
                            <div class="stat-title">Applicants</div>
                            <div class="stat-value">3,580</div>
                        </div>
                        <div class="stat place-items-center bg-base-100 shadow-sm rounded-box">
                            <div class="stat-title">Avg. Match</div>
                            <div class="stat-value">83%</div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="mockup-window border bg-base-100 shadow-xl">
                        <div class="p-6">
                            @if(isset($latestJobs) && $latestJobs->count())
                                <div class="flex items-center justify-between mb-4">
                                    <div class="font-semibold">Latest roles</div>
                                    <a href="{{ route('public.jobs.index') }}" class="link link-primary text-sm">Browse all</a>
                                </div>

                                <div class="grid sm:grid-cols-2 gap-4">
                                    @foreach($latestJobs as $j)
                                        <a href="{{ route('public.jobs.show', $j) }}" class="card bg-base-100 border border-base-300 hover:shadow-md transition">
                                            <div class="card-body p-4">
                                                <div class="flex items-start justify-between gap-2">
                                                    <h4 class="font-semibold line-clamp-2">{{ $j->title }}</h4>
                                                    <span class="badge badge-soft">{{ ucfirst(str_replace('_',' ', $j->job_type)) }}</span>
                                                </div>
                                                <div class="text-sm opacity-80 mt-1">
                                                    {{ $j->location_city ?? '—' }}, {{ $j->location_county ?? '—' }}
                                                </div>
                                                @if(is_array($j->required_skills) && count($j->required_skills))
                                                    <div class="mt-3 flex flex-wrap gap-2">
                                                        @foreach(array_slice($j->required_skills, 0, 3) as $s)
                                                            <span class="badge badge-ghost">{{ $s }}</span>
                                                        @endforeach
                                                        @if(count($j->required_skills) > 3)
                                                            <span class="badge badge-ghost">+{{ count($j->required_skills) - 3 }}</span>
                                                        @endif
                                                    </div>
                                                @endif
                                                <div class="mt-3 text-sm font-medium">
                                                    {{ $j->currency ?? 'KES' }}
                                                    {{ $j->pay_min ? number_format($j->pay_min) : '—' }}
                                                    – {{ $j->pay_max ? number_format($j->pay_max) : '—' }}
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                {{-- Fallback: show skeletons if there are no jobs to showcase yet --}}
                                <div class="skeleton h-4 w-32 mb-4"></div>
                                <div class="skeleton h-4 w-full mb-2"></div>
                                <div class="skeleton h-4 w-2/3"></div>
                                <div class="mt-6 grid grid-cols-2 gap-3">
                                    <div class="skeleton h-24"></div>
                                    <div class="skeleton h-24"></div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="absolute -bottom-10 -left-0 hidden sm:block">
                        <div class="badge badge-primary badge-lg shadow">AI-assisted matches</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section id="features" class="py-12 sm:py-16 bg-base-100 border-t border-base-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl sm:text-3xl font-bold">Everything you need to hire & get hired</h2>
            <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach([
                    ['icon'=>'briefcase','title'=>'Simple job posting','text'=>'Create polished listings with location, skills, and pay ranges.'],
                    ['icon'=>'users','title'=>'Smart recommendations','text'=>'We surface relevant seekers for each role and jobs for seekers.'],
                    ['icon'=>'chat','title'=>'Built-in chat','text'=>'Message candidates or employers securely without leaving your dashboard.'],
                    ['icon'=>'star','title'=>'Reviews','text'=>'Structured feedback builds trust and speeds up decisions.'],
                    ['icon'=>'map','title'=>'County-aware search','text'=>'Find local talent quickly with precise location filters.'],
                    ['icon'=>'chart','title'=>'Reports','text'=>'Track posting performance and applicant funnels at a glance.'],
                ] as $f)
                <div class="card bg-base-100 border border-base-300 shadow-sm">
                    <div class="card-body">
                        <div class="mb-3">
                            <x-icon :name="$f['icon']" class="w-6 h-6 text-primary" />
                        </div>
                        <h3 class="font-semibold text-lg">{{ $f['title'] }}</h3>
                        <p class="opacity-80 mt-1">{{ $f['text'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section id="why" class="py-16">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold">Hire better in days, not weeks</h2>
            <p class="mt-3 opacity-80">
                Whether you're an SME, county HR, or a skilled pro - Worksphere helps you connect and close quickly.
            </p>
            <div class="mt-8 flex justify-center gap-3">
                <a href="{{ route('register') }}" class="btn btn-primary btn-wide">Create free account</a>
                <a href="{{ route('public.jobs.index') }}" class="btn btn-ghost">Browse jobs</a>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer id="footer" class="border-t border-base-300 bg-base-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <a href="{{ url('/') }}" class="flex items-center gap-2">
                    <img src="{{ asset('workshpere-logo.png') }}" alt="Worksphere Logo" class="h-32 w-32">
                    <!-- <span class="font-semibold">Worksphere</span> -->
                </a>
                
                <span class="opacity-70">© {{ date('Y') }}</span>
            </div>
            <div class="flex gap-4 text-sm">
                <a href="{{ route('public.jobs.index') }}" class="link link-hover">Jobs</a>
                <a href="{{ route('login') }}" class="link link-hover">Sign in</a>
                <a href="{{ route('register') }}" class="link link-hover">Create account</a>
            </div>
        </div>
    </footer>
</body>
</html>
