<x-guest-layout>
    <div class="card bg-base-100 shadow-xl max-w-md mx-auto">
        <div class="card-body">
            <h1 class="card-title justify-center">Confirm your password</h1>
            <p class="text-sm opacity-70">For your security, please confirm your password to continue.</p>

            <form method="POST" action="{{ route('password.confirm') }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="label"><span class="label-text">Password</span></label>
                    <input id="password" type="password" name="password" class="input input-bordered w-full" required autocomplete="current-password">
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <button class="btn btn-primary w-full">Confirm</button>
            </form>
        </div>
    </div>
</x-guest-layout>
