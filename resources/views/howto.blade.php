@extends('layouts.app')

@section('head_meta')
    <title>@lang('messages.title')</title>
    <meta name="description" content="@lang('messages.descr')" />
@endsection

@push('styles')
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Round" />
    <link rel="icon" type="image/png" href="{{ asset_cdn('images/favicon.png') }}" />
@endpush

@section('main')
    <div class="main">
        <div class="container">
            <div class="how">

            <nav class="breadcrumb" aria-label="Breadcrumb">
                <ol>
                <li><a href="/">@lang('messages.menu_main')</a></li>
                <li class="active">@lang('messages.menu_how_to_use')</li>
                </ol>
            </nav>

            <div class="gads-wrapper infeed" style="width:100%">
                <div class="googleads" style="width:100%">
                <!-- google ads here -->
                @include('ads.google.infeed')
                <!-- google ads here -->
                </div>
            </div>


            <div class="how__wrap">
                <div class="how__btns">
                <div class="how__btn active">@lang('messages.t_2')</div>
                <div class="how__btn">@lang('messages.t_3')</div>
                <div class="how__btn">@lang('messages.t_4')</div>
                <div class="how__btn" style="margin-bottom: 10px;">@lang('messages.t_6')</div>
                </div>

                @php
                $locale = app()->getLocale();
                @endphp

                <div class="how__tabs">
                @foreach ([1, 2, 3, 4] as $i)
                <div class="how__tab {{ $i === 1 ? 'active' : '' }}">
                @if(View::exists("lang.$locale.howto.tab_$i"))
                @include("lang.$locale.howto.tab_$i")
                @else
                @include("lang.en.howto.tab_$i")
                @endif
                </div>
                @endforeach
                </div>


            </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset_ver('js/pagination.js') }}"></script>
    <script src="{{ asset_ver('js/main.js') }}"></script>
@endpush
