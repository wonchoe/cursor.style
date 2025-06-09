<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    @yield('head_meta')

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />

    {!! renderHreflangLinks() !!}

    @include('partials.analytics')

    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>


    <link rel="stylesheet" href="/css/styles.css?v=37" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="/css/critical.css?v=37">
    <link rel="stylesheet" href="{{ asset_ver('css/chat.css') }}" media="print" onload="this.media='all'" />
    <link href="https://fonts.googleapis.com/css2?family=Shantell+Sans&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <link rel="canonical" href="https://{{ Request::host() }}{{ Request::getRequestUri() }}" />

    @stack('styles')
    <script>
        window.assetCdnBase = "{{ rtrim(config('app.asset_cdn', 'https://cursor.style'), '/') }}";
    </script>
    <script async src="{{ asset_ver('js/lang.v2.js') }}"></script>
    <script src="{{ asset_ver('js/init.js') }}" defer></script>

    <style>

    </style>
</head>

<body>
    <div id="ovelay" class="overlay" style="display:none"></div>


    <div class="master-container">
        <div class="sidebar-left">
            <div class="gads-wrapper">
                @include('ads.google.infeed')
            </div>
        </div>

        <div class="main-container">
            <header>
                @include('partials.header')
            </header>
            <main>
                @yield('main')
            </main>
        </div>

        <div class="sidebar-right">
            <div class="gads-wrapper">
                @include('ads.google.infeed')
            </div>
        </div>
    </div>


    @include('partials.install-bottom')


    @include('partials.footer')

    @stack('scripts')
    <script src="{{ asset_ver('js/main.js') }}"></script>
    <script src="{{ asset_ver('js/chat.js') }}"></script>
    <script src="{{ asset_ver('js/vote.js') }}"></script>
</body>

</html>