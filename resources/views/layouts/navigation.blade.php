@php
    /** @var \App\Models\User|null $user */
    $user = auth()->user();
    $currentTheme = session('ui.theme', 'worksphere');
    $themes = [
        'worksphere' => 'Worksphere',
        'light'      => 'Light',
        'dark'       => 'Dark',

    ];
@endphp

<div class="navbar bg-base-100 border-b border-base-300 px-safe">
    {{-- LEFT: brand / drawer --}}
    <div class="flex-1 min-w-0">
        <label for="ws-drawer" class="btn btn-ghost lg:hidden mr-1" aria-label="open sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </label>

        <a href="{{ url('/') }}" class="btn btn-ghost text-base sm:text-lg font-semibold truncate">Dashboard</a>
    </div>

    {{-- RIGHT: tools / account --}}
    <div class="flex-none flex items-center gap-1 sm:gap-2 min-w-0 flex-wrap">
        {{-- Theme switcher (icon-only on xs) --}}
        <details class="dropdown dropdown-end">
            <summary class="btn btn-ghost btn-sm normal-case">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 sm:mr-2" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M21.64 13A9 9 0 1111 2.36a7 7 0 1010.64 10.64z"/>
                </svg>
                <span class="hidden sm:inline">Theme</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 opacity-70 hidden sm:inline" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 9l6 6 6-6"/>
                </svg>
            </summary>
            <ul class="menu menu-sm dropdown-content bg-base-100 rounded-box z-[60] mt-2 w-56 p-2 shadow">
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

        {{-- Account --}}
        @auth
            <details class="dropdown dropdown-end">
                <summary class="btn btn-ghost btn-sm">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="avatar placeholder shrink-0">
                            <div class="bg-primary text-primary-content rounded-full w-7 h-7 grid place-items-center">
                                <span class="text-xs">
                                    {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user?->name ?? 'U', 0, 1)) }}
                                </span>
                            </div>
                        </div>
                        <span class="hidden sm:inline text-sm truncate max-w-[10rem]">{{ $user?->name }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-70 hidden sm:block" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 9l6 6 6-6"/>
                        </svg>
                    </div>
                </summary>
                <ul class="menu menu-sm dropdown-content bg-base-100 rounded-box z-[60] mt-2 w-64 p-2 shadow">
                    <li class="menu-title">Signed in</li>
                    <li>
                        <span class="truncate px-3 text-xs text-base-content/70 block max-w-full">
                            {{ $user?->email }}
                        </span>
                    </li>

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
            <div class="flex items-center gap-1 sm:gap-2">
                <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Log in</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Sign up</a>
            </div>
        @endguest
    </div>
</div>

@push('scripts')
<script>
    window.addEventListener('pageshow', function (event) {
        if (event.persisted) window.location.reload();
    });
</script>
@endpush
