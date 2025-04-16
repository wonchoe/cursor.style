@extends('layouts.app')

@section('title')
    @lang('feedback.title')
@endsection

@section('lib_top')

    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Round" />
    <link rel="stylesheet" href="{{ secure_asset('css/hover-min.css') }}" />
    <link rel="stylesheet" href="{{ secure_asset('fonts/fonts.css') }}" />
    <link rel="stylesheet" href="{{ secure_asset('css/libscss.css') }}" />
    <link rel="stylesheet" href="{{ secure_asset('css/main.css') }}" />
    <link rel="stylesheet" href="{{ secure_asset('css/ie.css') }}" />
    <link rel="stylesheet" href="{{ secure_asset('css/loader.css') }}" />
    <link rel="stylesheet" href="{{ secure_asset('css/contact/main.css') }}" />
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
            </div>

            <div class="contact1">
                <div class="feedback_img"><img style="border-radius: 4px;" src="@lang('feedback.img_1')"></div>
                <div class="container-contact1" id="thanks_form" style="display:none">
                    <p class="message_fback">@lang('feedback.thanks_for_feedback')</p>
                </div>

                <div class="container-contact1" id="contact_form">
                    <form id="feedback" name="feedback" class="contact1-form validate-form" style="width: 100%;">
                        <span class="contact1-form-title" style="font-size:20px">
                            @lang('feedback.text_1')
                        </span>
                        <span class="contact1-form-title" style="font-size:20px; margin-top: 10px;">
                            @lang('feedback.text_2')
                        </span>
                        <div class="wrap-input1 validate-input" data-validate="Message is required">
                            <textarea class="input1 textarea_f" name="message" id="message"
                                placeholder="@lang('feedback.i_didnt_like')"></textarea>
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


        </div>
    </div>



@endsection


@section('lib_bottom')
    <script src="{{ secure_asset('/js/contact/feedback.js') }}"></script>
    <script src="{{ secure_asset('/js/main.js') }}"></script>

@endsection