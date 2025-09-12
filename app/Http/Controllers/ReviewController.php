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

        // Ensure current user is part of this application (seeker or the job’s employer)
        $isSeekerParticipant   = $application->seeker_id === $user->id;
        $isEmployerParticipant = optional($application->job)->employer_id === $user->id;

        if (!($isSeekerParticipant || $isEmployerParticipant)) {
            abort(403, 'Not allowed to review this application.');
        }

        // Only allow after completion (prefer "completed" else fallback to "accepted")
        $status = (string) $application->status;
        $allowed = in_array($status, ['completed', 'accepted'], true);
        if (!$allowed) {
            return back()->withErrors([
                'review' => 'Reviews can only be left after the job is accepted/completed.'
            ]);
        }

        $data = $request->validate([
            'rating'  => ['required','integer','min:1','max:5'],
            'title'   => ['nullable','string','max:120'],
            'comment' => ['nullable','string','max:2000'],
        ]);

        $reviewerRole = $isEmployerParticipant ? 'employer' : 'seeker';
        $revieweeId   = $isEmployerParticipant ? $application->seeker_id : optional($application->job)->employer_id;

        // Guard against duplicate
        $exists = Review::where('application_id', $application->id)
            ->where('reviewer_id', $user->id)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'review' => 'You already submitted a review for this job.'
            ]);
        }

        Review::create([
            'application_id' => $application->id,
            'reviewer_id'    => $user->id,
            'reviewee_id'    => $revieweeId,
            'reviewer_role'  => $reviewerRole,
            'rating'         => $data['rating'],
            'title'          => $data['title'] ?? null,
            'comment'        => $data['comment'] ?? null,
        ]);

        return back()->with('status', 'Thanks! Your review has been submitted.');
    }
}
