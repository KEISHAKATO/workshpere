@props([
    'cityName'   => 'location_city',
    'countyName' => 'location_county',
    'latName'    => 'lat',
    'lngName'    => 'lng',

    'city'   => null,
    'county' => null,
    'lat'    => null,
    'lng'    => null,

    'label'  => 'Location',
])

@php
    // Unique IDs so labels are associated correctly (fixes the a11y warning)
    $uid      = uniqid('loc_');
    $idSearch = $uid.'_search';
    $idCity   = $uid.'_city';
    $idCounty = $uid.'_county';
    $idLat    = $uid.'_lat';
    $idLng    = $uid.'_lng';
@endphp

<div class="space-y-3" data-place-picker>
    <label for="{{ $idSearch }}" class="block text-sm font-medium text-gray-700">{{ $label }}</label>

    {{-- This is the only field users type into --}}
    <input
        id="{{ $idSearch }}"
        type="text"
        class="block w-full rounded-md border-gray-300"
        placeholder="Start typing a place, city, county, address…"
        value="{{ old($cityName, $city) ?: old($countyName, $county) }}"
        data-search
    />

    {{-- These are submitted with the form --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
            <label for="{{ $idCity }}" class="block text-xs text-gray-500">City</label>
            <input id="{{ $idCity }}" name="{{ $cityName }}" type="text"
                   class="block w-full rounded-md border-gray-300"
                   value="{{ old($cityName, $city) }}"
                   data-city />
        </div>
        <div>
            <label for="{{ $idCounty }}" class="block text-xs text-gray-500">County / Region</label>
            <input id="{{ $idCounty }}" name="{{ $countyName }}" type="text"
                   class="block w-full rounded-md border-gray-300"
                   value="{{ old($countyName, $county) }}"
                   data-county />
        </div>
        <div>
            <label for="{{ $idLat }}" class="block text-xs text-gray-500">Latitude</label>
            <input id="{{ $idLat }}" name="{{ $latName }}" type="text" inputmode="decimal"
                   class="block w-full rounded-md border-gray-300"
                   value="{{ old($latName, $lat) }}"
                   data-lat />
        </div>
        <div>
            <label for="{{ $idLng }}" class="block text-xs text-gray-500">Longitude</label>
            <input id="{{ $idLng }}" name="{{ $lngName }}" type="text" inputmode="decimal"
                   class="block w-full rounded-md border-gray-300"
                   value="{{ old($lngName, $lng) }}"
                   data-lng />
        </div>
    </div>
</div>

@push('scripts')
<script>
/**
 * Make the callback global so the Google loader can call it,
 * and guard to avoid re-initializing if the script executes twice.
 */
window.initAllPlacePickers = function () {
    if (window.__placePickersInit) return;
    window.__placePickersInit = true;

    if (!window.google || !google.maps || !google.maps.places) {
        console.warn('Google Maps Places not available');
        return;
    }

    document.querySelectorAll('[data-place-picker]').forEach(root => {
        const search   = root.querySelector('[data-search]');
        const cityEl   = root.querySelector('[data-city]');
        const countyEl = root.querySelector('[data-county]');
        const latEl    = root.querySelector('[data-lat]');
        const lngEl    = root.querySelector('[data-lng]');

        if (!search) return;

        const ac = new google.maps.places.Autocomplete(search, {
            fields: ['address_components', 'geometry', 'name'],
            // If you want addresses too, change to ['geocode']
            types: ['(regions)'],
            // Restrict to a country: componentRestrictions: { country: ['ke'] }
        });

        ac.addListener('place_changed', () => {
            const place = ac.getPlace();
            if (!place) return;

            const loc = place.geometry?.location;
            if (loc) {
                latEl.value = typeof loc.lat === 'function' ? loc.lat() : loc.lat;
                lngEl.value = typeof loc.lng === 'function' ? loc.lng() : loc.lng;
            }

            const comps = place.address_components || [];
            cityEl.value = pick(comps, [
                'locality',
                'sublocality',
                'administrative_area_level_3'
            ]) || cityEl.value;

            countyEl.value = pick(comps, [
                'administrative_area_level_2',
                'administrative_area_level_1'
            ]) || countyEl.value;

            if (!search.value) {
                search.value = [cityEl.value, countyEl.value].filter(Boolean).join(', ');
            }
        });

        function pick(components, types) {
            for (const t of types) {
                const hit = components.find(c => (c.types || []).includes(t));
                if (hit) return hit.long_name;
            }
            return '';
        }
    });
};
</script>
@endpush

