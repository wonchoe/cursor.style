@extends('layouts.app')

@section('title')
    @lang('feedback.title')
@endsection

@section('descr')
    @lang('feedback.descr')
@endsection

@section('lib_top')
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Round" />
    <link rel="icon" type="image/png" href="{{ secure_asset('images/favicon.png') }}" />
@endsection

@section('main')

<style>
    .contact1-form-title{
        font-size: 23px;    
        color: #4f4f4f;
    }
    .container-contact1{
        justify-content: center;
    }
    .message_fback{
        display: flex
;
    /* align-content: center; */
    justify-content: center;
    align-items: center;
    }
</style>

<script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>


    <div class="main">
        <div class="container">

            <div class="contact1">
 


                <div class="container-contact1" id="contact_form">

                <div class="feedback_img">
                    <dotlottie-player
                    src="https://lottie.host/cde90728-75d0-4b7d-a1c3-2ec972f0ebc0/a7JmsmRIAt.lottie"
                    background="transparent"
                    speed="1"
                    style="width: 300px; height: 300px"
                    loop
                    autoplay
                    ></dotlottie-player>
                </div>

                    @if (session('success'))
                                <p class="message_fback">@lang('feedback.thanks_contact')</p>
                    @else
                        <form method="POST" id="feedback" name="feedback" class="contact1-form validate-form"
                            style="width: 100%;">
                            <span class="contact1-form-title" style="font-size:20px">
                                @lang('feedback.text_1')
                            </span>
                            <span>
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
                    @endif

                  

                </div>

                <div class="gads-wrapper infeed" style="width:100%">
                        <div class="googleads" style="width:100%">
                            <!-- google ads here -->
                            @include('other.google.infeed')
                            <!-- google ads here -->
                        </div>
                    </div>  

            </div>
        </div>

    </div>


@endsection


@section('lib_bottom')
    <script src="{{ secure_asset('/js/main.js') }}"></script>
@endsection