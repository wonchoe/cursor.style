@extends('layouts.app')


@section('title')
@lang('cookies.title')
@endsection

@section('descr')
@lang('cookies.descr')
@endsection

@section('lib_top')
<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Round">
<link rel="icon" type="image/png" href="{{ secure_asset('images/favicon.png') }}"/>

@endsection


@section('main')
<div class="main">   
    <div class="container">

        @include('layouts.banner')

        <div class="doc">
            <div class="container-docs">
                @lang('cookies.text')
            </div>           
        </div>

    </div>
</div>


@include('layouts.install')
@endsection


@section('lib_bottom')
<script src="{{ secure_asset('/js/main.js') }}"></script>    
@endsection