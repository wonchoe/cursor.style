@extends('layouts.app')


@section('title')
404 - @lang('errors.e404_1')
@endsection

@section('lib_top')
<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Round">
       
<link rel="stylesheet" href="{{ secure_asset('fonts/fonts.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/libscss.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/main.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/ie.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/404.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/loader.css') }}"/>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

<!-- jQuery Modal -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />

<script src="{{ secure_asset('/js/anim_preload.js')}}"></script>    
<script src="{{ secure_asset('./js/preload.js')}}"></script>    
<link rel="icon" type="image/png" href="{{ secure_asset('images/favicon.png') }}"/>
@endsection


@section('main')

   
@include('layouts.modal')
<div class="main">   
    <div class="container">



        
        <div class="n404">
	<div id="notfound">
		<div class="notfound">
			<div class="notfound-404">
				<h1>4<span></span>4</h1>
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