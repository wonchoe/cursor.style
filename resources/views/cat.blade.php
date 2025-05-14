@extends('layouts.app')
@include('other.build')
@section('title')
    {{ $collection->currentTranslation->name ?? $collection->base_name_en }}
    {{ __('collections.mouse_cursors') }} |
    {{ $collection->currentTranslation->short_desc ?? $collection->short_descr }}
@endsection

@section('descr')
    {{ $collection->currentTranslation->desc ?? $collection->description }}
@endsection

@section('page_meta')
    <meta property="og:title" content="{{ $collection->currentTranslation->name ?? $collection->base_name_en }} {{ __('collections.mouse_cursors') }} | {{ $collection->currentTranslation->short_desc ?? $collection->short_descr }}">
    <meta property="og:image:width" content="700" />
    <meta property="og:image:height" content="350" />
    <meta property="og:description" content="{{ $collection->currentTranslation->desc ?? $collection->description }}">
    <meta property="og:image" content="/collection/{{ $collection->id }}-{{ $collection->alt_name }}.png">
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="@cursor.style" />
    <meta name="twitter:title" content="{{ $collection->currentTranslation->name ?? $collection->base_name_en }} {{ __('collections.mouse_cursors') }} | {{ $collection->currentTranslation->short_desc ?? $collection->short_descr }}">
    <meta name="twitter:description" content="{{ $collection->currentTranslation->desc ?? $collection->description }}">
    <meta name="twitter:image" content="/collection/{{ $collection->id }}-{{ $collection->alt_name }}.png">
@endsection

@section('lib_top')
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Round" />
    <link rel="icon" type="image/png" href="https://cursor.style/images/favicon.png" />
@endsection

@section('main')

    @include('layouts.modal')
    <div class="main">
        <div class="container collection_page">


            @include('layouts.banner')


            <div class="collection-page">
                <div class="container_cat">


                    <nav class="breadcrumb" aria-label="Breadcrumb">
                        <ol>
                            <li><a href="/">@lang('messages.menu_main')</a></li>
                            <li><a href="/collections">@lang('messages.menu_collection')</a></li>
                            <li class="active">{{ $collection->currentTranslation->name ?? $collection->base_name_en }}</li>
                        </ol>
                    </nav>


                    <div class="collection-description">
                        <div class="collection-description__img">
                            <img src="/collection/{{ $collection->id }}-{{ $collection->alt_name }}.png"
                            alt="@lang('collections.cursor_collection') {{ $collection->currentTranslation->name ?? $collection->base_name_en }}"
                            title="{{ $collection->currentTranslation->short_desc ?? $collection->short_descr }}">
                        </div>

                        <div class="collection-description__text">
                        <h1 class="collection-description__title">
                            {{ $collection->currentTranslation->name ?? $collection->base_name_en }}
                            - @lang('messages.mouse_cursors')
                        </h1>

                            <div class="collection-description__body">
                                @php
                                    $rawText = $collection->currentTranslation->desc ?? $collection->description;
                                    $text = strip_tags(stripslashes($rawText));
                                    $preview = Str::limit($text, 450, '...');
                                @endphp

                                {!! nl2br(e($preview)) !!}

                                @if(strlen($text) > 450)
                                    <a class="read-more-btn" href="#"
                                        onclick="event.preventDefault(); this.parentElement.innerHTML = `{!! nl2br(e($text)) !!}`">@lang('messages.read_more')</a>
                                @endif
                            </div>
                        </div>
                    </div>


                    <div class="gads-wrapper infeed" style="width:100%">
                        <div class="googleads" style="width:100%">
                            <!-- google ads here -->
                            @include('other.google.infeed')
                            <!-- google ads here -->
                        </div>
                    </div>

                        @if($cursors->isNotEmpty())
                        <div class="main__list" id="main_list">
                            @forelse($cursors as $key => $cursor)
                                @if ($key > 0 && $key % 16 === 0)
                                    <div class="gads-wrapper">
                                    <!-- google ads here -->
                                        @include('other.google.infeed')
                                    <!-- google ads here -->
                                    </div>
                                @endif
                                @php
                                    $translation = $cursor->currentTranslation->name ?? $cursor->name_en;
                                    $slug = Str::slug($translation);
                                    if (empty($slug)) {
                                        $slug = Str::slug($cursor->name_en);
                                    }
                                @endphp
                                <div class="main__item" data-container-id="{{ $cursor->id }}" onclick="handleItemClick(event, '/details/{{ $cursor->id }}-{{ 
                                    $slug
                                }}')">          
                                <div class="div_ar_p">
                                    <p>{{ $cursor->currentTranslation->name ?? $cursor->name_en }}</p>
                                </div>

                                <div class="main__item-img cs_pointer" data-cur-id="{{ $cursor->id }}" cursorshover="true">
                                    <img class="cursorimg"
                                        style="cursor: url({{ $cursor->c_file_no_ext }}) 0 0, auto !important;"
                                        src="{{ $cursor->c_file_no_ext }}">

                                    <img class="cursorimg"
                                        style="cursor: url({{ $cursor->p_file_no_ext }}) 0 0, auto !important;"
                                        src="{{ $cursor->p_file_no_ext }}">
                                </div>

                                <span class="downloads-badge">
                                    <img src="/images/icons/download.png" style="width: 10px;">
                                    {{ number_format($cursor->totalClick + $cursor->todayClick) }}
                                </span>

                                                <div class="main__btns">
                                                    <div class="btn-container">
                                                        <span class="pointerevent">
                                                            <button class="img-btn" data-action="apply" data-type="stat"
                                                                data-label="@lang('messages.add_to_collection')"
                                                                data-disabled="@lang('messages.add_to_collection_added')"
                                                                data-cataltname="{{ $cursor->collection->alt_name }}"
                                                                data-catbasename_en="{{ $cursor->collection->currentTranslation->name ?? $cursor->collection->base_name_en }}"
                                                                data-catbasename_es="{{ $cursor->collection->currentTranslation->name ?? $cursor->collection->base_name_en }}"
                                                                data-catbasename="{{ $cursor->collection->currentTranslation->name ?? $cursor->collection->base_name_en }}"
                                                                data-cat="{{ $cursor->cat }}" data-id="{{ $cursor->id }}"
                                                                data-name="{{ $cursor->currentTranslation->name ?? $cursor->name_en }}"
                                                                data-offset-x="{{ $cursor->offsetX }}"
                                                                data-offset-x_p="{{ $cursor->offsetX_p }}"
                                                                data-offset-y="{{ $cursor->offsetY }}"
                                                                data-offset-y_p="{{ $cursor->offsetY_p }}"
                                                                data-c_file="{{ $cursor->c_file_no_ext }}"
                                                                data-p_file="{{ $cursor->p_file_no_ext }}">
                                                                <img title="Apply" src="/images/apply.svg">
                                                            </button>
                                                        </span>

                                                        <span class="pointerevent">
                                                            <button class="img-btn" data-action="add" data-type="stat"
                                                                data-label="@lang('messages.add_to_collection')"
                                                                data-disabled="@lang('messages.add_to_collection_added')"
                                                                data-cataltname="{{ $cursor->collection->alt_name }}"
                                                                data-catbasename_en="{{ $cursor->collection->currentTranslation->name ?? $cursor->collection->base_name_en }}"
                                                                data-catbasename_es="{{ $cursor->collection->currentTranslation->name ?? $cursor->collection->base_name_en }}"
                                                                data-catbasename="{{ $cursor->collection->currentTranslation->name ?? $cursor->collection->base_name_en }}"
                                                                data-cat="{{ $cursor->cat }}" data-id="{{ $cursor->id }}"
                                                                data-name="{{ $cursor->currentTranslation->name ?? $cursor->name_en }}"
                                                                data-offset-x="{{ $cursor->offsetX }}"
                                                                data-offset-x_p="{{ $cursor->offsetX_p }}"
                                                                data-offset-y="{{ $cursor->offsetY }}"
                                                                data-offset-y_p="{{ $cursor->offsetY_p }}"
                                                                data-c_file="{{ $cursor->c_file_no_ext }}"
                                                                data-p_file="{{ $cursor->p_file_no_ext }}">
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
                        @else
                            @include('other.nocursors')
                        @endif



                    <div class="random_cat">
                    @foreach($random_cat as $item)
                        <a href="/collections/{{ $item->alt_name }}"
                            title="{{ $item->currentTranslation->short_desc ?? $item->short_descr }}">
                            <div class="random_cat_obj">
                                <div class="random_cat_text">
                                    <h2>{{ $item->currentTranslation->name ?? $item->base_name_en }} @lang('messages.collection')</h2>
                                </div>
                                <div class="random_cat_img">
                                    <img src="/collection/{{ $item->id }}-{{ $item->alt_name }}.png"
                                        alt="{{ $item->currentTranslation->name ?? $item->base_name_en }}"
                                        title="{{ $item->currentTranslation->short_desc ?? $item->short_descr }}">
                                </div>
                            </div>
                        </a>
                    @endforeach
                    </div>


                </div>
            </div>
        </div>

    </div>




    @include('layouts.install')

@endsection


@section('lib_bottom')
    <script src="https://cursor.style/js/pagination.js{{ build_version() }}"></script>
    <script src="https://cursor.style/js/main.js{{ build_version() }}"></script>
@endsection