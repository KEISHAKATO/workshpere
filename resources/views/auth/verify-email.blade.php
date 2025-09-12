<x-guest-layout>
    <div class="card bg-base-100 shadow-xl max-w-lg mx-auto">
        <div class="card-body">
            <h1 class="card-title justify-center">Verify your email</h1>
            <p class="text-sm opacity-80">
                Thanks for signing up! We’ve sent a verification link to your email.
                Didn’t get it? You can request another.
            </p>

            @if (session('status') == 'verification-link-sent')
                <div class="alert alert-success mt-3">
                    <span>A new verification link has been sent to your email address.</span>
                </div>
            @endif

            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-2">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button class="btn btn-primary w-full">Resend verification email</button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn w-full">Log out</button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
