@extends('layouts.app')

@section('title')
@lang('feedback.title')
@endsection

@section('descr')
@lang('feedback.descr')
@endsection

@section('lib_top')
<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Round"/>
<link rel="stylesheet" href="{{ secure_asset('css/hover-min.css') }}"/>        
<link rel="stylesheet" href="{{ secure_asset('fonts/fonts.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/libscss.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/main.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/ie.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/loader.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/contact/main.css') }}"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="{{ secure_asset('/js/cat/preload.js') }}"></script>    
<link rel="icon" type="image/png" href="{{ secure_asset('images/favicon.png') }}"/>

{!! htmlScriptTagJsApi([]) !!}


<!--
<script type="text/javascript">
recap = document.createElement('script');
recap.src = 'https://www.google.com/recaptcha/api.js?hl=' + document.documentElement.lang;
recap.async = '';
recap.defer = '';
document.head.appendChild(recap);
</script>-->
@endsection

@section('main')
<div class="main">   
    <div class="container">


<div id="taboola-mid-article-thumbnails"></div>


        <div class="doc">
		<img src="/images/update/update.svg"  style="width: 100%; margin-bottom: 5px;padding-left: 25%;padding-right: 25%;">
            <div  style="display: table;max-width: 1100px;table-layout: fixed;border-spacing: 15px;">
			<div class="container-docs" style="padding: 20px;display: table-cell;">
            <h2>More Secure</h2> 
			<img src="/images/update/secure.png"  style="width: 100%; margin-bottom: 10px;">
				<p style="margin-bottom: 0px;">All security protocols have been updated, the application meets the latest Google security requirements!</p>
            </div>
			
			<div class="container-docs" style="padding: 20px;display: table-cell;">
            <h2>Clear view at any size</h2> 
			<img src="/images/update/clear_view.png"  style="width: 100%; margin-bottom: 21px;">
				<p style="margin-bottom: 0px;">All cursors are converted to vector format, which guarantees perfect display quality on any screen!</p>
            </div>
			
			<div class="container-docs" style="padding: 20px;display: table-cell;">
            <h2>New collections</h2> 
			<img src="/images/update/collections.png"  style="width: 100%; margin-bottom: 10px;">
				<p style="margin-bottom: 0px;">New trending collections of cursors so you can customize your workspace to your taste! </p>
            </div>
			</div>
        </div>
			<a class="success__more__btn hvr-shutter-out-horizontal" href="/" style="margin-bottom: 100px;max-width: 350px;left: calc(50% - 175px);margin-top: 15px;">Go to catalogue</a>
    </div>
    
</div>


@endsection


@section('lib_bottom')
<script src="{{ secure_asset('/js/contact/tilt.jquery.min.js') }}"></script>    
<script src="{{ secure_asset('/js/contact/contact.js') }}"></script>    
<script src="{{ secure_asset('/js/main.js') }}"></script>    

<script src="{{ secure_asset('/js/banner_cursor.js') }}"></script>  
@endsection