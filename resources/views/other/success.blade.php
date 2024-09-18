@extends('layouts.app')


@section('title')
@lang('success.thanks_for_install')
@endsection

@section('lib_top')
<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Round">
<link rel="stylesheet" href="{{ secure_asset('css/hover-min.css') }}"/>        
<link rel="stylesheet" href="{{ secure_asset('fonts/fonts.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/libscss.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/main.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/ie.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/loader.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/contact/main.css') }}"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<link rel="icon" type="image/png" href="{{ secure_asset('images/favicon.png') }}"/>
@endsection

@section('main')
<div class="arrow_top"><img src="/images/arrow_top.svg"></div>
<div class="main">   
    <div class="container">

        <div class="install">
            <div class="container_install">
                <div class="install__left">
                    <h2>@lang('success.thanks_for_install')</h2>
                    <p><b>@lang('success.text_1')</b></p>
                    <p>@lang('success.text_2')</p>
                    <img class="install__rounded_img" src="@lang('success.img_1')" alt="Success install"/>
                    <p><b>@lang('success.text_3')</b></p>
                </div>
                <div class="install__right" href="/">
                    <img src="@lang('success.img_2')" alt=""/>
                    <p>@lang('success.text_4')</p>
                    <a class="success__more__btn hvr-shutter-out-horizontal" href="/">@lang('success.more_cursors')</a>
                </div>
            </div>
        </div>
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

    </div>
</div>


@endsection



@section('lib_bottom')

@endsection