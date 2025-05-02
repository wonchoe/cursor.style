@extends('layouts.app')
@include('other.build')
@section('title')
    @lang('collections.' . $alt_name) @lang('collections.mouse_cursors') |
    @lang('collections.' . $alt_name . '_short_descr')}
@endsection

@section('descr')
    @lang('collections.' . $alt_name . '_descr')
@endsection

@section('page_meta')
    <meta property="og:title"
        content="@lang('collections.' . $alt_name) @lang('collections.mouse_cursors') | @lang('collections.' . $alt_name . '_short_descr')" />
    <meta property="og:image:width" content="700" />
    <meta property="og:image:height" content="350" />
    <meta property="og:description" content="@lang('collections.' . $alt_name . '_descr')" />
    <meta property="og:image" content="https://en.cursor.style/collection/{{$alt_name}}.png" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="@cursor.style" />
    <meta name="twitter:title"
        content="@lang('collections.' . $alt_name) @lang('collections.mouse_cursors') | @lang('collections.' . $alt_name . '_short_descr')" />
    <meta name="twitter:description" content="@lang('collections.' . $alt_name . '_descr')" />
    <meta name="twitter:image" content="https://en.cursor.style/collection/{{$alt_name}}.png" />
@endsection

@section('lib_top')
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Round" />
    <link rel="icon" type="image/png" href="{{ secure_asset('images/favicon.png') }}" />
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
                            <li class="active">@lang('collections.' . $alt_name)</li>
                        </ol>
                    </nav>


                    <div class="collection-description">
                        <div class="collection-description__img">
                            <img src="/collection/{{$alt_name}}.png"
                                alt="@lang('collections.cursor_collection') @lang('collections.' . $alt_name)"
                                title="@lang('collections.' . $alt_name . '_short_descr')">
                        </div>

                        <div class="collection-description__text">
                            <h1 class="collection-description__title">
                                @lang('collections.' . $alt_name) - @lang('messages.mouse_cursors')
                            </h1>

                            <div class="collection-description__body">
                                @php
                                    $rawText = __('collections.' . $alt_name . '_descr');
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

                    <div class="main__list">
                        @forelse($cursors as $key => $cursor)

                            @if ($key > 0 && $key % 16 === 0)
                                <div class="gads-wrapper infeed" style="width:100%">
                                    <div class="googleads" style="width:100%">
                                        <!-- google ads here -->
                                        @include('other.google.infeed')
                                        <!-- google ads here -->
                                    </div>
                                </div>
                            @endif

                            <div class="main__item"
                                onclick="handleItemClick(event, '/details/{{$cursor->id}}-{{$cursor->name_s}}')">
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
                            @include('other.nocursors')
                        @endforelse
                    </div>


                    <div class="random_cat">
                        @foreach($random_cat as $item)
                            <a href="/collections/{{$item->alt_name}}"
                                title="@lang('collections.' . $item->alt_name . '_short_descr')">
                                <div class="random_cat_obj">
                                    <div class="random_cat_text">
                                        <h2>@lang('collections.' . $item->alt_name) @lang('messages.collection')</h2>
                                    </div>
                                    <div class="random_cat_img">
                                        <img src="/collection/{{$item->alt_name}}.png"
                                            alt="@lang('collections.' . $item->alt_name)">
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
<script src="{{ secure_asset('/js/main.js') }}{{ build_version() }}"></script>
@endsection