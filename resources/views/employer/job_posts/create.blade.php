<x-app-layout>
    <div class="max-w-3xl mx-auto p-6">
        <h1 class="text-2xl font-semibold mb-4">Create Job</h1>
        <form method="post" action="{{ route('employer.job_posts.store') }}" class="space-y-3">
            @csrf
            <input name="title" class="w-full border p-2" placeholder="Title" required>
            <textarea name="description" class="w-full border p-2" placeholder="Description" required></textarea>
            <input name="category" class="w-full border p-2" placeholder="Category (optional)">
            <select name="job_type" class="w-full border p-2" required>
                @foreach(['full_time','part_time','gig','contract'] as $t)
                    <option value="{{ $t }}">{{ str_replace('_',' ', $t) }}</option>
                @endforeach
            </select>
            <div class="grid grid-cols-2 gap-2">
                <input name="pay_min" class="border p-2" placeholder="Pay min (KES)">
                <input name="pay_max" class="border p-2" placeholder="Pay max (KES)">
            </div>
            <input name="currency" class="w-full border p-2" value="KES">
            <div class="grid grid-cols-2 gap-2">
                <input name="location_city" class="border p-2" placeholder="City">
                <input name="location_county" class="border p-2" placeholder="County">
            </div>
            <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
        </form>
    </div>
</x-app-layout>
