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

    <link rel="stylesheet" href="{{ secure_asset('css/switcher.css') }}">

    <script type="text/javascript" src="js/mycollection.js"></script>
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css">
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous" />

    <!-- FONT picker -->
    <link rel="stylesheet" href="css/fontpicker.css">
    <script src="js/fontpicker.iife.js"></script>
    <!-- END FONT picker -->


    <link rel="icon" type="image/png" href="{{ secure_asset('images/favicon.png') }}" />
<<<<<<< HEAD
<link rel="stylesheet" href="{{ secure_asset('fonts/fonts.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/libscss.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/main.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/ie.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/loader.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/mycollection.css') }}"/>
   
    
    
=======
>>>>>>> 8f96918 (🔄 Update site to new version)
@endsection

@section('main')


    <div class="mycollection-wrapper">
        <div class="left">
            
        </div>


        <div class="mycollection" id="mycollection">

<<<<<<< HEAD
=======
            <div class="chat-popup" id="chatPopup">
                <input type="text" id="usernameInput" placeholder="Enter your nickname">
                <button id="loginButton">Join Chat</button>
                <div id="loginMessage" class="system-message"></div>
            </div>
>>>>>>> 8f96918 (🔄 Update site to new version)

            <div class="my-collection-header">
                <div class="active-cursor">
                    <div class="currentcursorbox">
                        <img src="/images/nocursor.png" id="nocursor-preview">
                        <img src="/images/nopointer.png" id="nopointer-preview">
                    </div>
                </div>

                <div class="my-collection-switcher">
                    <div class="content">
                        <div class="switcher-label">Custom cursor</div>
                        <div class="switcher-switch">
                            <input type="checkbox" id="customcursor" class="mycursoronoff">
                            <label for="customcursor" class="mycursoronoff-label">
                                <span class="track">
                                    <span class="txt"></span>
                                </span>
                                <span class="thumb">|||</span>
                            </label>
                        </div>
                    </div>

                    <div class="content cursos-assistance">
                        <div class="switcher-label">Cursor assistance</div>
                        <div class="switcher-switch">
                            <input type="checkbox" id="cursorassistance" class="mycursoronoff">
                            <label for="cursorassistance" class="mycursoronoff-label">
                                <span class="track">
                                    <span class="txt"></span>
                                </span>
                                <span class="thumb">|||</span>
                            </label>
                        </div>
                    </div>

                    <div class="content">
                        <div class="switcher-label line-effect">Cursor Effects
                        </div>
                        <div class="switcher-switch">
                            <input type="checkbox" id="cursoreffect" class="mycursoronoff">
                            <label for="cursoreffect" class="mycursoronoff-label">
                                <span class="track">
                                    <span class="txt"></span>
                                </span>
                                <span class="thumb">|||</span>
                            </label>
                        </div>
                    </div>

                </div>


            </div>

            <div id="iconInput"></div>

            <div class="params">
                <div class="slider-container">
                    <h2>Cursor size</h2>

                    <input type="range" min="16" max="128" value="50" class="slider-1" id="slider1">
                    <div class="value-display">Curent size: <span id="value1">72</span></div>
                </div>
            </div>


            <div class="effects-container" id="effects-container">
                <h2 class="default_cursor_cs">Trail effect</h2>
            </div>


            <div id="mycursors">

            </div>

        </div>
        <div class="right">
            
        </div>



    </div>



@endsection


@section('lib_bottom')
    <script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js"></script>
    <script src="tabler/tablerClass.js"></script>
@endsection