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
<<<<<<< HEAD


<meta property="og:title" content="@lang('messages.og_title')" />
<meta property="og:image:width" content="700" />
<meta property="og:image:height" content="350" />
<meta property="og:description" content="@lang('messages.main_page_descr')" />
<meta property="og:image" content="https://en.cursor.style/images/img.jpg" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:site" content="@cursor.style" />
<meta name="twitter:title" content="@lang('messages.og_title')" />
<meta name="twitter:description" content="@lang('messages.main_page_descr')" />
<meta name="twitter:image" content="https://en.cursor.style/images/img.jpg"/>
@yield('css')

<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Round" />

<link rel="stylesheet" href="{{ secure_asset('fonts/fonts.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/libscss.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/main.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/ie.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/loader.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/chat.css') }}"/>

<script src="/js/jquery.js"></script>
<script src="{{ secure_asset('/js/newblock.js') }}"></script>  

<!-- jQuery Modal -->
<script rel="preload" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.2/jquery.modal.js"></script>
<link rel="stylesheet"  href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />


<link rel="icon" type="image/x-icon" href="{{ secure_asset('images/favicon.png') }}"/>

   
    
=======
    @yield('css')
    <link rel="icon" type="image/x-icon" href="{{ secure_asset('images/favicon.png') }}" />
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>
>>>>>>> 8f96918 (🔄 Update site to new version)
@endsection


@section('main')

    @include('layouts.modal')

    <div class="main">
        <div class="container">
            <div class="newblock" id="newblock"></div>

<<<<<<< HEAD


<div class="main">   
    <div class="container">



        @include('layouts.testzone')


        @include('layouts.banner')

        @include('layouts.chat')

        <div class="newblock" id="newblock"></div>

        <div class="collection_top_text">
            @lang('messages.main_text_1')
        </div>   


        <div class="tabs_menu">
                <div class="wrapper">
                    <nav class="tabs">
                        <a href="/" class="cur_menu @if ($sort == 'id') active @endif" id="menu_new" cursorshover="true"><i class="material-icons-round cs_pointer" cursorshover="true">fiber_new</i>@lang('messages.main_page_menu_2')</a>                        
                        <a href="/popular" class="cur_menu @if ($sort == 'top') active @endif" id="menu_top" cursorshover="true"><i class="material-icons-round">star</i>@lang('messages.main_page_menu_1')</a>                        
                        <a href="/collections" class="cur_menu" id="menu_collections"><i class="material-icons-round">list_alt</i>@lang('messages.main_page_menu_4')</a>
                    </nav>
                        <form action="/" method="GET">
                            <input type="text" name="q" id="cs_search" class="search" value="{{ $query }}" placeholder="@lang('messages.main_page_search')" aria-label="@lang('messages.main_page_search')">
                        </form>                    
                </div>                
            </div>
        

          
            <div class="main__list" id="main_list">

                @forelse($cursors as $key => $cursor)
                @if (($key % 16 == 0) && ( $key>0 ))
                    
                <div class="gads">
                <!--GOOGLE ADSENCE-->         
                    <div class="googleads">
                        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                        <ins class="adsbygoogle"
                             style="display:block"
                             data-ad-format="fluid"
                             data-ad-layout-key="-fb+5w+4e-db+86"
                             data-ad-client="ca-pub-2990484856526951"
                             data-ad-slot="2703806348"></ins>
                        <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                        </script>                    
                    </div>
                    <!--GOOGLE ADSENCE-->  
                </div>    
                @endif
=======
>>>>>>> 8f96918 (🔄 Update site to new version)
            
            <div class="seo-block collapsed" id="seoBlock">
                @include('layouts.banner')
                <p class="seo-info">
                    🎯 Discover the magic of custom cursors!
                    <button class="seo-toggle"
                        onclick="document.getElementById('seoBlock').classList.remove('collapsed'); this.remove()">Read
                        more</button>
                </p>
                <div class="seo-text">
                    <p> 
                        @lang('messages.main_text_1')
                    </p>
                </div>
            </div>

            @include('layouts.chat')

            <div class="tabs_menu">
                <div class="wrapper tabs-wrapper">
                    <nav class="tabs">
                        <a href="/" class="cur_menu {{ request()->is('/') ? 'active' : '' }}">
                            <span class="tab-icon">🆕</span>New
                        </a>
                        <a href="/popular" class="cur_menu {{ request()->is('popular') ? 'active' : '' }}">
                            <span class="tab-icon">🔥</span>Trending
                        </a>
                        <a href="/collections" class="cur_menu {{ request()->is('collections') ? 'active' : '' }}">
                            <span class="tab-icon">🎨</span>Collections
                        </a>
                    </nav>
                    <div class="search-wrapper">
                        <form action="/" method="GET">
                            <span class="search-icon">🔍</span>
                            <input type="text" name="q" id="cs_search" class="search" placeholder="Search"
                                aria-label="Search">
                        </form>
                    </div>
                </div>
            </div>
            <div class="main__list" id="main_list">
                @forelse($cursors as $key => $cursor)
                    @if ($key > 0 && $key % 16 === 0)
                        <div class="gads-wrapper">
                            <div class="googleads">
                                <!--GOOGLE ADSENCE-->
                                <div class="googleads">
                                    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                                    <ins class="adsbygoogle" style="display:block" data-ad-format="fluid"
                                        data-ad-layout-key="-fb+5w+4e-db+86" data-ad-client="ca-pub-2990484856526951"
                                        data-ad-slot="2703806348"></ins>
                                    <script>
                                        (adsbygoogle = window.adsbygoogle || []).push({});
                                    </script>
                                </div>
                                <!--GOOGLE ADSENCE-->
                            </div>
                        </div>
                    @endif

                    <div class="main__item" onclick="handleItemClick(event, '/details/{{$cursor->id}}-{{$cursor->name_s}}')">          
                        <div class="div_ar_p">
                            <p>@lang('cursors.c_' . $cursor->id) </p>
                        </div>
                        <div class="main__item-img cs_pointer" data-cur-id="{{ $cursor->id }}" cursorshover="true">
                            <img class="cursorimg"
                                style="cursor: url(/cursors/{{ $cursor->id . '-' . $cursor->name_s }}-cursor.svg) 0 0, auto !important;"
                                src="/cursors/{{ $cursor->id . '-' . $cursor->name_s }}-cursor.svg">
                            <img class="cursorimg"
                                style="cursor: url(/pointers/{{ $cursor->id . '-' . $cursor->name_s }}-pointer.svg) 0 0, auto !important;"
                                src="/pointers/{{ $cursor->id . '-' . $cursor->name_s }}-pointer.svg">
                        </div>
                        <div class="main__btns">
                            <button data-type="stat" class="cursor-button" data-label="ADD TO COLLECTION"
                                data-disabled="✔ ADDED" data-cataltname="{{ $cursor->collection->alt_name }}"
                                data-catbasename_en="{{ $cursor->collection->base_name_en }}"
                                data-catbasename_es="{{ $cursor->collection->base_name_es }}"
                                data-catbasename="{{ $cursor->collection->base_name }}" data-cat="{{ $cursor->cat }}"
                                data-id="{{ $cursor->id }}" data-name="@lang('cursors.c_' . $cursor->id)"
                                data-offset-x="{{ $cursor->offsetX }}" data-offset-x_p="{{ $cursor->offsetX_p }}"
                                data-offset-y="{{ $cursor->offsetY }}" data-offset-y_p="{{ $cursor->offsetY_p }}"
                                data-c_file="/cursors/{{ $cursor->id . '-' . $cursor->name_s }}-cursor.svg"
                                data-p_file="/pointers/{{ $cursor->id . '-' . $cursor->name_s }}-pointer.svg"></a>
                                <!--                         <button onClick='location.href="/details/{{$cursor->id}}-{{$cursor->name_s}}"' class="cursor">Preview</a>                         -->
                        </div>
                    </div>
                @empty
                    <div class="no_result">@lang('messages.no_result')</div>
                @endforelse
            </div>

            <div class="pagination-wrapper">
                <div class="pagination"></div>
            </div>

            <div class="seo-block collapsed" id="seoBlock2">
                <p class="seo-info">
                    Customize your browser with unique pointers and make your clicks truly yours!
                    <button class="seo-toggle"
                        onclick="document.getElementById('seoBlock2').classList.remove('collapsed'); this.remove()">Read
                        more</button>
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

    </div>

    @if ($cursors->lastPage() > 1)

        <script>
            let currentPage = {{ $cursors->currentPage() }};
            let totalPages = {{ $cursors->lastPage() }};
        </script>

    @endif

@endsection


@section('lib_bottom')
    <script src="{{ secure_asset('/js/pagination.js') }}"></script>
    <script src="{{ secure_asset('/js/main.js') }}"></script>
@endsection