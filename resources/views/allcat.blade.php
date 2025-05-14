@extends('layouts.app')
@include('other.build')

@section('title')
    @lang('collections.all_title')
@endsection

@section('descr')
    @lang('collections.all_descr')
@endsection

@section('lib_top')
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Round" />
    <link rel="icon" type="image/png" href="https://cursor.style/images/favicon.png" />
@endsection


@section('main')
    <div class="main">
        <div class="container">


            <div class="seo-block collapsed" id="seoBlock">
                @include('layouts.banner')
                <p class="seo-info">
                    @lang('collections.text_title')
                    <button class="seo-toggle"
                        onclick="document.getElementById('seoBlock').classList.remove('collapsed'); this.remove()">@lang('messages.read_more')</button>
                </p>
                <div class="seo-text">
                    <p>
                        @lang('collections.text_1')
                    </p>
                </div>
            </div>

            <nav class="breadcrumb" aria-label="Breadcrumb">
                <ol>
                    <li><a href="/">@lang('messages.menu_main')</a></li>
                    <li class="active">@lang('messages.allcollections')</li>
                </ol>
            </nav>


            <div class="collection__list">

                @foreach ($cats as $key => $item)
                    @if ($key % 18 == 0)
                        <div class="gads-wrapper infeed" style="width:100%">
                            <div class="googleads" style="width:100%">
                                <!-- google ads here -->
                                @include('other.google.infeed')
                                <!-- google ads here -->
                            </div>
                        </div>
                    @endif
                    <a class="collection__item_cat" href="/collections/{{ $item->alt_name }}">
                        <h2 class="collection__item_cat_title">
                           {{ $item->currentTranslation->name ?? $item->base_name_en }}
                        </h2>
                    <img class="main__cat-img"
                        src="{{ $item->img }}"
                        alt="{{ $item->currentTranslation->name ?? $item->base_name_en }}"
                        title="{{ $item->currentTranslation->short_desc ?? $item->short_descr }}">
                    </a>
                @endforeach
            </div>


            <div class="pagination-wrapper">
                <div class="pagination"></div>
            </div>

            @if ($cats->lastPage() > 1)
            <script>
                let currentPage = {{ $cats->currentPage() }};
                let totalPages = {{ $cats->lastPage() }};
            </script>
            @endif


            <div class="seo-block collapsed" id="seoBlock">
                <p class="seo-info">
                    @lang('collections.text_allcat_title')
                    <button class="seo-toggle"
                        onclick="document.getElementById('seoBlock').classList.remove('collapsed'); this.remove()">@lang('messages.read_more')</button>
                </p>
                <div class="seo-text">
                    <p> 
                        @lang('collections.text_2')
                    </p>
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