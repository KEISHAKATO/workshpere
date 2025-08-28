<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $profile = $request->user()->profile()->firstOrCreate([]);
        return view('employer.profile.edit', compact('profile'));
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
