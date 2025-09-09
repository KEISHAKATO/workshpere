import { Loader } from '@googlemaps/js-api-loader';

function getEl(sel) {
  return sel ? document.querySelector(sel) : null;
}

function findComponent(components, type) {
  return components?.find(c => (c.types || []).includes(type))?.long_name || '';
}

function initOneInput(google, input) {
  const countryCode = (input.getAttribute('data-country') || '').toLowerCase();

  /** @type {google.maps.places.AutocompleteOptions} */
  const opts = {
    fields: ['place_id', 'formatted_address', 'geometry', 'address_components', 'name'],
    // "geocode" biases to addresses; "(regions)" biases to cities/counties; pick what you prefer:
    // types: ['(regions)'],
    types: ['geocode'],
  };
  if (countryCode) {
    opts.componentRestrictions = { country: [countryCode] };
  }

  const ac = new google.maps.places.Autocomplete(input, opts);

  ac.addListener('place_changed', () => {
    const place = ac.getPlace();
    if (!place) return;

    const cityEl   = getEl(input.getAttribute('data-target-city'));
    const countyEl = getEl(input.getAttribute('data-target-county'));
    const latEl    = getEl(input.getAttribute('data-target-lat'));
    const lngEl    = getEl(input.getAttribute('data-target-lng'));

    // City/town (best-effort)
    const comps = place.address_components || [];
    const city =
      findComponent(comps, 'locality') ||
      findComponent(comps, 'postal_town') ||
      findComponent(comps, 'sublocality') ||
      place.name ||
      '';
    // County (KE often level_2; fallback level_1)
    const county =
      findComponent(comps, 'administrative_area_level_2') ||
      findComponent(comps, 'administrative_area_level_1') ||
      '';

    if (cityEl)   cityEl.value   = city;
    if (countyEl) countyEl.value = county;

    const loc = place.geometry?.location;
    const lat = typeof loc?.lat === 'function' ? loc.lat() : loc?.lat;
    const lng = typeof loc?.lng === 'function' ? loc.lng() : loc?.lng;
    if (latEl && lngEl && typeof lat === 'number' && typeof lng === 'number') {
      latEl.value = lat.toFixed(7);
      lngEl.value = lng.toFixed(7);
    }
  });
}

async function boot() {
  const key = window.WORKSPHERE_GOOGLE_KEY;
  if (!key) {
    console.warn('Google Maps key missing (window.WORKSPHERE_GOOGLE_KEY)');
    return;
  }

  const loader = new Loader({
    apiKey: key,
    version: 'weekly',
    libraries: ['places'],
  });

  try {
    await loader.load();
  } catch (e) {
    console.error('Failed to load Google Maps JS:', e);
    return;
  }

  const google = window.google;
  const inputs = document.querySelectorAll('input[data-gmaps="autocomplete"]');

  // If your markup is injected dynamically later, you can re-run this boot() after render.
  inputs.forEach(input => initOneInput(google, input));
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', boot);
} else {
  boot();
}
