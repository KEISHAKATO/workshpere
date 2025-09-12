<x-guest-layout>
    <div class="text-center">
        <h1 class="text-2xl font-bold">Sign in</h1>
    </div>

    <x-auth-session-status class="mb-3" :status="session('status')" />
    <x-input-error class="mb-3" :messages="$errors->all()" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <label class="form-control w-full">
            <div class="label"><span class="label-text">Email</span></div>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                   class="input input-bordered w-full" />
        </label>

        <label class="form-control w-full">
            <div class="label flex justify-between">
                <span class="label-text">Password</span>
                @if (Route::has('password.request'))
                    <a class="link link-hover text-sm" href="{{ route('password.request') }}">Forgot password?</a>
                @endif
            </div>
            <input id="password" name="password" type="password" required class="input input-bordered w-full" />
        </label>

        <label class="label cursor-pointer justify-start gap-3">
            <input id="remember_me" type="checkbox" name="remember" class="checkbox" />
            <span class="label-text">Remember me</span>
        </label>

        <button class="btn btn-primary w-full">Sign in</button>

        <div class="divider my-6">New here?</div>

        <a href="{{ route('register') }}" class="btn w-full">Create an account</a>
    </form>
</x-guest-layout>
