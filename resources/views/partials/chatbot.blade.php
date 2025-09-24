@if (app()->environment('local') || app()->environment('development') || app()->environment('production'))
  <style>
    /* Launcher (blue round button) */
    #botmanWidgetRoot .botmanWidget--launcher{
      position:fixed !important;
      right:max(16px, env(safe-area-inset-right)) !important;
      bottom:max(16px, env(safe-area-inset-bottom)) !important;
      width:64px !important; height:64px !important;
      border-radius:9999px !important;
      background:#2563eb !important;
      box-shadow:0 10px 24px rgba(0,0,0,.25) !important;
      z-index:2147483000 !important;
      display:flex !important; align-items:center !important; justify-content:center !important;
    }
    /* Panel (opens from bottom, like a sheet) */
    #botmanWidgetRoot .botmanWidget{
      position:fixed !important;
      bottom:0 !important; top:auto !important;
      right:0 !important; left:auto !important;
      width:min(420px,100vw) !important;
      max-height:72vh !important;
      transform:none !important;
      border-radius:12px 12px 0 0 !important;
      overflow:hidden !important;
      z-index:2147483001 !important;
    }
    @media (max-width:640px){
      #botmanWidgetRoot .botmanWidget{ width:100vw !important; left:0 !important; right:0 !important; }
    }
  </style>

  <script>
    (function () {
      const ENVELOPE =
        "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='28' height='28' fill='white'><path d='M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zM4 8l8 5 8-5v10H4V8zm16-2l-8 5-8-5V6h16z'/></svg>";

      function apply() {
        const root = document.getElementById('botmanWidgetRoot');
        if (!root) return;

        // Ensure the widget root is a direct child of <body> (prevents clipping by drawers/containers)
        if (root.parentElement !== document.body) {
          document.body.appendChild(root);
        }

        // Fix launcher (bubble) + lock icon
        const launcher = root.querySelector('.botmanWidget--launcher');
        if (launcher) {
          launcher.innerHTML = ENVELOPE;
          const svg = launcher.querySelector('svg');
          if (svg) svg.style.cssText = 'display:block;margin:0;';
          Object.assign(launcher.style, {
            position:'fixed', right:'16px', bottom:'16px',
            width:'64px', height:'64px', borderRadius:'9999px',
            background:'#2563eb', boxShadow:'0 10px 24px rgba(0,0,0,.25)',
            zIndex:'2147483000', display:'flex', alignItems:'center', justifyContent:'center'
          });
        }

        // Fix panel (bottom sheet)
        const panel = root.querySelector('.botmanWidget');
        if (panel) {
          Object.assign(panel.style, {
            position:'fixed', bottom:'0', top:'auto', right:'0', left:'auto',
            width:'min(420px,100vw)', maxHeight:'72vh', transform:'none',
            borderRadius:'12px 12px 0 0', overflow:'hidden', zIndex:'2147483001'
          });
        }
      }

      // Re-apply whenever the widget injects/updates DOM
      new MutationObserver(apply).observe(document.documentElement, { childList:true, subtree:true });
      window.addEventListener('resize', apply);
      window.addEventListener('orientationchange', apply);
      document.addEventListener('DOMContentLoaded', apply);

      // Botman config
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
    })();
  </script>

  <script src="https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/widget.js" defer></script>
@endif
