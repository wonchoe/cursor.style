@extends('layouts.app')


@section('title')
@lang('collections.all_title')
@endsection

@section('descr')
@lang('collections.all_descr')
@endsection

@section('lib_top')
<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Round"/>
<link rel="stylesheet" href="{{ secure_asset('css/hover-min.css') }}"/>        
<link rel="stylesheet" href="{{ secure_asset('fonts/fonts.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/libscss.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/main.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/ie.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/loader.css') }}"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.js"></script>
<script src="{{ secure_asset('/js/cat/preload.js') }}"></script>    
<link rel="icon" type="image/png" href="{{ secure_asset('images/favicon.png') }}"/>
@endsection


@section('main')
<div class="main">   
    <div class="container">

        @if (!$success)
        @include('layouts.banner')
        @endif

        @if ($success)
        <div class="success_after_install">
            <div class="success_after_install_text">Спасибо, что установили наше расширение!
            </div>
            <div class="success_after_install_img">
                <img src="/images/steps.webp"/>
            </div>
            <div class="success_after_install_bottom_text_container">
                <div class="success_after_install_bottom_text">
                    Добавьте понравившийся курсор с сайта
                </div>
                <div class="success_after_install_bottom_text">
                    Откройте меню расширения, нажав на иконку в панели
                </div>
                <div class="success_after_install_bottom_text">
                    Активируйте добавленный курсор, кликнув по нему.
                </div>
            </div>            
        </div>
        @endif

        <div class="collection_top_text">
            <h1>@lang('collections.text_title')</h1>
            @lang('collections.text_1')
        </div>




        

        <div class="collection__list">
            
            @foreach ($cats as $key => $item)
            @if ($key % 18 == 0) 
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
             @endif
            <a class="collection__item_cat" href="/collections/{{$item->alt_name}}">
                <h2 class="collection__item_cat_title">@lang('collections.'.$item->alt_name)</h2>
                <img class="main__cat-img" src="/collection/{{$item->alt_name}}.png" alt="@lang('collections.'.$item->alt_name)" title="@lang('collections.'.$item->alt_name.'_short_descr')">
                <h3 class="collection__description">@lang('collections.'.$item->alt_name.'_short_descr')</h3>
            </a>   
            @endforeach
        </div>
        

        
        <div class="collection_bottom_text">
             @lang('collections.text_2')
        </div>    
    </div>
</div>

@if (!$success)
@include('layouts.install')
@endif
@endsection


@section('lib_bottom')
<script src="{{ secure_asset('/js/main.js') }}"></script>    
<script src="{{ secure_asset('/js/banner_cursor.js') }}"></script>
@endsection