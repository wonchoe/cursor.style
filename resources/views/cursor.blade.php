@extends('layouts.app')
@include('other.build')
@section('title')
    Cursor {{$cursor->name_en}}
@endsection

@section('descr')
    {{$cursor->name_en}}
@endsection

@section('lib_top')
    <meta property="og:image:width" content="700" />
    <meta property="og:image:height" content="350" />

    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Round" />

    <link rel="icon" type="image/png" href="{{ secure_asset('images/favicon.png') }}" />
@endsection

@section('main')

    <div class="main">
        <div class="container">
            @include('layouts.modal')
            @include('layouts.banner')

  


            <div class="near-wrapper">
                <div class="main__list_near">
                    @forelse ($all_cursors as $key => $currentcursor)
                        <div onclick='location.href="/details/{{$currentcursor->id}}-{{$currentcursor->name_s}}"' class="main__item" @if ($key == 0) style="opacity: 0.5;" @endif @if ($key == 2) style="opacity: 0.5;" @endif>
                            <div class="div_ar_p">
                                <p>@lang('cursors.c_' . $currentcursor->id) </p>
                            </div>
                            <div class="main__item-img cs_pointer" data-cur-id="{{ $currentcursor->id }}" cursorshover="true">
                                <img src="/cursors/{{ $currentcursor->id . '-' . $currentcursor->name_s }}-cursor.svg">
                                <img src="/pointers/{{ $currentcursor->id . '-' . $currentcursor->name_s }}-pointer.svg">
                            </div>
                        </div>
                    @empty
                        @include('other.nocursors')
                    @endforelse
                </div>
            </div>

      

            <div class="main__item-wrapper">
                @if ($id_prev)
                    <button onclick='location.href="/details/{{$id_prev[0]}}-{{$id_prev[1]}}"' class="nav-arrow left">‹</button>
                @endif

                <div class="main__item">
                    <div class="main__item-title">
                        <p>@lang('cursors.c_' . $cursor->id)</p>
                    </div>

                    <div class="main__item-img cs_pointer" data-cur-id="{{ $cursor->id }}" cursorshover="true">
                        <img class="cursorimg"
                            style="cursor: url(/cursors/{{ $cursor->id . '-' . $cursor->name_s }}-cursor.svg) 0 0, auto !important;"
                            src="/cursors/{{ $cursor->id . '-' . $cursor->name_s }}-cursor.svg" alt="Cursor image">

                        <img class="cursorimg"
                            style="cursor: url(/pointers/{{ $cursor->id . '-' . $cursor->name_s }}-pointer.svg) 0 0, auto !important;"
                            src="/pointers/{{ $cursor->id . '-' . $cursor->name_s }}-pointer.svg" alt="Pointer image">
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

                @if ($id_next)
                    <button onclick='location.href="/details/{{$id_next[0]}}-{{$id_next[1]}}"'
                        class="nav-arrow right">›</button>
                @endif
            </div>






        <div class="random_cat">
            @foreach($random_cat as $item)
                <a href="/collections/{{$item->alt_name}}" title="@lang('collections.' . $item->alt_name . '_short_descr')">
                    <div class="random_cat_obj">
                        <div class="random_cat_text">
                            <h2>@lang('collections.' . $item->alt_name) @lang('messages.collection')</h2>
                        </div>
                        <div class="random_cat_img">
                            <img src="/collection/{{$item->alt_name}}.png" alt="@lang('collections.' . $item->alt_name)">
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

  

    </div>
    </div>




    @include('layouts.install')

@endsection


@section('lib_bottom')
    <script src="{{ secure_asset('/js/main.js') }}{{ build_version() }}"></script>
@endsection