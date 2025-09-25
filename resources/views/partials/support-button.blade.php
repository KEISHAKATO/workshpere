<button
    id="ws-chat-toggle"
    type="button"
    aria-label="Open support chat"
    class="fixed z-[2147483000] bottom-4 right-4 sm:bottom-6 sm:right-6 w-14 h-14 rounded-full bg-primary text-primary-content shadow-xl flex items-center justify-center focus:outline-none focus:ring-4 ring-primary/30"
>
    {{-- Envelope icon --}}
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-7 h-7 fill-current">
        <path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zM4 8l8 5 8-5v10H4V8zm16-2l-8 5-8-5V6h16z"/>
    </svg>
</button>

<div
    id="ws-chat-sheet"
    class="fixed inset-x-0 bottom-0 z-[2147483001] pointer-events-none"
    style="contain: layout paint;"
>
    <div
        class="mx-auto w-full sm:max-w-md md:max-w-lg lg:max-w-xl bg-base-100 border-t border-base-300 shadow-2xl rounded-t-2xl translate-y-full transition-transform duration-300 will-change-transform pointer-events-auto"
    >
        {{-- Header (single header – no overlap) --}}
        <div class="h-12 flex items-center justify-between px-4 border-b border-base-300">
            <div class="font-semibold">Worksphere Assistant</div>
            <button id="ws-chat-close" class="btn btn-ghost btn-sm" aria-label="Close chat" type="button">✕</button>
        </div>

        {{-- Chat body (70vh, safe-area aware) --}}
        <div class="h-[70vh] sm:h-[72vh] px-safe flex flex-col" id="ws-chat">
            {{-- Messages --}}
            <div id="ws-messages" class="flex-1 overflow-y-auto py-3 space-y-2 bg-[url('https://ik.imagekit.io/xqjcglzri/contours.png')] bg-[length:600px_600px] bg-repeat rounded-t-2xl">
                {{-- Messages inserted by JS --}}
            </div>

            {{-- Composer --}}
            <form id="ws-composer" class="mt-2 mb-[max(8px,env(safe-area-inset-bottom))] flex gap-2">
                @csrf
                <input
                    id="ws-input"
                    type="text"
                    name="message"
                    class="input input-bordered flex-1"
                    placeholder="Write a message…"
                    autocomplete="off"
                />
                <button class="btn btn-primary" type="submit" aria-label="Send">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M2 21l21-9L2 3v7l15 2-15 2v7z"/></svg>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
(() => {
  const btn   = document.getElementById('ws-chat-toggle');
  const sheet = document.getElementById('ws-chat-sheet');
  const panel = sheet?.querySelector('div > div'); // inner panel
  const close = document.getElementById('ws-chat-close');
  const msgs  = document.getElementById('ws-messages');
  const form  = document.getElementById('ws-composer');
  const input = document.getElementById('ws-input');

  const KEY = 'ws_chat_uid';
  const uid = localStorage.getItem(KEY) || (crypto?.randomUUID?.() || String(Date.now()));
  localStorage.setItem(KEY, uid);

  function scrollToBottom(){ requestAnimationFrame(() => { msgs.scrollTop = msgs.scrollHeight; }); }
  function el(tag, className, html){ const n=document.createElement(tag); if(className) n.className=className; if(html!=null) n.innerHTML=html; return n; }
  function esc(s){ return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;'); }

  function bubble(text, who='bot'){
    const wrap = el('div', who==='bot' ? 'flex justify-start px-2' : 'flex justify-end px-2');
    const inner = el('div',
      who==='bot'
        ? 'max-w-[85%] bg-base-200 text-base-content rounded-2xl px-3 py-2 shadow'
        : 'max-w-[85%] bg-primary text-primary-content rounded-2xl px-3 py-2 shadow',
      esc(text)
    );
    wrap.appendChild(inner);
    msgs.appendChild(wrap);
    scrollToBottom();
    return inner; // return to allow adding buttons underneath
  }

  function buttonsRow(buttons){
    if (!Array.isArray(buttons) || !buttons.length) return null;
    const row = el('div', 'flex flex-wrap gap-2 mt-2');
    buttons.forEach(b => {
      // normalize different button field names
      const label = b?.text ?? b?.title ?? b?.label ?? b?.value ?? 'Select';
      const value = b?.value ?? b?.payload ?? b?.postback ?? b?.text ?? b?.title ?? label;

      const chip = el('button', 'btn btn-sm btn-outline rounded-full normal-case', esc(label));
      chip.type = 'button';
      chip.addEventListener('click', async () => {
        bubble(label, 'me');
        try { await sendToBot(value); } catch(e){}
      });
      row.appendChild(chip);
    });
    return row;
  }

  // Render a single Botman message object in many shapes
  function renderBotmanMessage(m){
    // 1) text + buttons/actions directly on the message
    if (m && (m.text || m.message || m.buttons || m.actions)) {
      const txt = m.text ?? m.message ?? '';
      const anchor = bubble(txt, 'bot');
      const row = buttonsRow(m.buttons ?? m.actions);
      if (row) anchor.after(row);
      return;
    }
    // 2) template attachment
    const att = m?.attachment; const pl = att?.payload;
    if (att && pl && (pl.text || pl.buttons)) {
      const anchor = bubble(pl.text ?? '', 'bot');
      const row = buttonsRow(pl.buttons);
      if (row) anchor.after(row);
      return;
    }
    // 3) plain string
    if (typeof m === 'string') { bubble(m, 'bot'); return; }

    // Unknown shape: show something and log once
    if (!window.__ws_logged_unknown) {
      console.debug('[Worksphere chat] Unknown message shape from server:', m);
      window.__ws_logged_unknown = true;
    }
    const fallback = m?.text ?? m?.message ?? m?.reply ?? '';
    if (fallback) bubble(fallback, 'bot');
  }

  async function sendToBot(message){
    const body = new URLSearchParams();
    body.append('driver', 'web');
    body.append('userId', uid);
    body.append('message', message);
    body.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}');

    const res = await fetch(@json(route('botman.handle')), {
      method:'POST',
      headers:{ 'X-Requested-With':'XMLHttpRequest' },
      body
    });

    // Try JSON; if not JSON, fall back to text
    let payload, textFallback=null;
    try { payload = await res.json(); } catch { textFallback = await res.text(); }

    // Normalize top-level: messages array OR single fields
    if (payload && Array.isArray(payload.messages)) {
      payload.messages.forEach(renderBotmanMessage);
      return;
    }
    // Some drivers send { reply: "...", buttons:[...] }
    if (payload && (payload.reply || payload.text || payload.message || payload.buttons || payload.actions)) {
      const anchor = bubble(payload.reply ?? payload.text ?? payload.message ?? '', 'bot');
      const row = buttonsRow(payload.buttons ?? payload.actions);
      if (row) anchor.after(row);
      return;
    }
    // Text fallback
    if (textFallback) { bubble(textFallback, 'bot'); }
  }

  function openSheet(){
    panel?.classList.remove('translate-y-full');
    sheet.style.pointerEvents = 'auto';
    if (!sessionStorage.getItem('ws_chat_greeted')) {
      setTimeout(() => { sendToBot('hello'); }, 120);
      sessionStorage.setItem('ws_chat_greeted', '1');
    }
    setTimeout(scrollToBottom, 160);
  }
  function closeSheet(){
    panel?.classList.add('translate-y-full');
    sheet.style.pointerEvents = 'none';
  }
  btn?.addEventListener('click', openSheet);
  close?.addEventListener('click', closeSheet);
  window.addEventListener('keydown', e => { if (e.key === 'Escape') closeSheet(); });
  sheet?.addEventListener('click', e => { if (e.target === sheet) closeSheet(); });

  form?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const text = input.value.trim();
    if (!text) return;
    bubble(text, 'me');
    input.value = '';
    try { await sendToBot(text); } catch { bubble('Sorry, something went wrong.', 'bot'); }
  });
})();
</script>


