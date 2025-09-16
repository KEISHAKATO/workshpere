<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role'     => ['required', 'in:seeker,employer'],
        ]);

        // Create user with selected role
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
        ]);

        // Create a blank profile row via relationship (auto-fills user_id)
        $user->profile()->firstOrCreate([]);

        event(new Registered($user));
        Auth::login($user);
        return redirect()->route('verification.notice');


        // Send them to the role-specific editor first; else go to dashboard
        if ($user->isEmployer() || $user->isAdmin()) {
            return redirect()
                ->route('employer.profile.edit')
                ->with('status', 'Welcome! Tell us about your company.');
        }

        return redirect()
            ->route('seeker.profile.edit')
            ->with('status', 'Welcome! Complete your profile to get better matches.');
    }
}
