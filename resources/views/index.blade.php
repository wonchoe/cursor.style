@include('other.build')

@extends('layouts.app')

@section('title')
    @lang('messages.main_page_title')
@endsection

@section('descr')
    @lang('messages.main_page_descr')
@endsection

@section('page_meta')
    <meta property="og:title" content="@lang('messages.og_title')" />
    <meta property="og:image:width" content="700" />
    <meta property="og:image:height" content="350" />
    <meta property="og:description" content="@lang('messages.main_page_descr')" />
    <meta property="og:image" content="https://en.cursor.style/images/img.jpg" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="@cursor.style" />
    <meta name="twitter:title" content="@lang('messages.og_title')" />
    <meta name="twitter:description" content="@lang('messages.main_page_descr')" />
    <meta name="twitter:image" content="https://en.cursor.style/images/img.jpg" />
@endsection


@section('lib_top')
    @yield('css')
    <link rel="icon" type="image/x-icon" href="{{ secure_asset('images/favicon.png') }}" />
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>
@endsection


@section('main')

    @include('layouts.modal')

    <div class="main">
        <div class="container">
            <div class="newblock" id="newblock"></div>

            
            <div class="cursor-reward-banner" id="rewardBanner">
                <div id="rewardBlock">
                    <div class="cursor-images">
                        <img src="/cursors/2082-cursor-style-cursor.svg" alt="Cursor" class="cursor-img">
                        <img src="/pointers/2082-cursor-style-pointer.svg" alt="Pointer" class="cursor-img">
                    </div>
                    <div class="cursor-text" id="rewardText">
                        <h2><img class="gift" src="/images/gift.svg">@lang('messages.reward_title')</h2>
                        <p>@lang('messages.reward_text')</p>
                        <button onclick="startLoading()" class="cursor-btn">@lang('messages.reward_button')</button>
                    </div>
                </div>
                <div class="cursor-loader" id="loader" style="display: none;">
                    <img src="images/loader.svg" alt="Loading...">
                    <p>⏳ @lang('messages.reward_wait')</p>
                </div>
            </div>



            <div class="seo-block collapsed" id="seoBlock">
                @include('layouts.banner')
                <p class="seo-info">                    
                    @lang('messages.discover_the_magic')
                    <button class="seo-toggle" onclick="document.getElementById('seoBlock').classList.remove('collapsed'); this.remove()">@lang('messages.read_more')</button>
                </p>
                <div class="seo-text">
                    <p> 
                        @lang('messages.main_text_1')
                    </p>
                </div>
            </div>

            @include('layouts.chat')

            @include('layouts.review')
                        
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
                    <div class="search-wrapper">
                        <form action="/" method="GET">
                            <span class="icon-search"></span>
                            <input type="text" name="q" id="cs_search" class="search" placeholder="@lang('messages.main_page_search')" aria-label="Search">
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="main__list" id="main_list">
                @forelse($cursors as $key => $cursor)
                    @if ($key > 0 && $key % 16 === 0)
                        <div class="gads-wrapper">
                        <!-- google ads here -->
                            @include('other.google.infeed')
                        <!-- google ads here -->
                        </div>
                    @endif

                    <div class="main__item" data-container-id="{{ $cursor->id }}" onclick="handleItemClick(event, '/details/{{$cursor->id}}-{{$cursor->name_s}}')">          
                        <div class="div_ar_p">
                            <p>@lang('cursors.c_' . $cursor->id) </p>
                        </div>
                        <div class="main__item-img cs_pointer" data-cur-id="{{ $cursor->id }}" cursorshover="true">
                            <img class="cursorimg"
                                style="cursor: url(/cursors/{{ $cursor->id }}-{{ $cursor->name_s }}-cursor.svg) 0 0, auto !important;"
                                src="{{ $cursor->c_file_prev ? asset('assets/' . $cursor->c_file_prev) : asset('cursors/' . $cursor->id . '-' . $cursor->name_s . '-cursor.svg') }}">

                            <img class="cursorimg"
                                style="cursor: url(/pointers/{{ $cursor->id }}-{{ $cursor->name_s }}-pointer.svg) 0 0, auto !important;"
                                src="{{ $cursor->p_file_prev ? asset('assets/' . $cursor->p_file_prev) : asset('pointers/' . $cursor->id . '-' . $cursor->name_s . '-pointer.svg') }}">
                        </div>

                        <span class="downloads-badge"><img src="/images/icons/download.png" style="width: 10px;">{{ number_format($cursor->totalClick + $cursor->todayClick) }}</span>

                        <div class="main__btns">
                                <div class="btn-container">                                    
                                    <span class="pointerevent">
                                        <button class="img-btn" data-action="apply" data-type="stat" data-label="@lang('messages.add_to_collection')"
                                        data-disabled="@lang('messages.add_to_collection_added')" data-cataltname="{{ $cursor->collection->alt_name }}"
                                        data-catbasename_en="@lang('collections.' . $cursor->collection->alt_name)"
                                        data-catbasename_es="@lang('collections.' . $cursor->collection->alt_name)"
                                        data-catbasename="@lang('collections.' . $cursor->collection->alt_name)" data-cat="{{ $cursor->cat }}"
                                        data-id="{{ $cursor->id }}" data-name="@lang('cursors.c_' . $cursor->id)"
                                        data-offset-x="{{ $cursor->offsetX }}" data-offset-x_p="{{ $cursor->offsetX_p }}"
                                        data-offset-y="{{ $cursor->offsetY }}" data-offset-y_p="{{ $cursor->offsetY_p }}"
                                        data-c_file="/cursors/{{ $cursor->id . '-' . $cursor->name_s }}-cursor.svg"
                                        data-p_file="/pointers/{{ $cursor->id . '-' . $cursor->name_s }}-pointer.svg">
                                            <img title="Apply" src="/images/apply.svg">
                                        </button>
                                    </span>

                                    <span class="pointerevent">
                                        <button class="img-btn" data-action="add" data-type="stat" data-label="@lang('messages.add_to_collection')"
                                        data-disabled="@lang('messages.add_to_collection_added')" data-cataltname="{{ $cursor->collection->alt_name }}"
                                        data-catbasename_en="@lang('collections.' . $cursor->collection->alt_name)"
                                        data-catbasename_es="@lang('collections.' . $cursor->collection->alt_name)"
                                        data-catbasename="@lang('collections.' . $cursor->collection->alt_name)" data-cat="{{ $cursor->cat }}"
                                        data-id="{{ $cursor->id }}" data-name="@lang('cursors.c_' . $cursor->id)"
                                        data-offset-x="{{ $cursor->offsetX }}" data-offset-x_p="{{ $cursor->offsetX_p }}"
                                        data-offset-y="{{ $cursor->offsetY }}" data-offset-y_p="{{ $cursor->offsetY_p }}"
                                        data-c_file="/cursors/{{ $cursor->id . '-' . $cursor->name_s }}-cursor.svg"
                                        data-p_file="/pointers/{{ $cursor->id . '-' . $cursor->name_s }}-pointer.svg">
                                            <img title="@lang('messages.add_to_collection')" src="/images/plus.svg">
                                        </button>
                                    </span>
                                </div>
                        </div>
                    </div>
                @empty
                    <div class="no_result">@lang('messages.no_result')</div>
                @endforelse
            </div>

            @if (isset($cursors) && method_exists($cursors, 'lastPage') && $cursors->lastPage() > 1)
                <div class="pagination-wrapper">
                    <div class="pagination"></div>
                </div>
            @endif

            <div class="seo-block collapsed" id="seoBlock2">
                <p class="seo-info">
                
                    @lang('messages.customize_your_browser_with')
                    <button class="seo-toggle"
                        onclick="document.getElementById('seoBlock2').classList.remove('collapsed'); this.remove()">@lang('messages.read_more') </button>
                </p>
                <div class="seo-text">
                    <p>
                        @lang('messages.main_text_2')
                    </p>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.install')



    @if (isset($cursors) && method_exists($cursors, 'lastPage') && $cursors->lastPage() > 1)
    <script>
        let currentPage = {{ $cursors->currentPage() }};
        let totalPages = {{ $cursors->lastPage() }};
    </script>
@endif

@endsection


@section('lib_bottom')
    <script src="{{ secure_asset('/js/pagination.js') }}{{ build_version() }}"></script>
    <script src="{{ secure_asset('/js/main.js') }}{{ build_version() }}"></script>
@endsection