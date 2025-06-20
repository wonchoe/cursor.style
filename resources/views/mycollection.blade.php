@extends('layouts.app')

@section('head_meta')
    <title>@lang('feedback.title')</title>
    <meta name="description" content="@lang('feedback.descr')" />
    <meta property="og:title" content="@lang('feedback.title')" />
    <meta property="og:image:width" content="700" />
    <meta property="og:image:height" content="350" />
    <meta property="og:description" content="@lang('feedback.descr')" />
    <meta property="og:image" content="{{ asset_cdn('images/img.jpg') }}" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="@cursor.style" />
    <meta name="twitter:title" content="@lang('feedback.title')" />
    <meta name="twitter:description" content="@lang('feedback.descr')" />
    <meta name="twitter:image" content="{{ asset_cdn('images/img.jpg') }}" />
@endsection

@push('styles')
    <link rel="icon" type="image/png" href="{{ asset_cdn('images/favicon.png') }}" />
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Round" />
    <link rel="stylesheet" href="https://cursor.style/css/switcher.css{{ build_version() }}">
    <link rel="stylesheet" href="https://cursor.style/css/fontpicker.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css">
    <script src="https://cursor.style/js/fontpicker.iife.js"></script>
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>
    <link rel="stylesheet" href="{{ asset_ver('css/mycollection.css') }}" />
@endpush


<script id="category-data" type="application/json">
{!! json_encode($mycollection->pluck('trans', 'id')) !!}
</script>

@section('main')
    <div class="mycollection-wrapper">
        <div class="left">
            <div class="gads-wrapper google-sides">
                <div class="googleads">
                    <!-- google ads here -->
                </div>
            </div>
        </div>

        <div class="mycollection" id="mycollection">

            <div class="gads-wrapper infeed" style="width:100%">
                <div class="googleads" style="width:100%">
                    @include('ads.google.infeed')
                </div>
            </div>

            <div class="chat-popup" id="chatPopup">
                <input type="text" id="usernameInput" data-lang-placeholder="enter_your_nickname"
                    placeholder="@lang('messages.enter_your_nickname')">
                <button id="loginButton" data-lang-tag="join_chat">@lang('messages.join_chat')</button>
                <div id="loginMessage" class="system-message"></div>
            </div>

            <div class="my-collection-header">
                <div class="active-cursor">
                    <div class="currentcursorbox">
                        <img src="/images/nocursor.png" id="nocursor-preview">
                        <img src="/images/nopointer.png" id="nopointer-preview">
                    </div>
                </div>

                <div class="my-collection-switcher">
                    <div class="content">
                        <div class="switcher-label" data-lang-tag="custom_cursor">@lang('messages.custom_cursor')</div>
                        <label class="ui-toggle-switch">
                            <input type="checkbox" class="mycursoronoff" id="customcursor">
                            <span class="ui-toggle-slider"></span>
                        </label>
                    </div>
                    <div class="content cursos-assistance">
                        <div class="switcher-label" data-lang-tag="cursor_assistance">@lang('messages.cursor_assistance')</div>
                        <label class="ui-toggle-switch">
                            <input type="checkbox" class="mycursoronoff" id="cursorassistance">
                            <span class="ui-toggle-slider"></span>
                        </label>
                    </div>
                    <div class="content">
                        <div class="switcher-label line-effect" data-lang-tag="cursor_effect">@lang('messages.cursor_trail')</div>
                        <label class="ui-toggle-switch">
                            <input type="checkbox" class="mycursoronoff" id="cursoreffect">
                            <span class="ui-toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            <div id="iconInput"></div>

            <div class="params">
                <div class="slider-container">
                    <h2 data-lang-tag="cursor_size">@lang('messages.cursor_size')</h2>
                    <input type="range" min="16" max="128" value="50" class="slider-1" id="slider1">
                    <div class="value-display" data-lang-tag="cursor_size_value">
                        @lang('messages.cursor_size_value') <span id="value1">72</span>
                    </div>
                </div>
            </div>

            <div class="effects-container-wrapper">
                <h2 data-lang-tag="cursor_trail">@lang('messages.cursor_trail')</h2>                
                <div class="effects-container" id="effects-container">
                    <div class="trail-lock-overlay">
                        <div class="emoji" style="font-size:40px">🔒</div>
                        <img width="60px" src="/images/switch2.gif">
                    </div>
                    <h2 class="default_cursor_cs" data-lang-tag="trail_effect">@lang('messages.cursor_trail_effect')</h2>
                </div>
            </div>

            <div id="mycursors"></div>

            <div class="gads-wrapper infeed" style="width:100%">
                <div class="googleads" style="width:100%">
                    @include('ads.google.infeed')
                </div>
            </div>
        </div>
        <div class="right">
            <div class="gads-wrapper google-sides">
                <div class="googleads">
                    <!-- google ads here -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js"></script>
    <script src="https://cursor.style/js/mycollection.js{{ build_version() }}"></script>
    <script src="https://cursor.style/js/tabler/tablerClass.js"></script>
@endpush
