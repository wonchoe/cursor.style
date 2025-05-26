@extends('layouts.app')

@section('head_meta')
    <title>
        {{ $cursor->seo_title ?? $cursor->currentTranslation->name ?? $cursor->name_n ?? $cursor->name_en }}
    </title>
    <meta name="description"
          content="{{ $cursor->seo_description ?? $cursor->currentTranslation->name ?? $cursor->name_n ?? $cursor->name_en }}">
    <meta property="og:image:width" content="700" />
    <meta property="og:image:height" content="350" />
    <meta property="og:image" content="{{ asset_cdn($cursor->c_file) }}.png" />
    <link rel="icon" type="image/png" href="{{ asset_cdn('images/favicon.png') }}" />
@endsection

@section('main')
<div class="main">
    <div class="container">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <ol>
                    <li><a href="/">@lang('messages.menu_main')</a></li>
                    <li><a href="/collections">@lang('messages.menu_collection')</a></li>
                    <li><a href="{{ $cursor->collectionSlug }}">{{ $cursor->collection->currentTranslation->name ?? $cursor->collection->base_name_en }}</a></li>
                    <li class="active">{{ $cursor->currentTranslation->name ?? $cursor->name_en }}</li>
                </ol>
            </nav>

        

        @include('partials.cursor-viewer', [
            'cursor' => $cursor,
            'id_prev' => $id_prev,
            'id_next' => $id_next
        ])

        @include('partials.nearby-cursors', ['cursors' => $all_cursors, 'current_id' => $cursor->id])

        @if($category_cursors->isNotEmpty())
        
        <h2 class="section-title">
            <span class="title">
                {{ $cursor->collection->currentTranslation->name ?? $cursor->collection->base_name_en }} - @lang('messages.collection')
            </span>
            <br>
            <span class="short_descr">
                {{ $cursor->collection->currentTranslation->short_desc ?? $cursor->collection->short_descr ?? '' }}
            </span>
        </h2>
            
            @include('partials.cursor-list', ['cursors' => $category_cursors])

            @if (isset($category_cursors) && method_exists($category_cursors, 'lastPage') && $category_cursors->lastPage() > 1)
                <div class="pagination-wrapper">
                    <div class="pagination"></div>
                </div>
            @endif            
        @endif

        @if($random)
        <div class="random_cat">
            @foreach($random as $item)
                <a href="{{ $item->url }}"
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
        @endif
    </div>
</div>


    @if (isset($category_cursors) && method_exists($category_cursors, 'lastPage') && $category_cursors->lastPage() > 1)
    <script>
        let currentPage = {{ $category_cursors->currentPage() }};
        let totalPages = {{ $category_cursors->lastPage() }};
    </script>
    @endif

@endsection

@push('scripts')
    <script src="{{ asset_ver('js/pagination.js') }}"></script>
@endpush
