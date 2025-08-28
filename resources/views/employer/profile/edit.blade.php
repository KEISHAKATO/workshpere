<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Company Profile (Employer)</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto p-6 bg-white rounded-xl shadow">
        @if (session('status'))
            <div class="mb-4 p-3 bg-green-50 text-green-700 rounded">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('employer.profile.update') }}" class="space-y-4">
            @csrf @method('PATCH')

            <div>
                <label class="block text-sm font-medium">Company Name</label>
                <input name="company_name" class="w-full border rounded p-2"
                       value="{{ old('company_name', $profile->company_name) }}" required>
                @error('company_name') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Website</label>
                <input type="url" name="website" class="w-full border rounded p-2"
                       value="{{ old('website', $profile->website) }}" placeholder="https://example.com">
                @error('website') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">About</label>
                <textarea name="about" class="w-full border rounded p-2" rows="4"
                          placeholder="Describe your company and the kind of talent you hire.">{{ old('about', $profile->about) }}</textarea>
                @error('about') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium">City</label>
                    <input name="location_city" class="w-full border rounded p-2"
                           value="{{ old('location_city', $profile->location_city) }}">
                    @error('location_city') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">County</label>
                    <input name="location_county" class="w-full border rounded p-2"
                           value="{{ old('location_county', $profile->location_county) }}">
                    @error('location_county') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium">Latitude</label>
                    <input name="lat" class="w-full border rounded p-2"
                           value="{{ old('lat', $profile->lat) }}" placeholder="-1.2921">
                    @error('lat') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium">Longitude</label>
                    <input name="lng" class="w-full border rounded p-2"
                           value="{{ old('lng', $profile->lng) }}" placeholder="36.8219">
                    @error('lng') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
                </div>
            </div>

            <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
        </form>
    </div>
</x-app-layout>
