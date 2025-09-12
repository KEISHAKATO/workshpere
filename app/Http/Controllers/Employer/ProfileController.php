<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user    = $request->user();
        $profile = $user->profile()->firstOrCreate([]);

        // Reviews received by this employer (from seekers)
        $reviews = $user->reviewsReceived()
            ->with('reviewer:id,name')
            ->latest()
            ->paginate(5, ['*'], 'reviews_page');

        $avgRating    = $user->avg_rating; // accessor on User
        $reviewsCount = $user->reviewsReceived()->count();

        return view('employer.profile.edit', compact('profile', 'reviews', 'avgRating', 'reviewsCount'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'company_name'     => ['required','string','max:160'],
            'website'          => ['nullable','url','max:191'],
            'about'            => ['nullable','string','max:5000'],
            'location_city'    => ['nullable','string','max:120'],
            'location_county'  => ['nullable','string','max:120'],
            'lat'              => ['nullable','numeric'],
            'lng'              => ['nullable','numeric'],
        ]);

        $request->user()->profile()->updateOrCreate([], $data);

        return back()->with('status', 'Company profile updated.');
    }
}
