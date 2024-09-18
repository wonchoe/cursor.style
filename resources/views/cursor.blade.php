@extends('layouts.app')

@section('title')
Cursor {{$cursor->name_en}}
@endsection

@section('descr')
{{$cursor->name_en}}
@endsection

@section('lib_top')
<meta property="og:image:width" content="700" />
<meta property="og:image:height" content="350" />

<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Round"/>
<link rel="stylesheet" href="{{ secure_asset('css/hover-min.css') }}"/>        
<link rel="stylesheet" href="{{ secure_asset('fonts/fonts.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/libscss.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/main.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/ie.css') }}"/>
<link rel="stylesheet" href="{{ secure_asset('css/loader.css') }}"/>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.js"></script>
<!-- jQuery Modal -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.2/jquery.modal.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />
<script src="{{ secure_asset('/js/cat/preload.js') }}"></script>    
<link rel="icon" type="image/png" href="{{ secure_asset('images/favicon.png') }}"/>
@endsection

@section('main')

<div class="main">   
    <div class="container">

        @include('layouts.testzone')


        @include('layouts.banner')

                
            <div class="main__list_cursor">

                <div class="main__item">
                    @if ($id_prev)
                    <div onclick='location.href="/details/{{$id_prev[0]}}-{{$id_prev[1]}}"' class="left">Prev</div>
                    @endif
                    @if ($id_next)
                    <div onclick='location.href="/details/{{$id_next[0]}}-{{$id_next[1]}}"' class="right">Next</div>    
                    @endif
                    

                
                    <div class="main__btns">
                        <button onClick="addToBtn(this)" data-type="stat" data-cataltname="{{ $cursor->categories->alt_name }}" data-catbasename_en="{{ $cursor->categories->base_name_en }}"  data-catbasename_es="{{ $cursor->categories->base_name_es }}"  data-catbasename="{{ $cursor->categories->base_name }}" data-cat="{{ $cursor->cat }}" data-id="{{ $cursor->id }}" data-name="@lang('cursors.c_'.$cursor->id)" data-offset-x="{{ $cursor->offsetX }}" data-offset-x_p="{{ $cursor->offsetX_p }}" data-offset-y="{{ $cursor->offsetY }}" data-offset-y_p="{{ $cursor->offsetY_p }}" data-c_file="/cursors/{{ $cursor->id.'-'.$cursor->name_s }}-cursor.svg" data-p_file="/pointers/{{ $cursor->id.'-'.$cursor->name_s }}-pointer.svg" class="hvr-shutter-out-horizontal-g newbtn">Add</a>               
                    </div>                    
                    <div class="div_ar_p">
                        <p>@lang('cursors.c_'.$cursor->id) </p>
                    </div>
                    <div class="main__item-img cs_pointer" data-cur-id="{{ $cursor->id }}" cursorshover="true">
                        <img class="cursorimg" style="cursor: url(/cursors/{{ $cursor->id.'-'.$cursor->name_s }}-cursor.svg) 0 0, auto !important;" src="/cursors/{{ $cursor->id.'-'.$cursor->name_s }}-cursor.svg">
                        <img class="cursorimg" style="cursor: url(/pointers/{{ $cursor->id.'-'.$cursor->name_s }}-pointer.svg) 0 0, auto !important;"src="/pointers/{{ $cursor->id.'-'.$cursor->name_s }}-pointer.svg">
                    </div>
                </div>                        
               
            </div>

        
            <div class="main__list_near">                        
                @forelse ($all_cursors as $key => $cursor)                
                <div onclick='location.href="/details/{{$cursor->id}}-{{$cursor->name_s}}"' class="main__item" @if ($key == 0) style="opacity: 0.5;" @endif @if ($key == 2) style="opacity: 0.5;" @endif>
                    <div class="div_ar_p">
                        <p>@lang('cursors.c_'.$cursor->id) </p>
                    </div>
                    <div class="main__item-img cs_pointer" data-cur-id="{{ $cursor->id }}" cursorshover="true">
                        <img src="/cursors/{{ $cursor->id.'-'.$cursor->name_s }}-cursor.svg">
                        <img src="/pointers/{{ $cursor->id.'-'.$cursor->name_s }}-pointer.svg">
                    </div>
                </div>   
                @empty
                @include('other.nocursors')
                @endforelse                     
            </div>        


    </div>
</div>        




@include('layouts.install')

@endsection


@section('lib_bottom')
<script src="{{ secure_asset('/js/main.js') }}"></script>    
<script src="{{ secure_asset('/js/banner_cursor.js') }}"></script>  
<script src="{{ secure_asset('/js/installed.js') }}"></script>  
<script src="{{ secure_asset('/js/test_area_prev.js')}}"></script>    
@endsection