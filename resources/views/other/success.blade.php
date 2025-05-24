@extends('layouts.app')

@section('head_meta')
    <title>@lang('success.thanks_for_install')</title>
@endsection

@push('styles')
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Round">
    <link rel="icon" type="image/png" href="{{ asset_cdn('images/favicon.png') }}" />
@endpush

@section('main')
    <div class="main">
        <div class="container">
            <div class="gads-wrapper infeed" style="width:100%">
                <div class="googleads" style="width:100%">
                    @include('ads.google.infeed')
                </div>

                @include('lang.' . app()->getLocale() . '.success')
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
