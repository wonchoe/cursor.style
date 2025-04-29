@extends('layouts.app')


@section('title')
404 - @lang('errors.e404_1')
@endsection

@section('lib_top')
<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Round">
       
<link rel="stylesheet" href="{{ secure_asset('fonts/fonts.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/libscss.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/main_updated.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/ie.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/404.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/loader.css') }}"/>


<script src="{{ secure_asset('/js/anim_preload.js')}}"></script>    
<script src="{{ secure_asset('./js/preload.js')}}"></script>    
<link rel="icon" type="image/png" href="{{ secure_asset('images/favicon.png') }}"/>
<script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
@endsection


@section('main')

   
@include('layouts.modal')

<div class="main">   
    <div class="container">
        <div class="n404">
			<div id="notfound">
				<div class="notfound">
					<div class="notfound-404">
						<dotlottie-player
						src="https://lottie.host/98090ebd-6c71-4bf3-95d4-749748a41adb/6z9IUjpwfP.lottie"
						background="transparent"
						speed="1"
						style="width: 300px; height: 300px"
						loop
						autoplay
						></dotlottie-player>
					</div>
					<h2>@lang('errors.e404_2')</h2>
					<p>@lang('errors.e404_3')</p>
					<a href="/">@lang('errors.e404_4')</a>
				</div>
			</div>        
        </div>
    </div>
</div>



@endsection


@section('lib_bottom')
<script src="{{ secure_asset('/js/contact/tilt.jquery.min.js') }}"></script>  
<script src="{{ secure_asset('/js/main.js') }}"></script>    
<script src="{{ secure_asset('/js/menu.js') }}"></script>    
<script src="{{ secure_asset('/js/banner_cursor.js') }}"></script>  
<script src="{{ secure_asset('/js/installed.js') }}"></script>  
<script src="{{ secure_asset('/js/test_area_prev.js')}}"></script>    
@endsection