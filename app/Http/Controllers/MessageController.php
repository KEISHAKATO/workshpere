<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Message;
use App\Models\Application;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // Show chat thread for a job between logged-in user and the other party.
    public function index(Request $request, Job $job)
    {
        $user = $request->user();

        // Only employer or seekers who applied can access.
        $isEmployer = (int)$user->id === (int)$job->employer_id;
        $didApply   = Application::where('job_id', $job->id)->where('seeker_id', $user->id)->exists();

        if (!$isEmployer && !$didApply) {
            abort(403, 'Unauthorized.');
        }

        // Figure out the "other user" in the conversation
        if ($isEmployer) {
            $seekerId = (int) $request->query('seeker_id');
            if (!$seekerId) {
                // default to first applicant if none provided
                $seekerId = (int) Application::where('job_id', $job->id)->value('seeker_id');
            }

            if (!$seekerId) {
                // no applicants yet -> empty thread
                return view('chat.show', [
                    'job'         => $job,
                    'messages'    => collect(),
                    'otherUserId' => null,
                    'isEmployer'  => true,
                ]);
            }

            $otherUserId = $seekerId;
        } else {
            // seeker chats with employer
            $otherUserId = (int) $job->employer_id;
        }

        // Load messages (both directions)
        $messages = Message::where('job_id', $job->id)
            ->where(function ($q) use ($user, $otherUserId) {
                $q->where(function ($q2) use ($user, $otherUserId) {
                    $q2->where('sender_id', $user->id)->where('receiver_id', $otherUserId);
                })->orWhere(function ($q2) use ($user, $otherUserId) {
                    $q2->where('sender_id', $otherUserId)->where('receiver_id', $user->id);
                });
            })
            ->orderBy('created_at')
            ->get();

        return view('chat.show', [
            'job'         => $job,
            'messages'    => $messages,
            'otherUserId' => $otherUserId,
            'isEmployer'  => $isEmployer,
        ]);
    }

    // Optional JSON feed for polling
    public function fetch(Request $request, Job $job)
    {
        $user = $request->user();
        $isEmployer = (int)$user->id === (int)$job->employer_id;
        $didApply   = Application::where('job_id', $job->id)->where('seeker_id', $user->id)->exists();

        if (!$isEmployer && !$didApply) {
            abort(403);
        }

        $otherUserId = $isEmployer
            ? (int) $request->query('seeker_id')
            : (int) $job->employer_id;

        if ($isEmployer && !$otherUserId) {
            return response()->json(['messages' => []]);
        }

        $messages = Message::where('job_id', $job->id)
            ->where(function ($q) use ($user, $otherUserId) {
                $q->where(function ($q2) use ($user, $otherUserId) {
                    $q2->where('sender_id', $user->id)->where('receiver_id', $otherUserId);
                })->orWhere(function ($q2) use ($user, $otherUserId) {
                    $q2->where('sender_id', $otherUserId)->where('receiver_id', $user->id);
                });
            })
            ->orderBy('created_at')
            ->get(['id','sender_id','receiver_id','body','created_at']);

        return response()->json(['messages' => $messages]);
    }

    // Send a message
    public function store(Request $request, Job $job)
    {
        $user = $request->user();

        $isEmployer = (int)$user->id === (int)$job->employer_id;
        $didApply   = Application::where('job_id', $job->id)->where('seeker_id', $user->id)->exists();

        if (!$isEmployer && !$didApply) {
            abort(403, 'Unauthorized.');
        }

        $data = $request->validate([
            'body'      => ['required', 'string', 'max:5000'],
            'seeker_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        if ($isEmployer) {
            // employer must specify the seeker to send to
            $receiverId = (int) ($data['seeker_id'] ?? 0);
            if (!$receiverId) {
                return back()->withErrors(['body' => 'Missing seeker_id for employer message.']);
            }
        } else {
            // seeker -> employer
            $receiverId = (int) $job->employer_id;
        }

        Message::create([
        'job_id'      => $job->id,
        'sender_id'   => $user->id,
        'receiver_id' => $receiverId,
        'body'        => $data['body'],
        'content'     => $data['body'],
    ]);


        $params = $isEmployer ? ['job' => $job->id, 'seeker_id' => $receiverId] : ['job' => $job->id];

        return redirect()->route('chat.show', $params)->with('status', 'Message sent.');
    }
}
