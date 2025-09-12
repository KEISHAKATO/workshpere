<x-guest-layout>
    <div class="card bg-base-100 shadow-xl max-w-md mx-auto">
        <div class="card-body">
            <h1 class="card-title justify-center">Sign in</h1>

            @include('components.flash')

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="label"><span class="label-text">Email</span></label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           class="input input-bordered w-full" required autofocus autocomplete="username">
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <div>
                    <label class="label"><span class="label-text">Password</span></label>
                    <input id="password" type="password" name="password"
                           class="input input-bordered w-full" required autocomplete="current-password">
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <div class="flex items-center justify-between">
                    <label class="label cursor-pointer gap-2">
                        <input type="checkbox" name="remember" class="checkbox checkbox-sm">
                        <span class="label-text">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="link link-primary text-sm" href="{{ route('password.request') }}">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <div class="form-control mt-2">
                    <button class="btn btn-primary">Sign in</button>
                </div>
            </form>

            @if (Route::has('register'))
                <div class="divider my-4">New here?</div>
                <a class="btn btn-ghost" href="{{ route('register') }}">Create an account</a>
            @endif
        </div>
    </div>
</x-guest-layout>
