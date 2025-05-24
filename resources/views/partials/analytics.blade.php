<!-- Google Analytics tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-Z6S2NMJGYR"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());
    gtag('config', 'G-Z6S2NMJGYR');
</script>

@if (request()->is('success'))
    <script>
        gtag('event', 'install', {
            event_category: 'extension',
            event_label: 'cursor_style_chrome'
        });
    </script>
@endif

@if (request()->is('feedback'))
    <script>
        gtag('event', 'uninstall', {
            event_category: 'extension',
            event_label: 'cursor_style_chrome'
        });
    </script>
@endif
