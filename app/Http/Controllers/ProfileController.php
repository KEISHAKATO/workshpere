<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'bio'              => ['nullable','string','max:2000'],
            'skills'           => ['nullable','string'], // CSV string from the form
            'experience_years' => ['nullable','integer','min:0','max:60'],
            'location_city'    => ['nullable','string','max:120'],
            'location_county'  => ['nullable','string','max:120'],
            'lat'              => ['nullable','numeric'],
            'lng'              => ['nullable','numeric'],
        ]);

        // Convert CSV -> array (and clean it up)
        $skillsCsv = $data['skills'] ?? '';
        $skillsArr = array_values(array_filter(array_map(
            fn($s) => trim($s),
            $skillsCsv === '' ? [] : preg_split('/,|;|\|/u', $skillsCsv)
        )));
        $data['skills'] = $skillsArr;

        $request->user()->profile()->updateOrCreate([], $data);

        return back()->with('status', 'Profile saved.');
    }


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
