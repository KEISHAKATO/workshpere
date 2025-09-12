<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="">
        <div class="card-body">
            <h1 class="card-title text-2xl">Create your account</h1>

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                {{-- Name --}}
                <div>
                    <x-input-label for="name" value="Full Name" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                  value="{{ old('name') }}" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                {{-- Email --}}
                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                  value="{{ old('email') }}" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                {{-- Account type --}}
                <div>
                    <x-input-label value="Account Type" />
                    <div class="join mt-2">
                        <input class="join-item btn {{ old('role','seeker') === 'seeker' ? 'btn-primary' : '' }}"
                               type="button" value="Seeker"
                               onclick="document.getElementById('role-seeker').checked = true; toggleRoleButtons()">
                        <input class="join-item btn {{ old('role') === 'employer' ? 'btn-primary' : '' }}"
                               type="button" value="Employer"
                               onclick="document.getElementById('role-employer').checked = true; toggleRoleButtons()">
                    </div>

                    <input id="role-seeker"   type="radio" name="role" value="seeker"   class="hidden" {{ old('role','seeker') === 'seeker' ? 'checked' : '' }}>
                    <input id="role-employer" type="radio" name="role" value="employer" class="hidden" {{ old('role') === 'employer' ? 'checked' : '' }}>

                    <p class="text-sm text-gray-500 mt-2">
                        <span id="role-hint">
                            {{ old('role','seeker') === 'employer'
                                ? 'Employer: post jobs and manage applicants.'
                                : 'Seeker: browse jobs and apply.' }}
                        </span>
                    </p>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>

                {{-- Password --}}
                <div>
                    <x-input-label for="password" value="Password" />
                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full"
                                  required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                {{-- Confirm --}}
                <div>
                    <x-input-label for="password_confirmation" value="Confirm Password" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                                  class="mt-1 block w-full" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="form-control mt-6">
                    <button class="btn btn-primary">Create account</button>
                </div>

                <p class="text-sm text-center mt-2">
                    Already registered?
                    <a class="link link-primary" href="{{ route('login') }}">Sign in</a>
                </p>
            </form>
        </div>
    </div>

    <script>
        function toggleRoleButtons() {
            const seekerBtn   = document.querySelectorAll('.join-item.btn')[0];
            const employerBtn = document.querySelectorAll('.join-item.btn')[1];
            const seekerChecked   = document.getElementById('role-seeker').checked;
            seekerBtn.classList.toggle('btn-primary', seekerChecked);
            employerBtn.classList.toggle('btn-primary', !seekerChecked);
            document.getElementById('role-hint').textContent =
                seekerChecked
                    ? 'Seeker: browse jobs and apply.'
                    : 'Employer: post jobs and manage applicants.';
        }
        document.addEventListener('DOMContentLoaded', toggleRoleButtons);
    </script>
</x-guest-layout>
