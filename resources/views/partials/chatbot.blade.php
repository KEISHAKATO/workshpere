@if (app()->environment('local') || app()->environment('development') || app()->environment('production'))
    <script>
        if (window.self === window.top) {
            window.botmanWidget = {
                title: 'Worksphere Assistant',
                introMessage: '👋 Hi! I can help with navigation, FAQs and job/apply steps.',
                aboutText: 'Worksphere Support',
                bubbleBackground: '#2563eb',
                mainColor: '#2563eb',
                placeholderText: 'Type a message…',
                chatServer: @json(route('botman.handle')),
                // Optional sizing
                desktopWidth: 360,
                desktopHeight: 520,
                mobileHeight: 460,
            };
        }
    </script>

    {{-- BotMan Web Widget (served from jsdelivr) --}}
    <script src="https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/widget.js" defer></script>
@endif
