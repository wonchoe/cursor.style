@extends('layouts.app')


@section('title')
    @lang('collections.all_title')
@endsection

@section('descr')
    @lang('collections.all_descr')
@endsection

@section('lib_top')
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Round" />
    <link rel="icon" type="image/png" href="{{ secure_asset('images/favicon.png') }}" />
@endsection


@section('main')
    <div class="main">
        <div class="container">


            <div class="seo-block collapsed" id="seoBlock">
                @include('layouts.banner')
                <p class="seo-info">
                    @lang('collections.text_title')
                    <button class="seo-toggle"
                        onclick="document.getElementById('seoBlock').classList.remove('collapsed'); this.remove()">Read
                        more</button>
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
                    <li class="active">@lang('messages.menu_collection')</li>
                </ol>
            </nav>


            <div class="collection__list">

                @foreach ($cats as $key => $item)
                    @if ($key % 18 == 0)
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
                    <a class="collection__item_cat" href="/collections/{{$item->alt_name}}">
                        <h2 class="collection__item_cat_title">@lang('collections.' . $item->alt_name)</h2>
                        <img class="main__cat-img" src="/collection/{{$item->alt_name}}.png"
                            alt="@lang('collections.' . $item->alt_name)"
                            title="@lang('collections.' . $item->alt_name . '_short_descr')">
                    </a>
                @endforeach
            </div>



            <div class="seo-block collapsed" id="seoBlock">
                <p class="seo-info">
                    @lang('collections.text_allcat_title')
                    <button class="seo-toggle"
                        onclick="document.getElementById('seoBlock').classList.remove('collapsed'); this.remove()">Read
                        more</button>
                </p>
                <div class="seo-text">
                    <p> 
                        @lang('collections.text_2')
                    </p>
                </div>
            </div>

        </div>
    </div>

    @if (!$success)
        @include('layouts.install')
    @endif
@endsection


@section('lib_bottom')
    <script src="{{ secure_asset('/js/main.js') }}"></script>
@endsection