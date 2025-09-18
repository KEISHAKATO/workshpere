@props([
    'application', // \App\Models\Application
    'user' => auth()->user(),
])

@php
    $alreadyReviewed = \App\Models\Review::where('job_id', $application->job_id)
        ->where('reviewer_id', $user->id)
        ->exists();

    $isParticipant = ($application->seeker_id === $user->id)
        || (optional($application->job)->employer_id === $user->id);

    $status = (string) $application->status;
    $canReview = $isParticipant && in_array($status, ['completed','accepted'], true) && !$alreadyReviewed;
@endphp

<div class="mt-6">
    @if($errors->has('review'))
        <div class="mb-3 rounded bg-red-50 text-red-800 px-3 py-2 text-sm">
            {{ $errors->first('review') }}
        </div>
    @endif

    @if($canReview)
        <form method="POST" action="{{ route('applications.reviews.store', $application) }}" class="space-y-3">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1" for="rating">Rating</label>
                <select id="rating" name="rating" class="w-full border rounded p-2" required>
                    <option value="">Select rating…</option>
                    @for($i=5; $i>=1; $i--)
                        <option value="{{ $i }}">{{ $i }} ★</option>
                    @endfor
                </select>
                @error('rating') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" for="feedback">Feedback (optional)</label>
                <textarea id="feedback" name="feedback" class="w-full border rounded p-2" rows="4"
                          placeholder="Add any details that may help others trust this user."></textarea>
                @error('feedback') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <button class="bg-blue-600 text-white px-4 py-2 rounded">
                Submit Review
            </button>
        </form>
    @else
        <div class="text-sm text-gray-600">
            @if(!$isParticipant)
                You're not a participant of this application.
            @elseif(!in_array($status, ['completed','accepted']))
                Reviews are available after the job is accepted/completed.
            @elseif($alreadyReviewed)
                You already submitted a review for this job.
            @endif
        </div>
    @endif
</div>
