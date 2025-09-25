@if (app()->environment('local') || app()->environment('development') || app()->environment('production'))
<style>
  #botmanWidgetRoot .botmanWidget--launcher{
    position:fixed !important;
    right:max(16px, env(safe-area-inset-right)) !important;
    bottom:max(16px, env(safe-area-inset-bottom)) !important;
    left:auto !important; top:auto !important;
    width:64px !important; height:64px !important;
    border-radius:9999px !important;
    display:flex !important; align-items:center !important; justify-content:center !important;
    padding:0 !important; margin:0 !important; transform:none !important;
    background:#2563eb !important;
    box-shadow:0 10px 24px rgba(0,0,0,.25) !important;
    z-index:2147483000 !important;
  }
  #botmanWidgetRoot .botmanWidget--launcher svg { display:none !important; }
  #botmanWidgetRoot .botmanWidget--launcher::before{
    content:"";
    display:block; width:28px; height:28px; margin:18px auto;
    background: center / contain no-repeat
      url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white'><path d='M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zM4 8l8 5 8-5v10H4V8zm16-2l-8 5-8-5V6h16z'/></svg>");
  }

  #webchat{
    position:fixed !important;
    right:max(16px, env(safe-area-inset-right)) !important;
    bottom:max(16px, env(safe-area-inset-bottom)) !important;
    left:auto !important; top:auto !important;
    width:0 !important; height:0 !important;
    display:none !important;
    pointer-events:none !important;  /* <<< key */
    z-index:2147483001 !important;
    margin:0 !important; border:0 !important; overflow:hidden !important; transform:none !important;
  }

  /* When opened: bottom sheet */
  #webchat.opened,
  #webchat[style*="display: block"]{
    display:block !important;
    pointer-events:auto !important;
    width:min(420px, 100vw) !important;
    height:72vh !important; max-height:72vh !important;
    right:max(16px, env(safe-area-inset-right)) !important;
    bottom:max(16px, env(safe-area-inset-bottom)) !important;
    left:auto !important; top:auto !important;
    border-radius:12px 12px 0 0 !important;
    overflow:hidden !important; transform:none !important;
  }

  /* Mobile: full-width bottom sheet between safe-area insets */
  @media (max-width:640px){
    #webchat.opened,
    #webchat[style*="display: block"]{
      left:max(16px, env(safe-area-inset-left)) !important;
      right:max(16px, env(safe-area-inset-right)) !important;
      width:auto !important;
    }
  }

  /* Make sure the iframe fills the panel */
  #webchat iframe{
    display:block !important; width:100% !important; height:100% !important; border:0 !important;
  }

  /* Close button placement inside the panel */
  #botmanWidgetRoot .botman-close-button{ right:8px !important; top:8px !important; }
</style>

<script>
(function () {
  // Botman config (iframe internals still pick these up)
  window.botmanWidget = {
    title: 'Worksphere Assistant',
    introMessage: '👋 Hi! I can help with navigation, FAQs and job/apply steps.',
    aboutText: 'Worksphere Support',
    bubbleBackground: '#2563eb',
    mainColor: '#2563eb',
    placeholderText: 'Type a message…',
    chatServer: @json(route('botman.handle')),
    desktopWidth: 360,
    desktopHeight: 520,
    mobileHeight: 460
  };

  const ENVELOPE = "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='28' height='28' fill='white'><path d='M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zM4 8l8 5 8-5v10H4V8zm16-2l-8 5-8-5V6h16z'/></svg>";

  function clampWidget() {
    const root = document.getElementById('botmanWidgetRoot');
    if (!root) return;

    // Ensure not clipped by any parent (drawers etc.)
    if (root.parentElement !== document.body) document.body.appendChild(root);

    const launcher = root.querySelector('.botmanWidget--launcher');
    if (launcher) {
      // lock icon in case the widget swaps it on route change
      if (!launcher.querySelector('svg')) launcher.innerHTML = ENVELOPE;
      Object.assign(launcher.style, {
        position:'fixed', right:'16px', bottom:'16px', left:'auto', top:'auto',
        width:'64px', height:'64px', borderRadius:'9999px',
        background:'#2563eb', boxShadow:'0 10px 24px rgba(0,0,0,.25)',
        zIndex:'2147483000', display:'flex', alignItems:'center', justifyContent:'center'
      });
    }

    const wc = document.getElementById('webchat');
    if (wc) {
      const isOpen = wc.classList.contains('opened') || (wc.style.display && wc.style.display !== 'none');
      if (!isOpen) {
        // absolutely ensure it cannot intercept touches while closed
        wc.style.pointerEvents = 'none';
        wc.style.width = '0';
        wc.style.height = '0';
        wc.style.display = 'none';
        wc.style.right = '16px';
        wc.style.bottom = '16px';
        wc.style.left = 'auto';
        wc.style.top = 'auto';
      } else {
        wc.style.pointerEvents = 'auto';
        wc.style.display = 'block';
        wc.style.width = 'min(420px, 100vw)';
        wc.style.height = '72vh';
        wc.style.right = '16px';
        wc.style.bottom = '16px';
        wc.style.left = 'auto';
        wc.style.top = 'auto';
        wc.style.borderRadius = '12px 12px 0 0';
        wc.style.overflow = 'hidden';
        wc.style.zIndex = '2147483001';
      }
    }
  }

  const mo = new MutationObserver(clampWidget);
  mo.observe(document.documentElement, { childList:true, subtree:true, attributes:true });

  let ticks = 0;
  const iv = setInterval(() => { clampWidget(); if (++ticks > 16) clearInterval(iv); }, 500);

  window.addEventListener('load', clampWidget);
  window.addEventListener('resize', clampWidget);
  window.addEventListener('orientationchange', clampWidget);
})();
</script>

<script src="https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/widget.js?v=3" defer></script>
@endif
