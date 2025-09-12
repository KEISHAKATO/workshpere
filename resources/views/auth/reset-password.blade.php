<x-guest-layout>
    <div class="card bg-base-100 shadow-xl max-w-md mx-auto">
        <div class="card-body">
            <h1 class="card-title justify-center">Reset password</h1>

            <form method="POST" action="{{ route('password.update') }}" class="mt-2 space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div>
                    <label class="label"><span class="label-text">Email</span></label>
                    <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" class="input input-bordered w-full" required autofocus>
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <div>
                    <label class="label"><span class="label-text">New password</span></label>
                    <input id="password" type="password" name="password" class="input input-bordered w-full" required autofocus>
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <div>
                    <label class="label"><span class="label-text">Confirm password</span></label>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="input input-bordered w-full" required>
                </div>

                <button class="btn btn-primary w-full mt-2">Update password</button>
            </form>
        </div>
    </div>
</x-guest-layout>
