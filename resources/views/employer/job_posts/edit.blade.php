<x-app-layout>
    <div class="max-w-3xl mx-auto p-6">
        <h1 class="text-2xl font-semibold mb-4">Edit Job</h1>
        <form method="post" action="{{ route('employer.job_posts.update',$job) }}" class="space-y-3">
            @csrf @method('PUT')
            <input name="title" class="w-full border p-2" value="{{ old('title',$job->title) }}" required>
            <textarea name="description" class="w-full border p-2" required>{{ old('description',$job->description) }}</textarea>
            <input name="category" class="w-full border p-2" value="{{ old('category',$job->category) }}">
            <select name="job_type" class="w-full border p-2" required>
                @foreach(['full_time','part_time','gig','contract'] as $t)
                    <option value="{{ $t }}" @selected(old('job_type',$job->job_type)===$t)>{{ str_replace('_',' ', $t) }}</option>
                @endforeach
            </select>
            <div class="grid grid-cols-2 gap-2">
                <input name="pay_min" class="border p-2" value="{{ old('pay_min',$job->pay_min) }}">
                <input name="pay_max" class="border p-2" value="{{ old('pay_max',$job->pay_max) }}">
            </div>
            <input name="currency" class="w-full border p-2" value="{{ old('currency',$job->currency) }}">
            <div class="grid grid-cols-2 gap-2">
                <input name="location_city" class="border p-2" value="{{ old('location_city',$job->location_city) }}">
                <input name="location_county" class="border p-2" value="{{ old('location_county',$job->location_county) }}">
            </div>
            <select name="status" class="w-full border p-2" required>
                @foreach(['open','closed','paused'] as $s)
                    <option value="{{ $s }}" @selected(old('status',$job->status)===$s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
        </form>
    </div>
</x-app-layout>
