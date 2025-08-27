<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Request $request, Job $job)
    {
        $data = $request->validate([
            'content' => 'required|string|max:5000',
            'receiver_id' => 'required|exists:users,id',
        ]);

        // only participants can post: employer or applicant (seeker)
        $isEmployer = $job->employer_id === auth()->id();
        $isSeeker = $job->applications()->where('seeker_id', auth()->id())->exists();
        abort_unless($isEmployer || $isSeeker, 403);

        Message::create([
            'job_id'      => $job->id,
            'sender_id'   => auth()->id(),
            'receiver_id' => $data['receiver_id'],
            'content'     => $data['content'],
        ]);

        return back()->with('ok', 'Message sent.');
    }
}
