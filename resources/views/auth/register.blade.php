<x-guest-layout>
    <div class="text-center">
        <h1 class="text-2xl font-bold">Create your account</h1>
    </div>

    <x-input-error class="mb-3" :messages="$errors->all()" />

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <label class="form-control w-full">
            <div class="label"><span class="label-text">Full Name</span></div>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required class="input input-bordered w-full" />
        </label>

        <label class="form-control w-full">
            <div class="label"><span class="label-text">Email</span></div>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required class="input input-bordered w-full" />
        </label>

        {{-- Account type toggle --}}
        <div>
            <div class="label"><span class="label-text">Account Type</span></div>
            <div class="join">
                <input class="join-item btn {{ old('role','seeker')==='seeker'?'btn-primary':'' }}"
                       type="button" value="Seeker"
                       onclick="document.getElementById('role-seeker').checked=true; syncRoleBtn(this)" />
                <input class="join-item btn {{ old('role','seeker')==='employer'?'btn-primary':'' }}"
                       type="button" value="Employer"
                       onclick="document.getElementById('role-employer').checked=true; syncRoleBtn(this)" />
            </div>
            <input type="radio" id="role-seeker" name="role" value="seeker" class="hidden" {{ old('role','seeker')==='seeker'?'checked':'' }}>
            <input type="radio" id="role-employer" name="role" value="employer" class="hidden" {{ old('role')==='employer'?'checked':'' }}>
            <p class="text-sm opacity-70 mt-2"><span id="role-help">Seeker: browse jobs and apply.</span></p>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <label class="form-control">
                <div class="label"><span class="label-text">Password</span></div>
                <input id="password" name="password" type="password" required class="input input-bordered w-full" />
            </label>

            <label class="form-control">
                <div class="label"><span class="label-text">Confirm Password</span></div>
                <input id="password_confirmation" name="password_confirmation" type="password" required class="input input-bordered w-full" />
            </label>
        </div>

        <button class="btn btn-primary w-full">Create account</button>

        <p class="text-center text-sm mt-2">
            Already registered?
            <a href="{{ route('login') }}" class="link link-hover">Sign in</a>
        </p>
    </form>

    <script>
        function syncRoleBtn(el){
            const btns = el.parentElement.querySelectorAll('.btn');
            btns.forEach(b => b.classList.remove('btn-primary'));
            el.classList.add('btn-primary');
            document.getElementById('role-help').innerText =
                el.value === 'Employer'
                ? 'Employer: post jobs and manage applications.'
                : 'Seeker: browse jobs and apply.';
        }
    </script>
</x-guest-layout>
