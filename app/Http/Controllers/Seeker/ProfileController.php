<?php

namespace App\Http\Controllers\Seeker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $profile = $request->user()->profile()->firstOrCreate([]);
        return view('seeker.profile.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'bio'              => ['nullable','string','max:160'],
            'about'            => ['nullable','string','max:5000'],
            'skills'           => ['nullable','string'],           // CSV in the form
            'experience_years' => ['nullable','integer','min:0','max:60'],
            'location_city'    => ['nullable','string','max:120'],
            'location_county'  => ['nullable','string','max:120'],
            'lat'              => ['nullable','numeric'],
            'lng'              => ['nullable','numeric'],
        ]);

        // Convert CSV to array
        $skillsCsv = $data['skills'] ?? '';
        $data['skills'] = array_values(array_filter(array_map(
            fn ($s) => trim($s),
            $skillsCsv === '' ? [] : preg_split('/,|;|\|/u', $skillsCsv)
        )));

        $request->user()->profile()->updateOrCreate([], $data);

        return back()->with('status', 'Profile saved.');
    }
}
