@extends('layouts.app')
@include('other.build')

@section('title')
@lang('messages.title')
@endsection

@section('descr')
@lang('messages.descr')
@endsection

@section('lib_top')
<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Round"/>

    <link rel="icon" type="image/png" href="https://cursor.style/images/favicon.png" />
@endsection

@section('main')

<div class="main">   
    <div class="container">
        @include('layouts.banner')
        @include('layouts.how_to_content')
    </div>
</div>

@include('layouts.install')
@endsection


@section('lib_bottom')
    <script src="https://cursor.style/js/pagination.js{{ build_version() }}"></script>
    <script src="https://cursor.style/js/main.js{{ build_version() }}"></script>
@endsection