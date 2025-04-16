@extends('admin.layouts.app')

@section('title', 'Code Editor')

@section('src_top')
<link rel='stylesheet' href="{{ secure_asset('css/cursors.css') }}">
<link rel='stylesheet' href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.6.2/css/bootstrap-slider.min.css">

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>


@endsection

@section('cursor_section')
<div id="test_cursor" class="test_cursor">
    <img id="cursor_img" src="javascript:"></img>
</div>
@endsection

@section('content')

<div id="loaderNew" class="loader"><img class="loaderImg" src="{{ secure_asset('images/loader.svg') }}"></img></div>
<!-- Modal -->




<div class="container-fluid">

    <form id="upload_form" name="upload_form" method="POST" enctype="multipart/form-data" files="true">
        <input type="hidden" id="oX" name="oX" value="0">
        <input type="hidden" id="oY" name="oY" value="0">
        <input type="hidden" id="oXp" name="oXp" value="0">
        <input type="hidden" id="oYp" name="oYp" value="0">    
        
        <div class="row" id='pic_row'>
            <div class="col-12 text_align_center paddingr0">
                <input id="c_name" name="c_name" class="form-control" type="text" placeholder="Название курсора">
            </div>            

            <div class="row">
            <div class='col-6'>

                {{ csrf_field() }}
                <div id="uploadContainer">            
                    <div class="row uploadArea">
                        <div class="picContainer">
                            <div class="cursor-upload">
                                <div class="cursor-edit">
                                    <input type='file' id="cursorUpload" name="cursorUpload" accept=".svg, .png" />
                                    <label for="cursorUpload"></label>
                                </div>
                                <div class="cursor-preview" id="cursor_data">
                                    <div id="imagePreviewCursor">
                                    </div>
                                </div>
                            </div>

                            <div class="pointer-upload">
                                <div class="pointer-edit">
                                    <input type='file' id="pointerUpload" name="pointerUpload" accept=".svg, .png" />
                                    <label for="pointerUpload"></label>
                                </div>
                                <div class="pointer-preview" id="pointer_data">
                                    <div id="imagePreviewPointer">
                                    </div>
                                </div>
                            </div>                
                        </div>
                    </div>
                </div>
                
                <div id="uploadContainer">            
                    <div class="row uploadArea">
                        <div class="picContainer">
                            <div class="cursor-upload">
                                <div class="cursor-edit">
                                    <input type='file' id="cursorUpload_prev" name="cursorUpload_prev" accept=".svg, .png" />
                                    <label for="cursorUpload_prev"></label>
                                </div>
                                <div class="cursor-preview" id="cursor_data_prev">
                                    <div id="imagePreviewCursor_prev">
                                    </div>
                                </div>
                            </div>

                            <div class="pointer-upload">
                                <div class="pointer-edit">
                                    <input type='file' id="pointerUpload_prev" name="pointerUpload_prev" accept=".svg, .png" />
                                    <label for="pointerUpload_prev"></label>
                                </div>
                                <div class="pointer-preview" id="pointer_data_prev">
                                    <div id="imagePreviewPointer_prev">
                                    </div>
                                </div>
                            </div>                
                        </div>
                    </div>
                </div>                 

            </div>

            <div class='col-6'>
                <div class="row uploadArea" id="test_box">
                    <div class="col-12">
                        <div class="cur-options">
                            <!--                            <label for="cursorW">Cursor sizer</label><br/>
                                                        <input id="cursorW" type="text"/><br/>-->

                            <label for="offsetX">Offset X</label><br/>         
                            <input id="offsetX" name="offsetX" type="text"/><br/>

                            <label for="offsetY">Offset Y</label><br/>
                            <input id="offsetY" name="offsetY" type="text"/>
                        </div>
                    </div>
                </div>
            </div>
                </div>



            <div class="col-12 text_align_center">
                <button type="submit" class="btn btn-primary btn_save_cursor">Сохранить</button>
            </div>

            <!-- CURSORS LIST-->                        
            <div class="col-12 paddingr0" id="cursors_container">

            </div>

        </div>
    </form>
</div>
@endsection

@section('src_bottom')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.6.2/bootstrap-slider.min.js"></script> 
<script src="{{ secure_asset('js/animated/cursors.js') }}"></script> 
<script src="{{ secure_asset('js/animated/upload.js') }}"></script> 
<script src="{{ secure_asset('js/animated/show_cursors.js') }}"></script> 
@endsection