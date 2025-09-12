<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user    = $request->user();
        $profile = $user->profile()->firstOrCreate([]);

        // Reviews received by this seeker (from employers)
        $reviews = $user->reviewsReceived()
            ->with('reviewer:id,name')
            ->latest()
            ->paginate(5, ['*'], 'reviews_page');

        $avgRating    = $user->avg_rating; // accessor on User
        $reviewsCount = $user->reviewsReceived()->count();

        return view('seeker.profile.edit', compact('profile', 'reviews', 'avgRating', 'reviewsCount'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'bio'              => ['nullable','string','max:160'],
            'about'            => ['nullable','string','max:5000'],
            'skills'           => ['nullable','string'],
            'experience_years' => ['nullable','integer','min:0','max:60'],
            'location_city'    => ['nullable','string','max:120'],
            'location_county'  => ['nullable','string','max:120'],
            'lat'              => ['nullable','numeric'],
            'lng'              => ['nullable','numeric'],
        ]);

        // CSV → array
        $skillsCsv     = $data['skills'] ?? '';
        $data['skills'] = array_values(array_filter(array_map(
            fn ($s) => trim($s),
            $skillsCsv === '' ? [] : preg_split('/,|;|\|/u', $skillsCsv)
        )));

        $request->user()->profile()->updateOrCreate([], $data);

        return back()->with('status', 'Profile saved.');
    }
}
