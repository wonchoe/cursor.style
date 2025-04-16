@extends('layouts.app')


@section('title')
    @lang('success.thanks_for_install')
@endsection

@section('lib_top')
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Round">
    <link rel="icon" type="image/png" href="{{ secure_asset('images/favicon.png') }}" />
@endsection



@section('main')

    <div class="main">
        <div class="container">


            @include('other.lang.' . app()->getLocale() . '.success')


            <div class="googleads">
                <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                <ins class="adsbygoogle" style="display:block" data-ad-format="fluid" data-ad-layout-key="-fb+5w+4e-db+86"
                    data-ad-client="ca-pub-2990484856526951" data-ad-slot="2703806348"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            </div>

        </div>
    </div>


@endsection



@section('lib_bottom')

@endsection