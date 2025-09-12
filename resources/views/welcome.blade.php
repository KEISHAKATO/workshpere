<x-layouts.landing>
    {{-- HERO (full-bleed) --}}
    <section class="hero min-h-[70svh] bg-base-100">
        <div class="hero-content text-center">
            <div class="max-w-3xl">
                <h1 class="text-4xl md:text-5xl font-extrabold leading-tight">
                    Worksphere - local jobs, faster matches.
                </h1>
                <p class="mt-4 opacity-80">
                    Post jobs, discover skilled workers, and manage applications in one clean dashboard.
                    Built for counties, SMEs, and everyday pros.
                </p>
                <div class="mt-6 flex justify-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">Open Dashboard</a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-primary">Get Started</a>
                        <a href="{{ route('login') }}" class="btn btn-ghost">Sign in</a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    {{-- KPIs --}}
    <section class="py-12 bg-base-200">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="stat bg-base-100 rounded-box shadow">
                    <div class="stat-title">Jobs Posted</div>
                    <div class="stat-value">1,240</div>
                </div>
                <div class="stat bg-base-100 rounded-box shadow">
                    <div class="stat-title">Applicants</div>
                    <div class="stat-value">3,580</div>
                </div>
                <div class="stat bg-base-100 rounded-box shadow">
                    <div class="stat-title">Avg Match</div>
                    <div class="stat-value">83%</div>
                </div>
            </div>
        </div>
    </section>

    {{-- WHY --}}
    <section class="py-16 bg-base-100">
        <div class="container mx-auto px-4 max-w-5xl">
            <h2 class="text-2xl font-bold">Why Worksphere?</h2>
            <div class="mt-6 grid sm:grid-cols-3 gap-6">
                <div class="card bg-base-200">
                    <div class="card-body">
                        <h3 class="card-title">Smart matches</h3>
                        <p>Recommendations for both sides based on skills & location.</p>
                    </div>
                </div>
                <div class="card bg-base-200">
                    <div class="card-body">
                        <h3 class="card-title">Built-in chat & reviews</h3>
                        <p>Keep conversations and feedback in one place.</p>
                    </div>
                </div>
                <div class="card bg-base-200">
                    <div class="card-body">
                        <h3 class="card-title">County-aware search</h3>
                        <p>Location fields tailored to Kenyan counties and cities.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.landing>
