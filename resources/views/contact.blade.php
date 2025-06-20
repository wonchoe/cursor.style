@extends('layouts.app')

@section('head_meta')
    <title>@lang('feedback.title')</title>
    <meta name="description" content="@lang('feedback.descr')" />
@endsection

@push('styles')
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Round" />
    <link rel="icon" type="image/png" href="{{ asset_cdn('images/favicon.png') }}" />
@endpush

@section('main')
    <div class="main">
        <div class="container">

            <div class="gads-wrapper infeed" style="width:100%">
                <div class="googleads" style="width:100%">
                    @include('ads.google.infeed')
                </div>
            </div>

            @if (isset($success))
                <div class="contact1">
                    <div class="container-contact1" id="thanks_form">
                        <p class="message_fback">@lang('feedback.thanks_contact')</p>
                    </div>
                </div>
            @else
                <div class="contact1">
                    <div class="container-contact1" id="thanks_form" style="display:none">
                        <p class="message_fback">@lang('feedback.thanks_contact')</p>
                    </div>
                    <div class="container-contact1" id="contact_form">
                        <div class="contact1-pic js-tilt" data-tilt>
                            <img src="{{ asset_cdn('/images/img-01.png') }}" alt="contact form">
                        </div>

                        <form id="feedback" name="feedback" class="contact1-form validate-form" method="POST">
                            @csrf
                            <span class="contact1-form-title">
                                @lang('feedback.feedback')
                            </span>

                            <div class="wrap-input1 validate-input" data-validate="Name is required">
                                <input class="input1" type="text" name="name" id="name" placeholder="@lang('feedback.name')">
                                <span class="shadow-input1"></span>
                            </div>

                            <div class="wrap-input1 validate-input" data-validate="Valid email is required: ex@abc.xyz">
                                <input class="input1" type="text" name="email" id="email" placeholder="@lang('feedback.email')">
                                <span class="shadow-input1"></span>
                            </div>

                            <div class="wrap-input1 validate-input" data-validate="Subject is required">
                                <input class="input1" type="text" name="subject" id="subject" placeholder="@lang('feedback.subject')">
                                <span class="shadow-input1"></span>
                            </div>

                            <div class="wrap-input1 validate-input" data-validate="Message is required">
                                <textarea class="input1 textarea_f" name="message" id="message" placeholder="@lang('feedback.message')"></textarea>
                                <span class="shadow-input1"></span>
                            </div>

                            <div class="container-contact1-form-btn">
                                <button type="submit" class="contact1-form-btn">
                                    <span>
                                        @lang('feedback.send_button')
                                        <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset_ver('js/pagination.js') }}"></script>
    <script src="{{ asset_ver('js/main.js') }}"></script>
@endpush
