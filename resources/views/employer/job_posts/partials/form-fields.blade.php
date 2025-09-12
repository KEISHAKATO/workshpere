@php
    /** @var \App\Models\Job|null $job */
    $editing = isset($job) && $job;
@endphp

<div class="grid grid-cols-1 gap-5">
    {{-- Title --}}
    <div class="form-control">
        <label class="label"><span class="label-text">Job Title</span></label>
        <input name="title" value="{{ old('title', $job->title ?? '') }}"
               class="input input-bordered" required>
        <x-input-error :messages="$errors->get('title')" class="mt-1" />
    </div>

    {{-- Category --}}
    <div class="form-control">
        <label class="label"><span class="label-text">Category</span></label>
        <input name="category" value="{{ old('category', $job->category ?? '') }}"
               class="input input-bordered" placeholder="e.g. Electrical, Plumbing" />
        <x-input-error :messages="$errors->get('category')" class="mt-1" />
    </div>

    {{-- Job type --}}
    <div class="form-control">
        <label class="label"><span class="label-text">Job Type</span></label>
        <select name="job_type" class="select select-bordered">
            @php
                $types = ['full_time','part_time','contract','temporary','internship','remote'];
                $current = old('job_type', $job->job_type ?? 'full_time');
            @endphp
            @foreach($types as $t)
                <option value="{{ $t }}" @selected($current===$t)>{{ ucfirst(str_replace('_',' ', $t)) }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('job_type')" class="mt-1" />
    </div>

    {{-- Pay --}}
    <div class="grid sm:grid-cols-3 gap-3">
        <div class="form-control">
            <label class="label"><span class="label-text">Currency</span></label>
            <input name="currency" class="input input-bordered w-full"
                   value="{{ old('currency', $job->currency ?? 'KES') }}" />
            <x-input-error :messages="$errors->get('currency')" class="mt-1" />
        </div>
        <div class="form-control">
            <label class="label"><span class="label-text">Pay Min</span></label>
            <input name="pay_min" type="number" class="input input-bordered"
                   value="{{ old('pay_min', $job->pay_min ?? '') }}" />
            <x-input-error :messages="$errors->get('pay_min')" class="mt-1" />
        </div>
        <div class="form-control">
            <label class="label"><span class="label-text">Pay Max</span></label>
            <input name="pay_max" type="number" class="input input-bordered"
                   value="{{ old('pay_max', $job->pay_max ?? '') }}" />
            <x-input-error :messages="$errors->get('pay_max')" class="mt-1" />
        </div>
    </div>

    {{-- Required skills (CSV) --}}
    @php
        $skillsCsv = is_array(($job->required_skills ?? null))
            ? implode(', ', $job->required_skills)
            : ($job->required_skills ?? '');
    @endphp
    <div class="form-control">
        <label class="label"><span class="label-text">Required Skills (comma separated)</span></label>
        <input name="required_skills" class="input input-bordered"
               value="{{ old('required_skills', $skillsCsv) }}" placeholder="e.g. wiring, diagnostics, safety" />
        <x-input-error :messages="$errors->get('required_skills')" class="mt-1" />
        <div id="req-skills-preview" class="mt-2 flex flex-wrap gap-2"></div>
    </div>

    {{-- Description --}}
    <div class="form-control">
        <label class="label"><span class="label-text">Description</span></label>
        <textarea name="description" rows="6" class="textarea textarea-bordered"
                  placeholder="Describe the role, responsibilities and qualifications.">{{ old('description', $job->description ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-1" />
    </div>

    {{-- Location search (Google Places) --}}
    <div class="form-control">
        <label class="label"><span class="label-text">Location (search)</span></label>
        <input type="text" class="input input-bordered"
               placeholder="Start typing a city, county, address…"
               data-gmaps="autocomplete"
               data-country="ke"
               data-target-city="[name='location_city']"
               data-target-county="[name='location_county']"
               data-target-lat="[name='lat']"
               data-target-lng="[name='lng']"
               autocomplete="off">
        <label class="label"><span class="label-text-alt">Pick a suggestion to fill City/County and coordinates.</span></label>
    </div>

    {{-- Hidden coordinates / parts --}}
    <input type="hidden" name="location_city" value="{{ old('location_city', $job->location_city ?? '') }}">
    <input type="hidden" name="location_county" value="{{ old('location_county', $job->location_county ?? '') }}">
    <input type="hidden" name="lat" value="{{ old('lat', $job->lat ?? '') }}">
    <input type="hidden" name="lng" value="{{ old('lng', $job->lng ?? '') }}">

    {{-- Status (only on edit) --}}
    @if($editing)
        <div class="form-control">
            <label class="label"><span class="label-text">Status</span></label>
            @php $status = old('status', $job->status ?? 'open'); @endphp
            <select name="status" class="select select-bordered">
                <option value="open" @selected($status==='open')>Open</option>
                <option value="closed" @selected($status==='closed')>Closed</option>
            </select>
            <x-input-error :messages="$errors->get('status')" class="mt-1" />
        </div>
    @endif
</div>

{{-- small preview for skills --}}
<script>
    (function(){
        const src = document.querySelector('input[name="required_skills"]');
        const box = document.getElementById('req-skills-preview');
        if (!src || !box) return;
        const render = () => {
            const arr = src.value.split(',').map(s => s.trim()).filter(Boolean).slice(0, 24);
            box.innerHTML = arr.map(s => `<span class="badge">${s}</span>`).join(' ');
        };
        src.addEventListener('input', render); render();
    })();
</script>
