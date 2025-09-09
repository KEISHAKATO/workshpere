

(function () {
  const API_KEY = window.WORKSPHERE_GOOGLE_KEY;
  if (!API_KEY) {
    console.warn('Places autocomplete: missing WORKSPHERE_GOOGLE_KEY');
    return;
  }

  /** Minimal styling for the dropdown */
  const baseStyles = `
  .ws-places-dropdown{position:absolute; z-index: 50; background:#fff; border:1px solid #e5e7eb; border-radius:.5rem; box-shadow:0 10px 15px -3px rgba(0,0,0,.1),0 4px 6px -2px rgba(0,0,0,.05); max-height:16rem; overflow:auto;}
  .ws-places-item{padding:.5rem .75rem; cursor:pointer;}
  .ws-places-item:hover,.ws-places-item[aria-selected="true"]{background:#f3f4f6;}
  `;
  const style = document.createElement('style');
  style.textContent = baseStyles;
  document.head.appendChild(style);

  // Utilities
  const sleep = (ms) => new Promise(r => setTimeout(r, ms));
  const debounce = (fn, ms=200) => {
    let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
  };
  const uuid = () => crypto.randomUUID ? crypto.randomUUID() :
    'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
      const r = Math.random()*16|0, v = c === 'x' ? r : (r&0x3|0x8); return v.toString(16);
    });

  // Single-page session token improves quality/billing
  const sessionToken = uuid();

  // Fetch predictions
  async function fetchPredictions(input, regionHint) {
    if (!input || input.trim().length < 2) return [];

    const body = {
      input: input.trim(),
      // Nudge for Kenyan addresses; adjust as you need
      includedRegionCodes: regionHint ? [regionHint] : ['KE'],
      // Location-only predictions (no POIs)
      includedPrimaryTypes: ['locality','administrative_area_level_2','administrative_area_level_1','country'],
      sessionToken
    };

    const res = await fetch('https://places.googleapis.com/v1/places:autocomplete', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Goog-Api-Key': API_KEY,
        // We’ll parse displayName, placeId, etc.
        'X-Goog-FieldMask': 'suggestions.placePrediction.placeId,suggestions.placePrediction.text'
      },
      body: JSON.stringify(body)
    });

    if (!res.ok) {
      console.warn('Places autocomplete error', await res.text());
      return [];
    }
    const data = await res.json();
    // Normalize text as plain string (strip formatting runs)
    return (data.suggestions || []).map(s => {
      const text = (s.placePrediction?.text?.text) || '';
      return { label: text, placeId: s.placePrediction?.placeId };
    }).filter(p => p.placeId && p.label);
  }

  // Fetch details for selected place (to get lat/lng + components)
  async function fetchPlaceDetails(placeId) {
    const res = await fetch(`https://places.googleapis.com/v1/places/${encodeURIComponent(placeId)}?languageCode=en`, {
      method: 'GET',
      headers: {
        'X-Goog-Api-Key': API_KEY,
        // Specify only what we need for billing/perf
        'X-Goog-FieldMask': [
          'id',
          'displayName',
          'formattedAddress',
          'location',
          'addressComponents'
        ].join(',')
      },
    });
    if (!res.ok) {
      console.warn('Place details error', await res.text());
      return null;
    }
    return res.json();
  }

  function ensureDropdown(el) {
    let dd = el._wsDropdown;
    if (dd) return dd;
    dd = document.createElement('div');
    dd.className = 'ws-places-dropdown';
    dd.style.display = 'none';
    // Position it under the input
    const rect = el.getBoundingClientRect();
    dd.style.minWidth = `${rect.width}px`;
    // Use an anchor wrapper for better positioning in many layouts
    const wrap = document.createElement('div');
    wrap.style.position = 'relative';
    el.parentNode.insertBefore(wrap, el);
    wrap.appendChild(el);
    wrap.appendChild(dd);
    el._wsDropdown = dd;
    return dd;
  }

  function clearDropdown(dd) {
    dd.innerHTML = '';
    dd.style.display = 'none';
  }

  function showDropdown(el, items, onPick) {
    const dd = ensureDropdown(el);
    dd.innerHTML = '';
    if (!items.length) {
      clearDropdown(dd);
      return;
    }

    items.forEach((it, idx) => {
      const div = document.createElement('div');
      div.className = 'ws-places-item';
      div.textContent = it.label;
      div.setAttribute('role', 'option');
      div.setAttribute('aria-selected', idx === 0 ? 'true' : 'false');
      div.addEventListener('mousedown', e => {
        e.preventDefault(); // prevent input blur before click
        onPick(it);
      });
      dd.appendChild(div);
    });
    dd.style.display = 'block';
  }

  function wireOne(input) {
    const dd = ensureDropdown(input);
    const region = input.getAttribute('data-region') || 'KE';
    const citySel   = input.getAttribute('data-target-city');
    const countySel = input.getAttribute('data-target-county');
    const latSel    = input.getAttribute('data-target-lat');
    const lngSel    = input.getAttribute('data-target-lng');

    const cityEl   = citySel   ? document.querySelector(citySel)   : null;
    const countyEl = countySel ? document.querySelector(countySel) : null;
    const latEl    = latSel    ? document.querySelector(latSel)    : null;
    const lngEl    = lngSel    ? document.querySelector(lngSel)    : null;

    const doQuery = debounce(async (q) => {
      const list = await fetchPredictions(q, region);
      showDropdown(input, list, async (picked) => {
        input.value = picked.label;
        clearDropdown(dd);

        // Pull details → fill city/county/lat/lng
        const det = await fetchPlaceDetails(picked.placeId);
        if (!det) return;

        // Lat/Lng
        if (det.location && latEl && lngEl) {
          latEl.value = det.location.latitude ?? '';
          lngEl.value = det.location.longitude ?? '';
        }

        // Address components to City/County (best-effort)
        const comps = det.addressComponents || [];
        const getComp = (type) =>
          comps.find(c => (c.types || []).includes(type))?.longText;

        // locality = city/town; admin_area_level_2 ≈ county in KE
        const lc = getComp('locality');
        const cty = getComp('administrative_area_level_2') || getComp('administrative_area_level_1');

        if (cityEl && lc)   cityEl.value = lc;
        if (countyEl && cty) countyEl.value = cty;
      });
    }, 220);

    input.addEventListener('input', (e) => doQuery(e.target.value));
    input.addEventListener('blur', () => setTimeout(() => clearDropdown(dd), 120));
  }

  function boot() {
    document.querySelectorAll('input[data-places="search"]').forEach(wireOne);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
