<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Message;
use App\Models\Application;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // Show chat thread for a job between the logged-in user and the other party.
    public function index(Request $request, Job $job)
    {
        $user = $request->user();

        $isEmployer = $user->id === (int) $job->employer_id;
        $didApply   = Application::where('job_id', $job->id)
                        ->where('seeker_id', $user->id)->exists();

        if (! $isEmployer && ! $didApply) {
            abort(403, 'Unauthorized.');
        }

        if ($isEmployer) {
            $seekerId = (int) $request->query('seeker_id');
            if (!$seekerId) {
                $seekerId = (int) Application::where('job_id', $job->id)->value('seeker_id');
            }
            if (!$seekerId) {
                $messages = collect();
                return view('chat.show', [
                    'job'         => $job,
                    'messages'    => $messages,
                    'otherUserId' => null,
                    'isEmployer'  => true,
                ]);
            }
            $otherUserId = $seekerId;
        } else {
            $otherUserId = (int) $job->employer_id;
        }

        // Load thread (both directions)
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

        Message::where('job_id', $job->id)
            ->where('receiver_id', $user->id)
            ->where('sender_id', $otherUserId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('chat.show', [
            'job'         => $job,
            'messages'    => $messages,
            'otherUserId' => $otherUserId,
            'isEmployer'  => $isEmployer,
        ]);
    }

    // Send a message
        public function store(Request $request, Job $job)
    {
        $user = $request->user();

        $isEmployer = $user->id === (int) $job->employer_id;
        $didApply   = Application::where('job_id', $job->id)
                        ->where('seeker_id', $user->id)->exists();

        if (! $isEmployer && ! $didApply) {
            abort(403, 'Unauthorized.');
        }

        $data = $request->validate([
            'body'      => ['required', 'string', 'max:5000'],
            'seeker_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        if ($isEmployer) {
            $receiverId = (int) ($data['seeker_id'] ?? 0);
            if (!$receiverId) {
                return back()->withErrors(['body' => 'Missing seeker_id for employer message.']);
            }
        } else {
            $receiverId = (int) $job->employer_id;
        }

        Message::create([
            'job_id'      => $job->id,
            'sender_id'   => $user->id,
            'receiver_id' => $receiverId,
            'body'        => $data['body'],
        ]);

        $params = $isEmployer ? ['seeker_id' => $receiverId] : [];

        return redirect()
            ->route('chat.show', array_merge([$job], $params))
            ->with('status', 'Message sent.');
    }

    // Optional JSON fetch endpoint if you want polling
    public function fetch(Request $request, Job $job)
    {
        $user = $request->user();

        $isEmployer = $user->id === (int) $job->employer_id;
        $didApply   = Application::where('job_id', $job->id)
                        ->where('seeker_id', $user->id)->exists();

        if (! $isEmployer && ! $didApply) {
            abort(403, 'Unauthorized.');
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
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'body' => $m->body,
                'sender_id' => $m->sender_id,
                'receiver_id' => $m->receiver_id,
                'created_at' => optional($m->created_at)->toIso8601String(),
            ]);

        return response()->json(['messages' => $messages]);
    }
}


