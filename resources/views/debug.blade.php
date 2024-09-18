@extends('layouts.app')

@section('title')
DEBUG PAGE
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
        @if (isset($success))
        <div class="contact1">
            <div class="container-contact1" id="thanks_form">
		{{ $app_locale }}
            </div>
        </div>
        @endif
        @if (!isset($success))
        <div class="contact1">
            <div class="container-contact1" id="thanks_form" style="display:none">
                <p class="message_fback">@lang('feedback.thanks_contact')</p>
            </div>
            <div class="container-contact1" id="contact_form">
                <div class="contact1-pic js-tilt" data-tilt>
                    <img src="/images/img-01.png" alt="contact form">
                </div>

                <form id="feedback" name="feedback" class="contact1-form validate-form" method="POST">
                    @csrf
                    <span class="contact1-form-title">
                        @lang('feedback.feedback')
                    </span>

                    <div class="wrap-input1 validate-input" data-validate = "Name is required">
                        <input class="input1" type="text" name="name" id="name" placeholder="@lang('feedback.name')">
                        <span class="shadow-input1"></span>
                    </div>

                    <div class="wrap-input1 validate-input" data-validate = "Valid email is required: ex@abc.xyz">
                        <input class="input1" type="text" name="email" id="email" placeholder="Email">
                        <span class="shadow-input1"></span>
                    </div>

                    <div class="wrap-input1 validate-input" data-validate = "Subject is required">
                        <input class="input1" type="text" name="subject" id="subject" placeholder="@lang('feedback.subject')">
                        <span class="shadow-input1"></span>
                    </div>

                    <div class="wrap-input1 validate-input" data-validate = "Message is required">
                        <textarea class="input1 textarea_f" name="message" id="message" placeholder="@lang('feedback.message')"></textarea>
                        <span class="shadow-input1"></span>
                    </div>

                    <div class="container-contact1-form-btn">
                        <div>
                            <button type="submit" class="contact1-form-btn">
                                <span>
                                    @lang('feedback.send_button')
                                    <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
                                </span>
                            </button>                        
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>

</div>


@endsection


@section('lib_bottom')
<script src="{{ secure_asset('/js/contact/contact.js') }}"></script>    
<script src="{{ secure_asset('/js/main.js') }}"></script>    

<script src="{{ secure_asset('/js/banner_cursor.js') }}"></script>  
@endsection
