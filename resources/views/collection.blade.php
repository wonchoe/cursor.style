@extends('layouts.app')

@section('head_meta')
    <title>{{ $collection->currentTranslation->name ?? $collection->base_name_en }} {{ __('collections.mouse_cursors') }} | {{ $collection->currentTranslation->short_desc ?? $collection->short_descr }}</title>
    <meta name="description" content="{{ $collection->currentTranslation->desc ?? $collection->description }}" />
    <meta property="og:title" content="{{ $collection->currentTranslation->name ?? $collection->base_name_en }} {{ __('collections.mouse_cursors') }}" />
    <meta property="og:description" content="{{ $collection->currentTranslation->desc ?? $collection->description }}" />
    <meta property="og:image" content="{{ asset_cdn($collection->img) }}" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $collection->currentTranslation->name ?? $collection->base_name_en }} {{ __('collections.mouse_cursors') }}" />
    <meta name="twitter:description" content="{{ $collection->currentTranslation->desc ?? $collection->description }}" />
    <meta name="twitter:image" content="{{ asset_cdn($collection->img) }}" />

    {!! renderHreflangLinksForCollection($collection->id, $translations, $collection->base_name_en) !!}

@endsection

@push('styles')
    <link rel="icon" type="image/png" href="{{ asset_cdn('images/favicon.png') }}" />
@endpush

@section('main')
    @include('partials.modal-install')

    <div class="main">
        <div class="container collection_page">

            <nav class="breadcrumb" aria-label="Breadcrumb">
                <ol>
                    <li><a href="/">@lang('messages.menu_main')</a></li>
                    <li><a href="/collections">@lang('messages.menu_collection')</a></li>
                    <li class="active">{{ $collection->currentTranslation->name ?? $collection->base_name_en }}</li>
                </ol>
            </nav>

            <div class="collection-description">
                <div class="collection-description__img">
                    <img src="{{ asset_cdn($collection->img) }}"
                         alt="{{ $collection->currentTranslation->name ?? $collection->base_name_en }}"
                         title="{{ $collection->currentTranslation->short_desc ?? $collection->short_descr }}">
                </div>
                <div class="collection-description__text">
                    <h1 class="collection-description__title">
                        {{ $collection->currentTranslation->name ?? $collection->base_name_en }}
                        - @lang('messages.mouse_cursors')
                    </h1>

                    @php
                        $rawText = $collection->currentTranslation->desc ?? $collection->description;
                        $text = preg_replace('/\\\\*"/', '"', $rawText);
                        //$text = strip_tags(stripslashes($rawText));
                        $preview = Str::limit($text, 350, '...');
                    @endphp

                    <div class="collection-description__body">
                        {!! nl2br(e($preview)) !!}
                        @if(strlen($text) > 450)
                            <a class="read-more-btn" href="#"
                               onclick="event.preventDefault(); this.parentElement.innerHTML = `{!! nl2br(e($text)) !!}`">@lang('messages.read_more')</a>
                        @endif
                    </div>
                </div>
            </div>

            @include('partials.cursor-list', ['cursors' => $cursors])

            <div class="random_cat">
                @foreach($random as $item)
                    <a href="{{ $url = getUrl(null, null, $item->id) }}"
                       title="{{ $item->currentTranslation->short_desc ?? $item->short_descr }}">
                        <div class="random_cat_obj">
                            <div class="random_cat_text">
                                <h2>{{ $item->currentTranslation->name ?? $item->base_name_en }} @lang('messages.collection')</h2>
                            </div>
                            <div class="random_cat_img">
                                <img loading="lazy" src="{{ asset_cdn($item->img) }}"
                                     alt="{{ $item->currentTranslation->name ?? $item->base_name_en }}"
                                     title="{{ $item->currentTranslation->short_desc ?? $item->short_descr }}">
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset_ver('js/pagination.js') }}"></script>
@endpush
