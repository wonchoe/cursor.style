@extends('layouts.app')

@section('head_meta')
    <title>@lang('privacy.title')</title>
    <meta name="description" content="@lang('privacy.descr')" />
@endsection

@push('styles')
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Round">
    <link rel="icon" type="image/png" href="{{ asset_cdn('images/favicon.png') }}" />
@endpush

@section('main')
    <div class="main">
        <div class="container">

            <div class="doc">
                <div class="container-docs">
                    @lang('privacy.text')
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset_ver('js/main.js') }}"></script>
@endpush