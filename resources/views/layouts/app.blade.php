@php
    function build_version() {
        return '?v=5'; // ← тут змінюй версію вручну
    }
@endphp
<!DOCTYPE html>
<html lang="{{ Config::get('app.locale') }}">

<head>
    <title>@yield('title')</title>
    <meta http-equiv="Content-Language" content="{{ app()->getLocale() }}" />
    <meta name="description" content="@yield('descr')" />
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    @yield('page_meta')

    <!-- Google Analytics tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-Z6S2NMJGYR"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());

        gtag('config', 'G-Z6S2NMJGYR');
    </script>
        
    <link rel="alternate" hreflang="x-default" href="https://cursor.style/{{ltrim(Request::path(), '/')}}" />
    <link rel="alternate" hreflang="am" href="https://am.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="ar" href="https://ar.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="bg" href="https://bg.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="bn" href="https://bn.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="ca" href="https://ca.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="cs" href="https://cs.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="da" href="https://da.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="de" href="https://de.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="el" href="https://el.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="es" href="https://es.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="et" href="https://et.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="fa" href="https://fa.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="fi" href="https://fi.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="tl" href="https://fil.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="fr" href="https://fr.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="gu" href="https://gu.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="he" href="https://he.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="hi" href="https://hi.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="hr" href="https://hr.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="hu" href="https://hu.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="id" href="https://id.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="it" href="https://it.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="ja" href="https://ja.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="kn" href="https://kn.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="ko" href="https://ko.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="lt" href="https://lt.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="lv" href="https://lv.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="ml" href="https://ml.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="mr" href="https://mr.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="ms" href="https://ms.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="nl" href="https://nl.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="no" href="https://no.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="pl" href="https://pl.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="pt" href="https://pt.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="ro" href="https://ro.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="ru" href="https://ru.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="sk" href="https://sk.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="sl" href="https://sl.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="sr" href="https://sr.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="sv" href="https://sv.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="sw" href="https://sw.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="ta" href="https://ta.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="te" href="https://te.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="th" href="https://th.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="tr" href="https://tr.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="uk" href="https://uk.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="vi" href="https://vi.cursor.style/{{ ltrim(Request::path(), '/') }}" />
    <link rel="alternate" hreflang="zh" href="https://zh.cursor.style/{{ ltrim(Request::path(), '/') }}" />


    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="stylesheet" href="{{ secure_asset('css/mycollection.css') }}{{ build_version() }}" />
    <link rel="stylesheet" href="{{ secure_asset('css/main_updated.css') }}{{ build_version() }}" />
    <link rel="stylesheet" href="{{ secure_asset('/css/chat.css')}}{{ build_version() }}" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Round" />
    <link href="https://fonts.googleapis.com/css2?family=Shantell+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Shantell+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <link rel="canonical" href="https://{{ Request::host() }}{{ Request::getRequestUri() }}" />

    @yield('lib_top')
    <script async src="{{ secure_asset('/js/lang.v2.js')}}{{ build_version() }}"></script>
    <script src="{{ secure_asset('/js/init.js')}}{{ build_version() }}"></script>

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





</head>

<body>

    <div id="ovelay" class="overlay" style="display:none"></div>

    <div class="main-container">
        <div id="votepls" style="display:none">
            <div class="star_container">
                <div class="star_main_title">@lang('messages.vote_1')</div>
                <div class="star_title">@lang('messages.vote_2')</div>
                <a href="#" id="star_img_container">
                    <img style="width: 256px; margin: 20px;" src="/images/star.gif">
                </a>
                <button class="cursor-button" data-label="@lang('messages.vote_3')" id="star_vote"></button>
                <div class="maybelater" id="maybelater">@lang('messages.vote_4')</div>
            </div>
            <div class="dontshow">
                <input type="checkbox" id="dontshow"
                    style="vertical-align:middle; display: inline-block;-webkit-appearance: checkbox;">
                <label for="dontshow" id="css_cs_cursor">@lang('messages.vote_5')</label>
            </div>
            <div class="star_close" id="star_close_btn">
            </div>
        </div>


        <div class="header">
            <div class="container">
                <a class="logo" href="/"><img src="/images/logo.png" alt="@lang('messages.cursor_style')"
                        title="@lang('messages.cursor_style_logo_title')" /></a>

                <div class="nav">
                    <ul>
                        <li><a class="top_menu_link" href="/">@lang('messages.menu_main')</a></li>
                        <li><a class="top_menu_link" href="/howto">@lang('messages.menu_how_to_use')</a></li>
                        <li><a class="top_menu_link" href="/collections">@lang('messages.menu_collection')</a></li>
                        <li><a class="top_menu_link" href="/contact">@lang('messages.menu_contact')</a></li>
                    </ul>
                    <div id="lang_selector" class="language-dropdown">
                        <label for="toggle" class="lang-flag" title="Click to select the language">
                            <span class="flag"></span>
                        </label>
                        <ul class="lang-list">
                            <li class="lang lang-en" id="lang-en" style="margin: 0;" title="EN">
                                <span class="flag"></span>
                            </li>
                            <li class="lang lang-ru" id="lang-ru" style="margin: 0;" title="RU">
                                <span class="flag"></span>
                            </li>
                            <li class="lang lang-es" id="lang-es" style="margin: 0;" title="ES">
                                <span class="flag"></span>
                            </li>
                        </ul>
                    </div>
                </div>


                <div class="burger">
                    <div class="burger__line"></div>
                    <div class="burger__line"></div>
                    <div class="burger__line"></div>
                </div>
            </div>
        </div>
        <div class="mobile__nav">
            <div class="mobile__nav__panel">
                <div class="close"></div><a class="nav__link active" href="/">@lang('messages.menu_main')</a>
                <a class="nav__link" href="/howto">@lang('messages.menu_how_to_use')</a>
                <a class="nav__link" href="/collections">@lang('messages.menu_collection')</a>
                <a class="nav__link" href="/contact">@lang('messages.menu_contact')</a>
            </div>
        </div>


        @yield('main')
        <div class="footer">
            <div class="container">
                <div class="copyright">
                    © 2019–{{ now()->year }} 🖱️ Cursor.Style — by <a href="https://www.tiktok.com/@wonchoe"
                        target="_blank">Oleksii Semeniuk</a>
                </div>
                <div class="footer__links">
                    <div><a class="downlink" href="/terms">@lang('messages.footer_term_of_use')</a></div>
                    <div><a class="downlink" href="/privacy">@lang('messages.footer_policy')</a></div>
                    <div><a class="downlink" href="/cookie-policy">@lang('messages.footer_cookies_policy')</a></div>
                </div>
            </div>
        </div>

        <div id="wrapfabtest">
            <div class="adBanner">
            </div>
        </div>

    </div>



    @yield('lib_bottom')
    @yield('js')
    <script src="/js/chat.js{{ build_version() }}"></script>
    <script src="/js/vote.js{{ build_version() }}"></script>
</body>

</html>