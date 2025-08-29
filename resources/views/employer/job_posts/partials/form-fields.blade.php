@php
    $v = fn($key, $default='') => old($key, $job?->{$key} ?? $default);
@endphp

<div>
    <label class="block text-sm font-medium">Title</label>
    <input name="title" class="mt-1 w-full border rounded p-2" value="{{ $v('title') }}" required>
    @error('title') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium">Description</label>
    <textarea name="description" class="mt-1 w-full border rounded p-2" rows="6" required>{{ $v('description') }}</textarea>
    @error('description') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium">Category</label>
        <input name="category" class="mt-1 w-full border rounded p-2" value="{{ $v('category') }}">
        @error('category') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Job Type</label>
        <select name="job_type" class="mt-1 w-full border rounded p-2" required>
            @foreach(['full_time','part_time','gig','contract'] as $opt)
                <option value="{{ $opt }}" @selected($v('job_type')===$opt)>{{ ucfirst(str_replace('_',' ',$opt)) }}</option>
            @endforeach
        </select>
        @error('job_type') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div>
        <label class="block text-sm font-medium">Pay Min</label>
        <input type="number" name="pay_min" class="mt-1 w-full border rounded p-2" value="{{ $v('pay_min') }}">
        @error('pay_min') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Pay Max</label>
        <input type="number" name="pay_max" class="mt-1 w-full border rounded p-2" value="{{ $v('pay_max') }}">
        @error('pay_max') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Currency</label>
        <input name="currency" class="mt-1 w-full border rounded p-2" value="{{ $v('currency','KES') }}" maxlength="3">
        @error('currency') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium">City</label>
        <input name="location_city" class="mt-1 w-full border rounded p-2" value="{{ $v('location_city') }}">
        @error('location_city') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium">County</label>
        <input name="location_county" class="mt-1 w-full border rounded p-2" value="{{ $v('location_county') }}">
        @error('location_county') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium">Latitude</label>
        <input name="lat" class="mt-1 w-full border rounded p-2" value="{{ $v('lat') }}">
        @error('lat') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-medium">Longitude</label>
        <input name="lng" class="mt-1 w-full border rounded p-2" value="{{ $v('lng') }}">
        @error('lng') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>
</div>

<div>
    <label class="block text-sm font-medium">Required Skills (comma separated)</label>
    @php
        $skillsCsv = is_array($job?->required_skills ?? null)
            ? implode(', ', $job->required_skills)
            : ($job->required_skills ?? '');
    @endphp
    <input name="required_skills" class="mt-1 w-full border rounded p-2"
           value="{{ old('required_skills', $skillsCsv) }}"
           placeholder="e.g. plumbing, electrical, tiling">
    @error('required_skills') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium">Status</label>
    <select name="status" class="mt-1 w-full border rounded p-2">
        @foreach(['open','paused','closed'] as $st)
            <option value="{{ $st }}" @selected($v('status','open')===$st)>{{ ucfirst($st) }}</option>
        @endforeach
    </select>
    @error('status') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
</div>
