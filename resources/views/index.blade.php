@extends('layouts.app')

@section('head_meta')
    <title>@lang('messages.main_page_title')</title>
    <meta name="description" content="@lang('messages.main_page_descr')" />
    <meta property="og:title" content="@lang('messages.og_title')" />
    <meta property="og:image:width" content="700" />
    <meta property="og:image:height" content="350" />
    <meta property="og:description" content="@lang('messages.main_page_descr')" />
    <meta property="og:image" content="{{ asset_cdn('images/img.jpg') }}" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="@cursor.style" />
    <meta name="twitter:title" content="@lang('messages.og_title')" />
    <meta name="twitter:description" content="@lang('messages.main_page_descr')" />
    <meta name="twitter:image" content="{{ asset_cdn('images/img.jpg') }}" />
@endsection

@push('styles')
    <link rel="icon" type="image/png" href="{{ asset_cdn('images/favicon.png') }}" />
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>
@endpush

@section('main')
    @include('partials.modal-install')

    <div class="main">
        <div class="container">

            <div class="cursor-reward-banner" id="rewardBanner">
                <div id="rewardBlock">
                    <div class="cursor-images">
                        <img src="{{ asset_cdn('/collections/18-startovyj_nabor/2082-cursor-style-cursor.svg') }}" alt="Cursor" class="cursor-img">
                        <img src="{{ asset_cdn('/collections/18-startovyj_nabor/2082-cursor-style-pointer.svg') }}" alt="Pointer" class="cursor-img">
                    </div>
                    <div class="cursor-text" id="rewardText">
                        <h2><img class="gift" src="{{ asset_cdn('images/gift.svg') }}">@lang('messages.reward_title')</h2>
                        <p>@lang('messages.reward_text')</p>
                        <button onclick="startLoading()" class="cursor-btn">@lang('messages.reward_button')</button>
                    </div>
                </div>
                <div class="cursor-loader" id="loader" style="display: none;">
                    <img src="{{ asset_cdn('images/loader.svg') }}" alt="Loading...">
                    <p>⏳ @lang('messages.reward_wait')</p>
                </div>
            </div>

            @include('partials.seo', [
                'shortText' => __('messages.discover_the_magic'),
                'fullText' => __('messages.main_text_1'),
                'id' => 'seoBlock'
            ])

            @include('partials.chat')
            @include('partials.review')

            <div class="tabs_menu">
                <div class="wrapper tabs-wrapper">
                    <nav class="tabs">
                        <a href="/" class="cur_menu {{ request()->is('/') ? 'active' : '' }}">
                            <span class="icon-new"></span>@lang('messages.main_page_menu_2')
                        </a>
                        <a href="/popular" class="cur_menu {{ request()->is('popular') ? 'active' : '' }}">
                            <span class="icon-top"></span>@lang('messages.main_page_menu_1')
                        </a>
                        <a href="/collections" class="cur_menu {{ request()->is('collections') ? 'active' : '' }}">
                            <span class="icon-collection"></span>@lang('messages.main_page_menu_4')
                        </a>
                    </nav>
                </div>
            </div>

            @include('partials.search-input')
            @include('partials.cursor-list', ['cursors' => $cursors])

            @if (isset($cursors) && method_exists($cursors, 'lastPage') && $cursors->lastPage() > 1)
                <div class="pagination-wrapper">
                    <div class="pagination"></div>
                </div>
            @endif

            @include('partials.seo', [
                'shortText' => __('messages.customize_your_browser_with'),
                'fullText' => __('messages.main_text_2'),
                'id' => 'seoBlock2'
            ])
        </div>
    </div>


    @if (isset($cursors) && method_exists($cursors, 'lastPage') && $cursors->lastPage() > 1)
    <script>
        let currentPage = {{ $cursors->currentPage() }};
        let totalPages = {{ $cursors->lastPage() }};
    </script>
    @endif
@endsection

@push('scripts')
    <script src="{{ asset_ver('js/pagination.js') }}"></script>
@endpush