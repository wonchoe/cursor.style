@extends('layouts.appbg')

@section('title')
Обратная связь
@endsection

@section('lib_top')
<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Round">
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

@endsection

@section('main')
<div class="main">   
    <div class="container">
        <div class="contact1">
            <div class="feedback_img"><img style="border-radius: 4px;" src="https://www.x-cart.ru/sites/default/files/blog/image/%D0%9E%D1%82%D0%B7%D1%8B%D0%B2%D1%8B_%D0%B2_%D0%B8%D0%BD%D1%82%D0%B5%D1%80%D0%BD%D0%B5%D1%82_%D0%BC%D0%B0%D0%B3%D0%B0%D0%B7%D0%B8%D0%BD%D0%B5_1520px.jpg"></div>
            <div class="container-contact1" id="thanks_form" style="display:none">
                <p class="message_fback">Спасибо за ваш отзыв! Ваше мнение очень важно для нас.</p>
            </div>
            <div class="container-contact1" id="contact_form">
                <form id="feedback" name="feedback" class="contact1-form validate-form" style="width: 100%;">
                    <span class="contact1-form-title" style="font-size:20px">
                        Спасибо за установку нашего продукта!
                    </span>
                    <span class="contact1-form-title" style="font-size:20px; margin-top: 10px;">
                        Если не сложно, оставьте отзыв о работе расширения Обои вконтакте чтобы мы могли улучшить его!
                    </span>
                    <div class="wrap-input1 validate-input" data-validate="Message is required">
                        <textarea class="input1 textarea_f" name="message" id="message" placeholder="Что я думаю о расширении Обои вконтакте...."></textarea>
                        <span class="shadow-input1"></span>
                    </div>

                    <div class="container-contact1-form-btn">
                        <div>
                            <button type="submit" class="contact1-form-btn">
                                <span>
                                    Отправить
                                    <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
                                </span>
                            </button>                        
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>



@endsection


@section('lib_bottom')
<script src="{{ secure_asset('/js/contact/tilt.jquery.min.js') }}"></script>    
<script src="{{ secure_asset('/js/contact/feedback.js') }}"></script>    
<script src="{{ secure_asset('/js/main.js') }}"></script>    

<script src="{{ secure_asset('/js/banner_cursor.js') }}"></script>  
@endsection