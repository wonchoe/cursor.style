@extends('layouts.app')


@section('head_meta')
    <title>@lang('collections.all_title')</title>
    <meta name="description" content="@lang('collections.all_descr')" />
@endsection

@push('styles')
    <link rel="icon" type="image/png" href="{{ asset_cdn('images/favicon.png') }}" />
@endpush

@section('main')
    @include('partials.modal-install')

    <div class="main">
        <div class="container">

            @include('partials.seo', [
                'shortText' => __('collections.text_title'),
                'fullText' => __('collections.text_1'),
                'id' => 'seoBlock'
            ])

            <nav class="breadcrumb" aria-label="Breadcrumb">
                <ol>
                    <li><a href="/">@lang('messages.menu_main')</a></li>
                    <li class="active">@lang('messages.allcollections')</li>
                </ol>
            </nav>

            <div class="collection__list">
                @foreach ($collections as $key => $item)
                    @if ($key % 18 == 0)
                        <div class="gads-wrapper infeed" style="width:100%">
                            <div class="googleads" style="width:100%">
                                @include('ads.google.infeed')
                            </div>
                        </div>
                    @endif
                    <a class="collection__item_cat" href="{{ $item->url }}">
                        <h2 class="collection__item_cat_title">
                            {{ $item->currentTranslation->name ?? $item->base_name_en }}
                        </h2>
                        <img loading="lazy" class="main__cat-img"
                             src="{{ asset_cdn($item->img) }}"
                             alt="{{ $item->currentTranslation->name ?? $item->base_name_en }}"
                             title="{{ $item->currentTranslation->short_desc ?? $item->short_descr }}">
                    </a>
                @endforeach
            </div>

            @if ($collections->lastPage() > 1)
                <div class="pagination-wrapper">
                    <div class="pagination"></div>
                </div>
            @endif

            @include('partials.seo', [
                'shortText' => __('collections.text_allcat_title'),
                'fullText' => __('collections.text_2'),
                'id' => 'seoBlock2'
            ])
        </div>
    </div>

    @if ($collections->lastPage() > 1)
    <script>
        let currentPage = {{ $collections->currentPage() }};
        let totalPages = {{ $collections->lastPage() }};
    </script>
    @endif
@endsection

@push('scripts')
    <script src="{{ asset_ver('js/pagination.js') }}"></script>
@endpush
