@php
    /** @var \App\Models\User|null $user */
    $user = auth()->user();
    $currentTheme = session('ui.theme', 'worksphere');
    $themes = [
        'worksphere' => 'Worksphere',
        'light'      => 'Light',
        'dark'       => 'Dark',
        'business'   => 'Business',
        'corporate'  => 'Corporate',
        'emerald'    => 'Emerald',
        'cupcake'    => 'Cupcake',
        'lofi'       => 'Lofi',
        'synthwave'  => 'Synthwave',
    ];
@endphp

<div class="navbar bg-base-100 border-b border-base-300">
    <div class="flex-1">
        {{-- Drawer toggle (mobile) --}}
        <label for="ws-drawer" class="btn btn-ghost lg:hidden" aria-label="open sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </label>

        {{-- Brand --}}
        <a href="{{ url('/') }}" class="btn btn-ghost text-lg font-semibold">Dashboard</a>
    </div>

    <div class="flex-none gap-2">
        {{-- Theme switcher --}}
        <details class="dropdown dropdown-end">
            <summary class="btn btn-ghost normal-case">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M21.64 13A9 9 0 1111 2.36a7 7 0 1010.64 10.64z"/>
                </svg>
                Theme
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 opacity-70" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 9l6 6 6-6"/>
                </svg>
            </summary>
            <ul class="menu menu-sm dropdown-content bg-base-100 rounded-box z-[1] mt-2 w-56 p-2 shadow">
                @foreach($themes as $value => $label)
                    <li>
                        <form method="POST" action="{{ route('ui.theme') }}">
                            @csrf
                            <input type="hidden" name="theme" value="{{ $value }}">
                            <button type="submit" class="{{ $currentTheme === $value ? 'active font-semibold' : '' }}">
                                {{ $label }}
                            </button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </details>

        {{-- Account / Auth --}}
        @auth
            <details class="dropdown dropdown-end">
                <summary class="btn btn-ghost">
                    <div class="flex items-center gap-2">
                        <div class="avatar placeholder">
                            <div class="bg-primary text-primary-content rounded-full w-8">
                                <span class="text-sm">
                                    {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user?->name ?? 'U', 0, 1)) }}
                                </span>
                            </div>
                        </div>
                        <span class="hidden sm:inline text-sm">{{ $user?->name }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-70" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 9l6 6 6-6"/>
                        </svg>
                    </div>
                </summary>
                <ul class="menu menu-sm dropdown-content bg-base-100 rounded-box z-[1] mt-2 w-56 p-2 shadow">
                    <li class="menu-title">Signed in</li>
                    <li><span class="truncate px-3 text-xs text-base-content/70">{{ $user?->email }}</span></li>

                    <li class="menu-title mt-2">Account</li>
                    @php
                        $settingsUrl = null;
                        if ($user?->isSeeker() && \Illuminate\Support\Facades\Route::has('seeker.profile.edit')) {
                            $settingsUrl = route('seeker.profile.edit');
                        } elseif ($user?->isEmployer() && \Illuminate\Support\Facades\Route::has('employer.profile.edit')) {
                            $settingsUrl = route('employer.profile.edit');
                        }
                    @endphp
                    @if($settingsUrl)
                        <li><a href="{{ $settingsUrl }}">Settings</a></li>
                    @endif

                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">Logout</button>
                        </form>
                    </li>
                </ul>
            </details>
        @endauth

        @guest
            <div class="flex items-center gap-2">
                <a href="{{ route('login') }}" class="btn btn-ghost">Log in</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Sign up</a>
            </div>
        @endguest
    </div>
</div>

@push('scripts')
<script>
    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
</script>
@endpush
