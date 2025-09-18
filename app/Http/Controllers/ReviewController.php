<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Application $application)
    {
        $user = $request->user();

        $isSeekerParticipant   = $application->seeker_id === $user->id;
        $isEmployerParticipant = optional($application->job)->employer_id === $user->id;

        if (!($isSeekerParticipant || $isEmployerParticipant)) {
            abort(403, 'Not allowed to review this application.');
        }

        $status = (string) $application->status;
        if (!in_array($status, ['completed', 'accepted'], true)) {
            return back()->withErrors([
                'review' => 'Reviews can only be left after the job is accepted/completed.'
            ]);
        }

        $data = $request->validate([
            'rating'   => ['required','integer','min:1','max:5'],
            'feedback' => ['nullable','string','max:2000'],
        ]);

        $revieweeId = $isEmployerParticipant
            ? $application->seeker_id
            : optional($application->job)->employer_id;

        $exists = Review::where('job_id', $application->job_id)
            ->where('reviewer_id', $user->id)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'review' => 'You already submitted a review for this job.'
            ]);
        }

        Review::create([
            'job_id'      => $application->job_id,
            'reviewer_id' => $user->id,
            'reviewee_id' => $revieweeId,
            'rating'      => $data['rating'],
            'feedback'    => $data['feedback'] ?? null,
        ]);

        return back()->with('status', 'Thanks! Your review has been submitted.');
    }
}
