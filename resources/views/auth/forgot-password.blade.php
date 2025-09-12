<x-guest-layout>
    <div class="card bg-base-100 shadow-xl max-w-md mx-auto">
        <div class="card-body">
            <h1 class="card-title justify-center">Forgot password</h1>
            <p class="text-sm opacity-70">Enter your email and we’ll send you a reset link.</p>

            @if (session('status'))
                <div class="alert alert-success mt-3"><span>{{ session('status') }}</span></div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="label"><span class="label-text">Email</span></label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" class="input input-bordered w-full" required autofocus>
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <button class="btn btn-primary w-full">Email reset link</button>
            </form>

            <div class="mt-4 text-center">
                <a href="{{ route('login') }}" class="link link-primary">Back to login</a>
            </div>
        </div>
    </div>
</x-guest-layout>
