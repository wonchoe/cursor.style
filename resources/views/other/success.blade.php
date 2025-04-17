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
            <div class="gads-wrapper infeed" style="width:100%">
                
                <div class="googleads" style="width:100%">
                    <!-- google ads here -->
                    @include('other.google.infeed')
                    <!-- google ads here -->
                </div>

                @include('other.lang.' . app()->getLocale() . '.success')


            </div>

        </div>
    </div>


@endsection



@section('lib_bottom')

@endsection